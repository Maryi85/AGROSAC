<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Models\Loan;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        // Verificar que el usuario tiene el rol correcto
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'foreman'])) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }
        $query = Loan::with(['tool', 'user', 'approvedBy', 'returnedBy']);

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

        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.loans.index', compact('loans', 'tools', 'workers', 'statuses'))->with('layout', $layout);
    }

    // Los métodos create y store han sido eliminados
    // El administrador solo aprueba/rechaza solicitudes de trabajadores

    public function show(Loan $loan): View
    {
        $loan->load(['tool', 'user']);
        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.loans.show', compact('loan'))->with('layout', $layout);
    }

    public function edit(Loan $loan): View
    {
        $loan->load(['tool', 'user']);
        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.loans.edit', compact('loan'))->with('layout', $layout);
    }

    public function update(UpdateLoanRequest $request, Loan $loan): RedirectResponse
    {
        $loan->update($request->validated());

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Préstamo actualizado correctamente');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        // Si el préstamo está activo, devolver la cantidad a la herramienta
        if ($loan->status === 'out') {
            $loan->tool->incrementAvailableQty($loan->quantity);
        }

        $loan->delete();

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Préstamo eliminado correctamente');
    }

    public function return(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo ya ha sido devuelto.');
        }

        // Marcar como devuelto
        $loan->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        // Devolver la cantidad a la herramienta
        $loan->tool->incrementAvailableQty($loan->quantity);

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Herramienta devuelta correctamente');
    }

    public function markAsLost(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo ya ha sido procesado.');
        }

        DB::beginTransaction();
        try {
            // Marcar como perdido
            $loan->update([
                'status' => 'lost',
                'returned_at' => now(),
            ]);

            // Marcar como perdida en el inventario
            $loan->tool->markAsLost($loan->quantity);

            DB::commit();

            return redirect()->route(route_prefix() . 'loans.index')
                ->with('status', 'Herramienta marcada como perdida');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al marcar como perdida: ' . $e->getMessage()]);
        }
    }

    public function markAsDamaged(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo ya ha sido procesado.');
        }

        DB::beginTransaction();
        try {
            // Marcar como dañado
            $loan->update([
                'status' => 'damaged',
                'returned_at' => now(),
            ]);

            // Marcar como dañada en el inventario
            $loan->tool->markAsDamaged($loan->quantity);

            DB::commit();

            return redirect()->route(route_prefix() . 'loans.index')
                ->with('status', 'Herramienta marcada como dañada');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al marcar como dañada: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve a loan request
     */
    public function approve(Request $request, Loan $loan): RedirectResponse
    {
        if (!$loan->isPending()) {
            return redirect()->route(route_prefix() . 'loans.index')
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

            // Actualizar la cantidad disponible de la herramienta
            $tool->decrementAvailableQty($loan->quantity);

            // Marcar como 'out' y establecer out_at
            $loan->update([
                'status' => 'out',
                'out_at' => now(),
            ]);

            DB::commit();

            return redirect()->route(route_prefix() . 'loans.index')
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
            return redirect()->route(route_prefix() . 'loans.index')
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

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Préstamo rechazado correctamente');
    }

    /**
     * Confirm return of a tool
     */
    public function confirmReturn(Request $request, Loan $loan): RedirectResponse
    {
        if (!$loan->isReturnedByWorker()) {
            return redirect()->route(route_prefix() . 'loans.index')
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
            ]);

            // Devolver la cantidad a la herramienta (si no estaba ya devuelta)
            $tool = $loan->tool;
            $tool->incrementAvailableQty($loan->quantity);

            DB::commit();

            return redirect()->route(route_prefix() . 'loans.index')
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
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo no está aprobado.');
        }

        $loan->update([
            'status' => 'out',
            'out_at' => now(),
        ]);

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Préstamo procesado correctamente');
    }

    public function downloadPdf(Request $request)
    {
        $query = Loan::with(['tool', 'user', 'approvedBy', 'returnedBy']);

        // Aplicar los mismos filtros que en index
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
            'pending' => 'Pendiente',
            'approved' => 'Aprobado',
            'rejected' => 'Rechazado',
            'out' => 'Prestado',
            'returned_by_worker' => 'Devuelto por Trabajador',
            'returned' => 'Devuelto y Confirmado',
            'lost' => 'Perdido',
            'damaged' => 'Dañado',
        ];

        $pdf = Pdf::loadView('admin.loans.pdf', compact('loans', 'statuses'));
        return $pdf->download('prestamos-' . now()->format('Y-m-d') . '.pdf');
    }
}