<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLedgerEntryRequest;
use App\Http\Requests\UpdateLedgerEntryRequest;
use App\Models\LedgerEntry;
use App\Models\Crop;
use App\Models\Plot;
use App\Models\SupplyConsumption;
use App\Models\SupplyMovement;
use App\Models\Task;
use App\Models\ToolEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LedgerController extends Controller
{
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

        // Gastos por categoría (solo contables)
        $expensesByCategory = LedgerEntry::where('type', 'expense')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        // Calcular gastos adicionales por tipo
        $totalSupplyCosts = SupplyConsumption::sum('total_cost') + 
                           SupplyMovement::where('type', 'exit')->sum('total_cost');
        $totalToolCosts = ToolEntry::where('type', 'purchase')
                           ->whereNotNull('total_cost')
                           ->sum('total_cost');
        $totalTaskCosts = Task::sum('total_payment');
        
        // Calcular total de gastos (contables + insumos + herramientas + trabajadores)
        $totalAllExpenses = $totalExpenses + $totalSupplyCosts + $totalToolCosts + $totalTaskCosts;
        
        // Calcular ganancia/pérdida total
        $totalProfit = $totalIncome - $totalAllExpenses;

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

        // Ingresos y egresos por mes (últimos 6 meses) - para gráfico de tendencia
        $monthlyTrendData = LedgerEntry::select(
                DB::raw('DATE_FORMAT(occurred_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as ledger_expenses')
            )
            ->where('occurred_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                // Calcular costos operativos para cada mes
                $monthStart = $item->month . '-01';
                $monthEnd = date('Y-m-t', strtotime($monthStart));
                
                // Costos de insumos del mes
                $supplyCosts = SupplyConsumption::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_cost') +
                              SupplyMovement::where('type', 'exit')
                                  ->whereBetween('created_at', [$monthStart, $monthEnd])
                                  ->sum('total_cost');
                
                // Costos de tareas (trabajadores) del mes
                $taskCosts = Task::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_payment');
                
                // Costos de herramientas del mes (compras)
                $toolCosts = ToolEntry::where('type', 'purchase')
                    ->whereNotNull('total_cost')
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->sum('total_cost');
                
                // Total de egresos = gastos contables + costos operativos
                $totalExpenses = $item->ledger_expenses + $supplyCosts + $taskCosts + $toolCosts;
                
                // Formatear nombre del mes en español
                $monthNames = [
                    '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
                    '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                    '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
                    '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
                ];
                $monthParts = explode('-', $item->month);
                $monthLabel = $monthNames[$monthParts[1]];
                
                return [
                    'month' => $item->month,
                    'month_label' => $monthLabel,
                    'income' => $item->income,
                    'expenses' => $totalExpenses
                ];
            });


        // Análisis completo por cultivo (ingresos vs gastos totales)
        $crops = Crop::with('plot')->get();
        $cropAnalysis = [];
        
        foreach ($crops as $crop) {
            // Ingresos contables del cultivo
            $ledgerIncome = LedgerEntry::where('type', 'income')
                ->where('crop_id', $crop->id)
                ->sum('amount');
            
            // Gastos contables del cultivo
            $ledgerExpenses = LedgerEntry::where('type', 'expense')
                ->where('crop_id', $crop->id)
                ->sum('amount');
            
            // Costos de insumos consumidos (SupplyConsumption)
            $supplyConsumptionCosts = SupplyConsumption::where('crop_id', $crop->id)
                ->sum('total_cost');
            
            // Costos de movimientos de insumos (solo salidas/consumos)
            $supplyMovementCosts = SupplyMovement::where('crop_id', $crop->id)
                ->where('type', 'exit')
                ->sum('total_cost');
            
            // Costos de trabajadores (tareas)
            $taskCosts = Task::where('crop_id', $crop->id)
                ->sum('total_payment');
            
            // Costos de herramientas: calcular proporcionalmente basado en tareas
            // Obtener el total de costos de herramientas (compras)
            $totalToolPurchases = ToolEntry::where('type', 'purchase')
                ->whereNotNull('total_cost')
                ->sum('total_cost');
            
            // Obtener el total de tareas y tareas por cultivo para distribución proporcional
            $totalTasks = Task::whereNotNull('crop_id')->count();
            $cropTasks = Task::where('crop_id', $crop->id)->count();
            
            // Calcular costo de herramientas proporcional al cultivo
            $toolCosts = 0;
            if ($totalTasks > 0 && $totalToolPurchases > 0) {
                // Distribuir proporcionalmente basado en número de tareas
                $toolCosts = ($cropTasks / $totalTasks) * $totalToolPurchases;
            }
            
            // Total de costos (insumos + trabajadores + herramientas)
            $totalCropCosts = $supplyConsumptionCosts + $supplyMovementCosts + $taskCosts + $toolCosts;
            
            // Total general (gastos + costos)
            $totalCropExpenses = $ledgerExpenses + $totalCropCosts;
            
            // Ganancia/Pérdida
            $cropProfit = $ledgerIncome - $totalCropExpenses;
            
            $cropAnalysis[] = [
                'crop' => $crop,
                'income' => $ledgerIncome,
                'expenses' => [
                    'ledger' => $ledgerExpenses,
                    'supply_consumption' => $supplyConsumptionCosts,
                    'supply_movement' => $supplyMovementCosts,
                    'tasks' => $taskCosts,
                    'tools' => $toolCosts,
                    'total_costs' => $totalCropCosts,
                    'total' => $totalCropExpenses,
                ],
                'profit' => $cropProfit,
            ];
        }
        
        // Ordenar por ganancia/pérdida
        usort($cropAnalysis, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        return view('admin.ledger.dashboard', compact(
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'incomeByCategory',
            'expensesByCategory',
            'incomeByCrop',
            'expensesByCrop',
            'recentEntries',
            'monthlyTrendData',
            'cropAnalysis',
            'totalSupplyCosts',
            'totalToolCosts',
            'totalTaskCosts',
            'totalAllExpenses',
            'totalProfit'
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



    public function update(UpdateLedgerEntryRequest $request, LedgerEntry $ledger)
    {
        \Illuminate\Support\Facades\Log::info('Ledger update request', [
            'id' => $ledger->id,
            'data' => $request->all()
        ]);

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
        // Reutilizar la lógica del dashboard
        $totalIncome = LedgerEntry::where('type', 'income')->sum('amount');
        $totalExpenses = LedgerEntry::where('type', 'expense')->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $incomeByCategory = LedgerEntry::where('type', 'income')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        $expensesByCategory = LedgerEntry::where('type', 'expense')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        // Calcular costos totales
        $totalSupplyCosts = SupplyConsumption::sum('total_cost') + 
                           SupplyMovement::where('type', 'exit')->sum('total_cost');
        $totalToolCosts = ToolEntry::where('type', 'purchase')
                           ->whereNotNull('total_cost')
                           ->sum('total_cost');
        $totalTaskCosts = Task::sum('total_payment');

        // Análisis completo por cultivo
        $crops = Crop::with('plot')->get();
        $cropAnalysis = [];
        
        // Obtener el total de costos de herramientas para distribución proporcional
        $totalToolPurchases = ToolEntry::where('type', 'purchase')
            ->whereNotNull('total_cost')
            ->sum('total_cost');
        $totalTasks = Task::whereNotNull('crop_id')->count();
        
        foreach ($crops as $crop) {
            $ledgerIncome = LedgerEntry::where('type', 'income')
                ->where('crop_id', $crop->id)
                ->sum('amount');
            
            $ledgerExpenses = LedgerEntry::where('type', 'expense')
                ->where('crop_id', $crop->id)
                ->sum('amount');
            
            $supplyConsumptionCosts = SupplyConsumption::where('crop_id', $crop->id)
                ->sum('total_cost');
            
            $supplyMovementCosts = SupplyMovement::where('crop_id', $crop->id)
                ->where('type', 'exit')
                ->sum('total_cost');
            
            $taskCosts = Task::where('crop_id', $crop->id)
                ->sum('total_payment');
            
            // Costos de herramientas proporcionales
            $cropTasks = Task::where('crop_id', $crop->id)->count();
            $toolCosts = 0;
            if ($totalTasks > 0 && $totalToolPurchases > 0) {
                $toolCosts = ($cropTasks / $totalTasks) * $totalToolPurchases;
            }
            
            $totalCropCosts = $supplyConsumptionCosts + $supplyMovementCosts + $taskCosts + $toolCosts;
            $totalCropExpenses = $ledgerExpenses + $totalCropCosts;
            $cropProfit = $ledgerIncome - $totalCropExpenses;
            
            $cropAnalysis[] = [
                'crop' => $crop,
                'income' => $ledgerIncome,
                'expenses' => [
                    'ledger' => $ledgerExpenses,
                    'supply_consumption' => $supplyConsumptionCosts,
                    'supply_movement' => $supplyMovementCosts,
                    'tasks' => $taskCosts,
                    'tools' => $toolCosts,
                    'total_costs' => $totalCropCosts,
                    'total' => $totalCropExpenses,
                ],
                'profit' => $cropProfit,
            ];
        }
        
        usort($cropAnalysis, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

        $pdf = Pdf::loadView('admin.ledger.pdf.dashboard', compact(
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'incomeByCategory',
            'expensesByCategory',
            'cropAnalysis',
            'totalSupplyCosts',
            'totalToolCosts',
            'totalTaskCosts'
        ));

        return $pdf->download('dashboard-contable-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Generar PDF del análisis por cultivo
     */
    public function downloadCropAnalysisPdf()
    {
        $crops = Crop::with('plot')->get();
        $cropAnalysis = [];
        
        // Obtener el total de costos de herramientas para distribución proporcional
        $totalToolPurchases = ToolEntry::where('type', 'purchase')
            ->whereNotNull('total_cost')
            ->sum('total_cost');
        $totalTasks = Task::whereNotNull('crop_id')->count();
        
        foreach ($crops as $crop) {
            $ledgerIncome = LedgerEntry::where('type', 'income')
                ->where('crop_id', $crop->id)
                ->sum('amount');
            
            $ledgerExpenses = LedgerEntry::where('type', 'expense')
                ->where('crop_id', $crop->id)
                ->sum('amount');
            
            $supplyConsumptionCosts = SupplyConsumption::where('crop_id', $crop->id)
                ->sum('total_cost');
            
            $supplyMovementCosts = SupplyMovement::where('crop_id', $crop->id)
                ->where('type', 'exit')
                ->sum('total_cost');
            
            $taskCosts = Task::where('crop_id', $crop->id)
                ->sum('total_payment');
            
            // Costos de herramientas proporcionales
            $cropTasks = Task::where('crop_id', $crop->id)->count();
            $toolCosts = 0;
            if ($totalTasks > 0 && $totalToolPurchases > 0) {
                $toolCosts = ($cropTasks / $totalTasks) * $totalToolPurchases;
            }
            
            $totalCropCosts = $supplyConsumptionCosts + $supplyMovementCosts + $taskCosts + $toolCosts;
            $totalCropExpenses = $ledgerExpenses + $totalCropCosts;
            $cropProfit = $ledgerIncome - $totalCropExpenses;
            
            $cropAnalysis[] = [
                'crop' => $crop,
                'income' => $ledgerIncome,
                'expenses' => [
                    'ledger' => $ledgerExpenses,
                    'supply_consumption' => $supplyConsumptionCosts,
                    'supply_movement' => $supplyMovementCosts,
                    'tasks' => $taskCosts,
                    'tools' => $toolCosts,
                    'total_costs' => $totalCropCosts,
                    'total' => $totalCropExpenses,
                ],
                'profit' => $cropProfit,
            ];
        }
        
        usort($cropAnalysis, function($a, $b) {
            return $b['profit'] <=> $a['profit'];
        });

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