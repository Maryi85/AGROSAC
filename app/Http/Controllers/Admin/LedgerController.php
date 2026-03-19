<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLedgerEntryRequest;
use App\Http\Requests\UpdateLedgerEntryRequest;
use App\Models\LedgerEntry;
use App\Models\Crop;
use App\Models\Plot;
use App\Services\LedgerStatisticsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LedgerController extends Controller
{
    protected LedgerStatisticsService $statsService;

    public function __construct(LedgerStatisticsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function index(Request $request): View
    {
        $query = LedgerEntry::with(['crop', 'plot']);

        // Búsqueda general
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                // Buscar en categoría
                $q->where('category', 'like', '%' . $searchTerm . '%')
                  // Buscar en referencia
                  ->orWhere('reference', 'like', '%' . $searchTerm . '%')
                  // Buscar en cultivo
                  ->orWhereHas('crop', function($subQ) use ($searchTerm) {
                      $subQ->where('name', 'like', '%' . $searchTerm . '%');
                  })
                  // Buscar en lote
                  ->orWhereHas('plot', function($subQ) use ($searchTerm) {
                      $subQ->where('name', 'like', '%' . $searchTerm . '%');
                  });

                // Mapeo de términos en español a inglés para el tipo
                $lowerTerm = strtolower($searchTerm);
                if (str_contains('ingresos', $lowerTerm) || str_contains($lowerTerm, 'ingreso')) {
                    $q->orWhere('type', 'income');
                }
                if (str_contains('gastos', $lowerTerm) || str_contains($lowerTerm, 'gasto')) {
                    $q->orWhere('type', 'expense');
                }
            });
        }

        $entries = $query->orderBy('occurred_at', 'desc')->paginate(15);

        // Datos para el modal de edición/creación
        $crops = Crop::where('status', 'active')->orderBy('name')->get();
        $plots = Plot::orderBy('name')->get();

        $types = [
            'income' => 'Ingresos',
            'expense' => 'Gastos',
        ];

        // Categorías para la vista
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

        return view('admin.ledger.index', compact('entries', 'categories', 'crops', 'plots', 'types'));
    }

    public function dashboard(): View
    {
        // Estadísticas financieras generales
        $summary = $this->statsService->getFinancialSummary();

        // Desglose por categoría
        $incomeByCategory = $this->statsService->getIncomeByCategory();
        $expensesByCategory = $this->statsService->getExpensesByCategory();

        // Ingresos y egresos por mes
        $monthlyTrendData = $this->statsService->getMonthlyTrends(6);

        // Análisis por cultivo (Optimizado para evitar N+1)
        $cropAnalysis = $this->statsService->getCropAnalysis();
        
        // Ingresos y Gastos por cultivo (para gráficos simples)
        // Reutilizamos el análisis detallado para extraer esto si es necesario, 
        // o llamamos a métodos específicos si la vista los requiere en formato distinto.
        // Para mantener compatibilidad con la vista actual:
        $incomeByCrop = $this->statsService->getIncomeByCrop();
        $expensesByCrop = $this->statsService->getExpensesByCrop();

        // Movimientos recientes
        $recentEntries = LedgerEntry::with(['crop', 'plot'])
            ->orderBy('occurred_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.ledger.dashboard', array_merge($summary, compact(
            'incomeByCategory',
            'expensesByCategory',
            'incomeByCrop',
            'expensesByCrop',
            'recentEntries',
            'monthlyTrendData',
            'cropAnalysis'
        )));
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
        LedgerEntry::create($request->validated());

        return redirect()->route('admin.ledger.index')
            ->with('status', 'Movimiento contable registrado correctamente');
    }

    public function show(LedgerEntry $ledgerEntry): View
    {
        $ledgerEntry->load(['crop', 'plot']);
        return view('admin.ledger.show', compact('ledgerEntry'));
    }

    public function update(UpdateLedgerEntryRequest $request, LedgerEntry $ledger)
    {
        $ledger->update($request->validated());

        if ($request->wantsJson()) {
            session()->flash('status', 'Movimiento contable actualizado correctamente');
            return response()->json([
                'status' => 'success',
                'message' => 'Movimiento contable actualizado correctamente'
            ]);
        }

        return redirect()->route('admin.ledger.index')
            ->with('status', 'Movimiento contable actualizado correctamente');
    }

    public function destroy(LedgerEntry $ledgerEntry): RedirectResponse
    {
        $ledgerEntry->delete();

        return redirect()->route('admin.ledger.index')
            ->with('status', 'Movimiento contable eliminado correctamente');
    }

    /**
     * Generar PDF del dashboard contable
     */
    public function downloadDashboardPdf()
    {
        $summary = $this->statsService->getFinancialSummary();
        $incomeByCategory = $this->statsService->getIncomeByCategory();
        $expensesByCategory = $this->statsService->getExpensesByCategory();
        $cropAnalysis = $this->statsService->getCropAnalysis();

        $pdf = Pdf::loadView('admin.ledger.pdf.dashboard', array_merge($summary, compact(
            'incomeByCategory',
            'expensesByCategory',
            'cropAnalysis'
        )));

        return $pdf->download('dashboard-contable-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generar PDF del análisis por cultivo
     */
    public function downloadCropAnalysisPdf()
    {
        $cropAnalysis = $this->statsService->getCropAnalysis();

        $pdf = Pdf::loadView('admin.ledger.pdf.crop-analysis', compact('cropAnalysis'));

        return $pdf->download('analisis-cultivos-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generar PDF de movimientos contables con filtros
     */
    public function downloadMovementsPdf(Request $request)
    {
        $query = LedgerEntry::with(['crop', 'plot']);

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('crop_id') && $request->crop_id !== 'all') {
            $query->where('crop_id', $request->crop_id);
        }

        if ($request->filled('plot_id') && $request->plot_id !== 'all') {
            $query->where('plot_id', $request->plot_id);
        }

        if ($request->filled('date_from')) {
            $query->where('occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('occurred_at', '<=', $request->date_to);
        }

        $entries = $query->orderBy('occurred_at', 'desc')->get();

        $pdf = Pdf::loadView('admin.ledger.pdf.movements', compact('entries'));

        return $pdf->download('movimientos-contables-' . now()->format('Y-m-d') . '.pdf');
    }
}