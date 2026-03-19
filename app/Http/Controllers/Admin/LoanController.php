<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRequest;
use App\Http\Requests\UpdateLoanRequest;
use App\Models\Loan;
use App\Models\Tool;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class LoanController extends Controller
{
    protected LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index(Request $request): View
    {
        // Verificar que el usuario tiene el rol correcto
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'foreman'])) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        $filters = [
            'status' => $request->status,
            'tool_id' => $request->tool_id,
            'user_id' => $request->user_id,
            'search' => $request->search,
        ];

        $loans = $this->loanService->getPaginatedLoans($filters);

        // Obtener herramientas disponibles para el filtro
        $tools = Tool::with('entries')->get()->filter(function ($tool) {
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

    public function show(Loan $loan): View|JsonResponse
    {
        $loan->load(['tool', 'user', 'task.plot']);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'loan' => [
                    'id'             => $loan->id,
                    'tool_name'      => $loan->tool->name,
                    'tool_category'  => $loan->tool->category,
                    'worker_name'    => $loan->user->name,
                    'worker_email'   => $loan->user->email,
                    'quantity'       => $loan->quantity,
                    'status'         => $loan->status,
                    'out_at'         => $loan->out_at?->format('d/m/Y H:i'),
                    'due_at'         => $loan->due_at?->format('d/m/Y'),
                    'returned_at'    => $loan->returned_at?->format('d/m/Y H:i'),
                    'condition_return' => $loan->condition_return,
                    'request_notes'  => $loan->request_notes,
                    'admin_notes'    => $loan->admin_notes,
                    'task_name'      => $loan->task ? $loan->task->description . ' (' . ($loan->task->plot->name ?? 'Lote General') . ')' : null,
                ],
            ]);
        }

        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.loans.show', compact('loan'))->with('layout', $layout);
    }

    public function edit(Loan $loan): View
    {
        $loan->load(['tool', 'user']);
        $layout = route_prefix() === 'foreman.' ? 'foreman.layout' : 'admin.layout';
        return view('admin.loans.edit', compact('loan'))->with('layout', $layout);
    }

    public function update(UpdateLoanRequest $request, Loan $loan): RedirectResponse|JsonResponse
    {
        $loan->update($request->validated());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Préstamo actualizado correctamente',
                'loan' => $loan->fresh(['tool', 'user'])
            ]);
        }

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Préstamo actualizado correctamente');
    }

    public function destroy(Loan $loan): RedirectResponse|JsonResponse
    {
        // Solo permitir eliminar si el estado es 'rechazado' (rejected)
        if ($loan->status !== 'rejected') {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Solo se pueden eliminar préstamos en estado rechazado.'
                ], 403);
            }
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Solo se pueden eliminar préstamos en estado rechazado.');
        }

        $id = $loan->id;
        // Si el préstamo está activo, devolver la cantidad a la herramienta
        if ($loan->status === 'out') {
            $loan->tool->incrementAvailableQty($loan->quantity);
        }

        $loan->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Préstamo eliminado correctamente',
                'id' => $id
            ]);
        }

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Préstamo eliminado correctamente');
    }

    public function return (Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo ya ha sido devuelto.');
        }

        try {
            $this->loanService->return($loan);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Herramienta devuelta correctamente',
                    'loan' => $loan->fresh(['tool', 'user'])
                ]);
            }

            return redirect()->route(route_prefix() . 'loans.index')
                ->with('status', 'Herramienta devuelta correctamente');
        }
        catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al devolver herramienta: ' . $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['error' => 'Error al devolver herramienta: ' . $e->getMessage()]);
        }
    }

    public function markAsLost(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo ya ha sido procesado.');
        }

        try {
            $this->loanService->markAsLost($loan);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Herramienta marcada como perdida',
                    'loan' => $loan->fresh(['tool', 'user'])
                ]);
            }

            return redirect()->route(route_prefix() . 'loans.index')
                ->with('status', 'Herramienta marcada como perdida');
        }
        catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al marcar como perdida: ' . $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['error' => 'Error al marcar como perdida: ' . $e->getMessage()]);
        }
    }

    public function markAsDamaged(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo ya ha sido procesado.');
        }

        try {
            $this->loanService->markAsDamaged($loan);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Herramienta marcada como dañada',
                    'loan' => $loan->fresh(['tool', 'user'])
                ]);
            }

            return redirect()->route(route_prefix() . 'loans.index')
                ->with('status', 'Herramienta marcada como dañada');
        }
        catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al marcar como dañada: ' . $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['error' => 'Error al marcar como dañada: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve a loan request
     */
    public function approve(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if (!$loan->isPending()) {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo no está pendiente de aprobación.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->loanService->approve($loan, $request->admin_notes);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Préstamo aprobado correctamente',
                    'loan' => $loan->fresh(['tool', 'user'])
                ]);
            }

            return redirect()->route(route_prefix() . 'loans.index')
                ->with('status', 'Préstamo aprobado correctamente');
        }
        catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al aprobar el préstamo: ' . $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['error' => 'Error al aprobar el préstamo: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject a loan request
     */
    public function reject(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if (!$loan->isPending()) {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo no está pendiente de aprobación.');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $this->loanService->reject($loan, $request->admin_notes);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Préstamo rechazado correctamente',
                'loan' => $loan->fresh(['tool', 'user'])
            ]);
        }

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Préstamo rechazado correctamente');
    }

    /**
     * Confirm return of a tool
     */
    public function confirmReturn(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if (!$loan->isReturnedByWorker()) {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo no ha sido devuelto por el trabajador.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        try {
            $this->loanService->confirmReturn($loan, $request->admin_notes);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Devolución confirmada correctamente',
                    'loan' => $loan->fresh(['tool', 'user'])
                ]);
            }

            return redirect()->route(route_prefix() . 'loans.index')
                ->with('status', 'Devolución confirmada correctamente. La herramienta ha sido devuelta al inventario.');
        }
        catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al confirmar la devolución: ' . $e->getMessage()
                ], 422);
            }
            return back()->withErrors(['error' => 'Error al confirmar la devolución: ' . $e->getMessage()]);
        }
    }

    /**
     * Process approved loan (mark as out)
     */
    public function processApproved(Request $request, Loan $loan): RedirectResponse|JsonResponse
    {
        if (!$loan->isApproved()) {
            return redirect()->route(route_prefix() . 'loans.index')
                ->with('error', 'Este préstamo no está aprobado.');
        }

        $this->loanService->processApproved($loan);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Préstamo procesado correctamente',
                'loan' => $loan->fresh(['tool', 'user'])
            ]);
        }

        return redirect()->route(route_prefix() . 'loans.index')
            ->with('status', 'Préstamo procesado correctamente');
    }

    public function downloadPdf(Request $request)
    {
        $filters = [
            'status' => $request->status,
            'tool_id' => $request->tool_id,
            'user_id' => $request->user_id,
        ];

        $loans = $this->loanService->getAllLoans($filters);

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