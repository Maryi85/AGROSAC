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
use Carbon\Carbon;

class LoanController extends Controller
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

        return view('admin.loans.index', compact('loans', 'tools', 'workers', 'statuses'));
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

        return view('admin.loans.create', compact('tools', 'workers'));
    }

    public function store(StoreLoanRequest $request): RedirectResponse
    {
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

        return redirect()->route('admin.loans.index')
            ->with('status', 'Herramienta prestada correctamente');
    }

    public function show(Loan $loan): View
    {
        $loan->load(['tool', 'user']);
        return view('admin.loans.show', compact('loan'));
    }

    public function edit(Loan $loan): View
    {
        $loan->load(['tool', 'user']);
        return view('admin.loans.edit', compact('loan'));
    }

    public function update(UpdateLoanRequest $request, Loan $loan): RedirectResponse
    {
        $loan->update($request->validated());

        return redirect()->route('admin.loans.index')
            ->with('status', 'Préstamo actualizado correctamente');
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        // Si el préstamo está activo, devolver la cantidad a la herramienta
        if ($loan->status === 'out') {
            $loan->tool->increment('available_qty', $loan->quantity);
        }

        $loan->delete();

        return redirect()->route('admin.loans.index')
            ->with('status', 'Préstamo eliminado correctamente');
    }

    public function return(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route('admin.loans.index')
                ->with('error', 'Este préstamo ya ha sido devuelto.');
        }

        // Marcar como devuelto
        $loan->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        // Devolver la cantidad a la herramienta
        $loan->tool->increment('available_qty', $loan->quantity);

        return redirect()->route('admin.loans.index')
            ->with('status', 'Herramienta devuelta correctamente');
    }

    public function markAsLost(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route('admin.loans.index')
                ->with('error', 'Este préstamo ya ha sido procesado.');
        }

        // Marcar como perdido
        $loan->update([
            'status' => 'lost',
            'returned_at' => now(),
        ]);

        // No devolver la cantidad a la herramienta (está perdida)
        // Decrementar la cantidad total
        $loan->tool->decrement('total_qty', $loan->quantity);

        return redirect()->route('admin.loans.index')
            ->with('status', 'Herramienta marcada como perdida');
    }

    public function markAsDamaged(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'out') {
            return redirect()->route('admin.loans.index')
                ->with('error', 'Este préstamo ya ha sido procesado.');
        }

        // Marcar como dañado
        $loan->update([
            'status' => 'damaged',
            'returned_at' => now(),
        ]);

        // Devolver la cantidad a la herramienta pero marcar como dañada
        $loan->tool->increment('available_qty', $loan->quantity);
        $loan->tool->update(['status' => 'damaged']);

        return redirect()->route('admin.loans.index')
            ->with('status', 'Herramienta marcada como dañada');
    }
}