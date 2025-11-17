<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ForemanLoanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Loan::with(['tool', 'user']);

        // Filtro por estado
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filtro por herramienta
        if ($request->filled('tool_id') && $request->tool_id !== 'all') {
            $query->where('tool_id', $request->tool_id);
        }

        // Filtro por trabajador
        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(10);

        // Obtener herramientas disponibles para el filtro
        // Usar el accessor available_qty que calcula desde tool_entries
        $tools = Tool::with('entries')->get()->filter(function($tool) {
            return $tool->available_qty > 0;
        })->sortBy('name')->values();

        // Obtener trabajadores para el filtro
        $workers = User::where('role', 'worker')->orderBy('name')->get();

        // Estados disponibles
        $statuses = [
            'pending' => 'Pendiente',
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            'out' => 'Prestado',
            'returned_by_worker' => 'Devuelto por Trabajador',
            'returned' => 'Devuelto y Confirmado',
            'lost' => 'Perdido',
            'damaged' => 'Dañado',
        ];

        return view('foreman.loans.index', compact('loans', 'tools', 'workers', 'statuses'));
    }

    public function create(): View
    {
        // Obtener herramientas disponibles
        // Usar el accessor available_qty que calcula desde tool_entries
        $tools = Tool::with('entries')
                    ->where('status', 'operational')
                    ->get()
                    ->filter(function($tool) {
                        return $tool->available_qty > 0;
                    })
                    ->sortBy('name')
                    ->values();

        // Obtener trabajadores
        $workers = User::where('role', 'worker')->orderBy('name')->get();

        return view('foreman.loans.create', compact('tools', 'workers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'user_id' => 'required|exists:users,id',
            'quantity' => 'required|integer|min:1',
            'due_at' => 'nullable|date|after:today',
        ]);

        $tool = Tool::findOrFail($request->tool_id);
        
        // Verificar disponibilidad
        if ($tool->available_qty < $request->quantity) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'No hay suficientes unidades disponibles de esta herramienta.');
        }

        // Crear el préstamo
        $loan = Loan::create([
            'tool_id' => $request->tool_id,
            'user_id' => $request->user_id,
            'quantity' => $request->quantity,
            'out_at' => now(),
            'due_at' => $request->due_at ? Carbon::parse($request->due_at) : null,
            'status' => 'out',
        ]);

        // Actualizar la cantidad disponible de la herramienta
        $tool->decrementAvailableQty($request->quantity);
        return redirect()->route('foreman.loans.index')
            ->with('status', 'Herramienta prestada correctamente');
    }

    public function show(Loan $loan): View|JsonResponse
    {
        $loan->load(['tool', 'user']);
        
        // Si es una petición AJAX, devolver JSON
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'loan' => [
                    'id' => $loan->id,
                    'tool_name' => $loan->tool->name,
                    'tool_category' => $loan->tool->category,
                    'worker_name' => $loan->user->name,
                    'worker_email' => $loan->user->email,
                    'quantity' => $loan->quantity,
                    'out_at' => $loan->out_at->format('d/m/Y H:i'),
                    'due_at' => $loan->due_at ? $loan->due_at->format('d/m/Y') : 'Sin fecha límite',
                    'returned_at' => $loan->returned_at ? $loan->returned_at->format('d/m/Y H:i') : 'No devuelto',
                    'status' => $loan->status,
                    'condition' => $loan->condition_return ?? 'Sin observaciones',
                ]
            ]);
        }
        
        return view('foreman.loans.show', compact('loan'));
    }

    public function return(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if ($loan->status !== 'out') {
            $errorMessage = 'Este préstamo ya ha sido procesado.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return redirect()->route('foreman.loans.index')
                ->with('error', $errorMessage);
        }

        // Marcar como devuelto
        $loan->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        // Devolver la cantidad a la herramienta
        $loan->tool->incrementAvailableQty($loan->quantity);
        $message = 'Herramienta devuelta correctamente';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'loan' => [
                    'id' => $loan->id,
                    'status' => $loan->status,
                    'returned_at' => $loan->returned_at->format('d/m/Y H:i')
                ]
            ]);
        }

        return redirect()->route('foreman.loans.index')
            ->with('status', $message);
    }

    public function markAsLost(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if ($loan->status !== 'out') {
            $errorMessage = 'Este préstamo ya ha sido procesado.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return redirect()->route('foreman.loans.index')
                ->with('error', $errorMessage);
        }

        // Marcar como perdido
        $loan->update([
            'status' => 'lost',
            'returned_at' => now(),
        ]);

        // Marcar como perdida en el inventario
        $loan->tool->markAsLost($loan->quantity);
        $message = 'Herramienta marcada como perdida';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'loan' => [
                    'id' => $loan->id,
                    'status' => $loan->status,
                    'returned_at' => $loan->returned_at->format('d/m/Y H:i')
                ]
            ]);
        }

        return redirect()->route('foreman.loans.index')
            ->with('status', $message);
    }

    public function markAsDamaged(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if ($loan->status !== 'out') {
            $errorMessage = 'Este préstamo ya ha sido procesado.';
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
            
            return redirect()->route('foreman.loans.index')
                ->with('error', $errorMessage);
        }

        // Marcar como dañado
        $loan->update([
            'status' => 'damaged',
            'returned_at' => now(),
        ]);

        // Marcar como dañada en el inventario
        $loan->tool->markAsDamaged($loan->quantity);
        $message = 'Herramienta marcada como dañada';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'loan' => [
                    'id' => $loan->id,
                    'status' => $loan->status,
                    'returned_at' => $loan->returned_at->format('d/m/Y H:i')
                ]
            ]);
        }

        return redirect()->route('foreman.loans.index')
            ->with('status', $message);
    }

    /**
     * Approve a loan request
     */
    public function approve(Request $request, Loan $loan): RedirectResponse
    {
        if (!$loan->isPending()) {
            return redirect()->route('foreman.loans.index')
                ->with('error', 'Este préstamo no está pendiente de aprobación.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Verificar disponibilidad antes de aprobar
            $tool = $loan->tool;
            if ($tool->available_qty < $loan->quantity) {
                return redirect()->back()
                    ->with('error', 'No hay suficientes herramientas disponibles para aprobar este préstamo.');
            }

            // Actualizar el préstamo
            $loan->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // NO decrementar el stock aquí, se hará cuando se procese el préstamo aprobado

            DB::commit();

            return redirect()->route('foreman.loans.index')
                ->with('status', 'Préstamo aprobado correctamente');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al aprobar el préstamo: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a loan request
     */
    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        if (!$loan->isPending()) {
            return redirect()->route('foreman.loans.index')
                ->with('error', 'Este préstamo no está pendiente de aprobación.');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $loan->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('foreman.loans.index')
            ->with('status', 'Préstamo rechazado correctamente');
    }

    /**
     * Confirm return of a tool
     */
    public function confirmReturn(Request $request, Loan $loan): RedirectResponse
    {
        if (!$loan->isReturnedByWorker()) {
            return redirect()->route('foreman.loans.index')
                ->with('error', 'Este préstamo no ha sido devuelto por el trabajador.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar el préstamo con las notas del administrador
            $loan->update([
                'admin_notes' => $request->admin_notes,
                'status' => 'returned', // Confirmar que está devuelto
                'returned_by' => auth()->id(),
            ]);

            // Devolver la cantidad a la herramienta (si no estaba ya devuelta)
            $tool = $loan->tool;
            $tool->incrementAvailableQty($loan->quantity);

            DB::commit();

            return redirect()->route('foreman.loans.index')
                ->with('status', 'Devolución confirmada correctamente. La herramienta ha sido devuelta al inventario.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al confirmar la devolución: ' . $e->getMessage()]);
        }
    }

    /**
     * Process approved loan (mark as out)
     */
    public function processApproved(Loan $loan): RedirectResponse
    {
        if (!$loan->isApproved()) {
            return redirect()->route('foreman.loans.index')
                ->with('error', 'Este préstamo no está aprobado.');
        }

        DB::beginTransaction();
        try {
            // Verificar disponibilidad antes de procesar
            $tool = $loan->tool;
            if ($tool->available_qty < $loan->quantity) {
                return redirect()->back()
                    ->with('error', 'No hay suficientes herramientas disponibles para procesar este préstamo.');
            }

            // Decrementar el stock
            $tool->decrementAvailableQty($loan->quantity);

            // Marcar como 'out' y establecer out_at
            $loan->update([
                'status' => 'out',
                'out_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('foreman.loans.index')
                ->with('status', 'Préstamo procesado correctamente');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al procesar el préstamo: ' . $e->getMessage()]);
        }
    }

    public function destroy(Loan $loan): RedirectResponse|JsonResponse
    {
        // Si el préstamo está activo, devolver la cantidad a la herramienta
        if ($loan->status === 'out') {
            $loan->tool->incrementAvailableQty($loan->quantity);
        }

        $loanInfo = $loan->tool->name . ' - ' . $loan->user->name;
        $loan->delete();
        $message = 'Préstamo eliminado correctamente';

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }

        return redirect()->route('foreman.loans.index')
            ->with('status', $message);
    }

    public function downloadPdf(Request $request)
    {
        $query = Loan::with(['tool', 'user']);

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('tool_id') && $request->tool_id !== 'all') {
            $query->where('tool_id', $request->tool_id);
        }

        if ($request->filled('user_id') && $request->user_id !== 'all') {
            $query->where('user_id', $request->user_id);
        }

        $loans = $query->orderBy('created_at', 'desc')->get();

        $statuses = [
            'out' => 'Prestado',
            'returned' => 'Devuelto',
            'lost' => 'Perdido',
            'damaged' => 'Dañado',
        ];

        $pdf = Pdf::loadView('foreman.loans.pdf', compact('loans', 'statuses'));
        return $pdf->download('prestamos-' . now()->format('Y-m-d') . '.pdf');
    }
}
