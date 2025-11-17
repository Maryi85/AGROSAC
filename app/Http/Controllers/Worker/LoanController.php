<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $loans = Loan::with(['tool', 'approvedBy', 'returnedBy'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('worker.loans.index', compact('loans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Usar el accessor available_qty que calcula desde tool_entries
        $tools = Tool::with('entries')
            ->where('status', 'operational')
            ->get()
            ->filter(function($tool) {
                return $tool->available_qty > 0;
            })
            ->values();

        return view('worker.loans.create', compact('tools'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tool_id' => 'required|exists:tools,id',
            'quantity' => 'required|integer|min:1',
            'due_at' => 'required|date|after:today',
            'request_notes' => 'nullable|string|max:500',
        ]);

        $tool = Tool::findOrFail($request->tool_id);

        // Verificar que hay suficiente cantidad disponible
        if ($tool->available_qty < $request->quantity) {
            return back()->withErrors(['quantity' => 'No hay suficientes herramientas disponibles. Disponible: ' . $tool->available_qty]);
        }

        Loan::create([
            'tool_id' => $request->tool_id,
            'user_id' => auth()->id(),
            'quantity' => $request->quantity,
            'due_at' => $request->due_at,
            'request_notes' => $request->request_notes,
            'status' => 'pending',
        ]);

        return redirect()->route('worker.loans.index')
            ->with('status', 'Solicitud de préstamo enviada. Esperando aprobación del administrador.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        // Verificar que el préstamo pertenece al usuario autenticado
        if ($loan->user_id !== auth()->id()) {
            abort(403, 'No tienes permisos para ver este préstamo.');
        }

        $loan->load(['tool', 'approvedBy', 'returnedBy']);

        return view('worker.loans.show', compact('loan'));
    }

    /**
     * Show the form for returning a tool.
     */
    public function returnForm(Loan $loan)
    {
        // Verificar que el préstamo pertenece al usuario autenticado y está aprobado
        if ($loan->user_id !== auth()->id()) {
            abort(403, 'No tienes permisos para devolver este préstamo.');
        }

        if (!$loan->isOut()) {
            return redirect()->route('worker.loans.index')
                ->with('error', 'Este préstamo no está en estado de préstamo activo.');
        }

        return view('worker.loans.return', compact('loan'));
    }

    /**
     * Process the return of a tool.
     */
    public function processReturn(Request $request, Loan $loan)
    {
        // Verificar que el préstamo pertenece al usuario autenticado y está aprobado
        if ($loan->user_id !== auth()->id()) {
            abort(403, 'No tienes permisos para devolver este préstamo.');
        }

        if (!$loan->isOut()) {
            return redirect()->route('worker.loans.index')
                ->with('error', 'Este préstamo no está en estado de préstamo activo.');
        }

        $request->validate([
            'condition_return' => 'required|in:good,damaged,lost',
            'return_notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Actualizar el préstamo - NO devolver al inventario aún
            $loan->update([
                'condition_return' => $request->condition_return,
                'returned_by' => auth()->id(),
                'returned_at' => now(),
                'status' => 'returned_by_worker', // Estado intermedio
            ]);

            // NO actualizar la cantidad disponible de la herramienta aún
            // Esto se hará cuando el administrador confirme la devolución

            DB::commit();

            return redirect()->route('worker.loans.index')
                ->with('status', 'Herramienta devuelta exitosamente. Esperando confirmación del administrador.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Error al procesar la devolución: ' . $e->getMessage()]);
        }
    }
}