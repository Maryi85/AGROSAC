<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLedgerEntryRequest;
use App\Http\Requests\UpdateLedgerEntryRequest;
use App\Models\LedgerEntry;
use App\Models\Crop;
use App\Models\Plot;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class LedgerController extends Controller
{
    public function index(Request $request): View
    {
        $query = LedgerEntry::with(['crop', 'plot']);

        // Filtro por tipo
        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        // Filtro por categoría
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Filtro por cultivo
        if ($request->filled('crop_id') && $request->crop_id !== 'all') {
            $query->where('crop_id', $request->crop_id);
        }

        // Filtro por lote
        if ($request->filled('plot_id') && $request->plot_id !== 'all') {
            $query->where('plot_id', $request->plot_id);
        }

        // Filtro por fecha
        if ($request->filled('date_from')) {
            $query->where('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('occurred_at', '<=', $request->date_to);
        }

        $entries = $query->orderBy('occurred_at', 'desc')->paginate(15);

        // Obtener datos para los filtros
        $crops = Crop::where('status', 'active')->orderBy('name')->get();
        $plots = Plot::orderBy('name')->get();

        // Categorías disponibles
        $categories = [
            'venta_cultivos' => 'Venta de Cultivos',
            'servicios_agricolas' => 'Servicios Agrícolas',
            'subsidios' => 'Subsidios',
            'otros_ingresos' => 'Otros Ingresos',
            'insumos' => 'Insumos',
            'mano_obra' => 'Mano de Obra',
            'maquinaria' => 'Maquinaria',
            'fertilizantes' => 'Fertilizantes',
            'pesticidas' => 'Pesticidas',
            'riego' => 'Riego',
            'otros_gastos' => 'Otros Gastos',
        ];

        $types = [
            'income' => 'Ingresos',
            'expense' => 'Gastos',
        ];

        return view('admin.ledger.index', compact('entries', 'crops', 'plots', 'categories', 'types'));
    }

    public function dashboard(): View
    {
        // Estadísticas generales
        $totalIncome = LedgerEntry::where('type', 'income')->sum('amount');
        $totalExpenses = LedgerEntry::where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        // Ingresos por categoría
        $incomeByCategory = LedgerEntry::where('type', 'income')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        // Gastos por categoría
        $expensesByCategory = LedgerEntry::where('type', 'expense')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        // Ingresos por cultivo
        $incomeByCrop = LedgerEntry::where('type', 'income')
            ->whereNotNull('crop_id')
            ->with('crop')
            ->select('crop_id', DB::raw('SUM(amount) as total'))
            ->groupBy('crop_id')
            ->orderBy('total', 'desc')
            ->get();

        // Gastos por cultivo
        $expensesByCrop = LedgerEntry::where('type', 'expense')
            ->whereNotNull('crop_id')
            ->with('crop')
            ->select('crop_id', DB::raw('SUM(amount) as total'))
            ->groupBy('crop_id')
            ->orderBy('total', 'desc')
            ->get();

        // Movimientos recientes
        $recentEntries = LedgerEntry::with(['crop', 'plot'])
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        // Ingresos y gastos por mes (últimos 12 meses)
        $monthlyData = LedgerEntry::select(
                DB::raw('DATE_FORMAT(occurred_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as expense')
            )
            ->where('occurred_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.ledger.dashboard', compact(
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'incomeByCategory',
            'expensesByCategory',
            'incomeByCrop',
            'expensesByCrop',
            'recentEntries',
            'monthlyData'
        ));
    }

    public function create(): View
    {
        $crops = Crop::where('status', 'active')->orderBy('name')->get();
        $plots = Plot::orderBy('name')->get();

        $categories = [
            'venta_cultivos' => 'Venta de Cultivos',
            'servicios_agricolas' => 'Servicios Agrícolas',
            'subsidios' => 'Subsidios',
            'otros_ingresos' => 'Otros Ingresos',
            'insumos' => 'Insumos',
            'mano_obra' => 'Mano de Obra',
            'maquinaria' => 'Maquinaria',
            'fertilizantes' => 'Fertilizantes',
            'pesticidas' => 'Pesticidas',
            'riego' => 'Riego',
            'otros_gastos' => 'Otros Gastos',
        ];

        $types = [
            'income' => 'Ingresos',
            'expense' => 'Gastos',
        ];

        return view('admin.ledger.create', compact('crops', 'plots', 'categories', 'types'));
    }

    public function store(StoreLedgerEntryRequest $request): RedirectResponse
    {
        $entry = LedgerEntry::create($request->validated());

        return redirect()->route('admin.ledger.index')
            ->with('status', 'Movimiento contable registrado correctamente');
    }

    public function show(LedgerEntry $ledgerEntry): View
    {
        $ledgerEntry->load(['crop', 'plot']);
        return view('admin.ledger.show', compact('ledgerEntry'));
    }

    public function edit(LedgerEntry $ledgerEntry): View
    {
        $ledgerEntry->load(['crop', 'plot']);
        
        $crops = Crop::where('status', 'active')->orderBy('name')->get();
        $plots = Plot::orderBy('name')->get();

        $categories = [
            'venta_cultivos' => 'Venta de Cultivos',
            'servicios_agricolas' => 'Servicios Agrícolas',
            'subsidios' => 'Subsidios',
            'otros_ingresos' => 'Otros Ingresos',
            'insumos' => 'Insumos',
            'mano_obra' => 'Mano de Obra',
            'maquinaria' => 'Maquinaria',
            'fertilizantes' => 'Fertilizantes',
            'pesticidas' => 'Pesticidas',
            'riego' => 'Riego',
            'otros_gastos' => 'Otros Gastos',
        ];

        $types = [
            'income' => 'Ingresos',
            'expense' => 'Gastos',
        ];

        return view('admin.ledger.edit', compact('ledgerEntry', 'crops', 'plots', 'categories', 'types'));
    }

    public function update(UpdateLedgerEntryRequest $request, LedgerEntry $ledgerEntry): RedirectResponse
    {
        $ledgerEntry->update($request->validated());

        return redirect()->route('admin.ledger.index')
            ->with('status', 'Movimiento contable actualizado correctamente');
    }

    public function destroy(LedgerEntry $ledgerEntry): RedirectResponse
    {
        $ledgerEntry->delete();

        return redirect()->route('admin.ledger.index')
            ->with('status', 'Movimiento contable eliminado correctamente');
    }
}