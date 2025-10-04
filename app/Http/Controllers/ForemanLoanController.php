<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

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
        $tools = Tool::where('available_qty', '>', 0)->orderBy('name')->get();

        // Obtener trabajadores para el filtro
        $workers = User::where('role', 'worker')->orderBy('name')->get();

        // Estados disponibles
        $statuses = [
            'out' => 'Prestado',
            'returned' => 'Devuelto',
            'lost' => 'Perdido',
            'damaged' => 'Dañado',
        ];

        return view('foreman.loans.index', compact('loans', 'tools', 'workers', 'statuses'));
    }

    public function create(): View
    {
        // Obtener herramientas disponibles
        $tools = Tool::where('available_qty', '>', 0)
                    ->where('status', 'operational')
                    ->orderBy('name')
                    ->get();

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
        $tool->decrement('available_qty', $request->quantity);

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
        $loan->tool->increment('available_qty', $loan->quantity);

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

        // No devolver la cantidad a la herramienta (está perdida)
        // Decrementar la cantidad total
        $loan->tool->decrement('total_qty', $loan->quantity);

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

        // Devolver la cantidad a la herramienta pero marcar como dañada
        $loan->tool->increment('available_qty', $loan->quantity);
        $loan->tool->update(['status' => 'damaged']);

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

    public function destroy(Loan $loan): RedirectResponse|JsonResponse
    {
        // Si el préstamo está activo, devolver la cantidad a la herramienta
        if ($loan->status === 'out') {
            $loan->tool->increment('available_qty', $loan->quantity);
        }

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
}
