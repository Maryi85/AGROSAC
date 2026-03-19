<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte Financiero - AGROSAC</title>
    <style>
        @page {
            margin: 10mm 15mm;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }

        /* --- Colors --- */
        /* Corporate Green: #10b981 */
        /* Red: #ef4444 */
        /* Orange: #f59e0b */
        
        /* --- Utilities --- */
        .text-green { color: #059669; }
        .bg-green-light { background-color: #ecfdf5; }
        .text-red { color: #dc2626; }
        .bg-red-light { background-color: #fef2f2; }
        .text-orange { color: #d97706; }
        .bg-orange-light { background-color: #fffbeb; }
        .text-gray { color: #6b7280; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* --- Header --- */
        .header {
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #10b981;
        }
        .header-table { width: 100%; }
        .header-logo { width: 60px; height: auto; }
        .header-title { 
            font-size: 18pt; 
            font-weight: bold; 
            color: #10b981; 
            margin: 0;
        }
        .header-subtitle { 
            font-size: 10pt; 
            color: #666; 
            margin-top: 5px;
        }

        /* --- KPIs Cards --- */
        .kpi-table {
            width: 100%;
            border-spacing: 10px;
            margin-bottom: 25px;
        }
        .kpi-card {
            padding: 15px;
            border-radius: 8px; /* DomPDF supports border-radius */
            text-align: center;
            width: 25%;
            vertical-align: middle;
        }
        .kpi-label {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .kpi-value {
            font-size: 14pt;
            font-weight: bold;
        }
        
        /* --- Widgets (Summary Tables) --- */
        .widgets-container {
            width: 100%;
            margin-bottom: 30px;
            overflow: hidden; /* Clear floats */
        }
        .widget {
            float: left;
            width: 48%;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px;
        }
        .widget-title {
            font-size: 11pt;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }
        .widget-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .widget-table td {
            padding: 6px 4px;
            border-bottom: 1px solid #f3f4f6;
        }
        .widget-table tr:last-child td { border-bottom: none; }
        .category-name { font-weight: 500; color: #4b5563; }
        
        /* Fix for second widget float */
        .widget.right { float: right; }

        /* --- Detailed Table --- */
        .details-section {
            width: 100%;
            margin-top: 20px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #111;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-left: 5px solid #10b981;
            padding-left: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .data-table th {
            background-color: #064e3b; /* Dark green */
            color: #fff;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        
        /* --- Footer --- */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 60px; vertical-align: middle;">
                    <!-- Logo (assuming public path is accessible) -->
                   <img src="{{ public_path('AGROSACLOGO.png') }}" class="header-logo" alt="Logo">
                </td>
                <td style="vertical-align: middle; padding-left: 15px;">
                    <h1 class="header-title">AGROSAC</h1>
                    <div class="header-subtitle">Sistema de Gestión Agrícola</div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div style="font-size: 14pt; font-weight: bold; color: #333;">REPORTE FINANCIERO</div>
                    <div style="font-size: 9pt; color: #666;">Generado: {{ now()->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- KPIs Cards -->
    <table class="kpi-table">
        <tr>
            <!-- Ingresos -->
            <td class="kpi-card bg-green-light">
                <div class="kpi-label text-green">Ingresos Totales</div>
                <div class="kpi-value text-green">${{ number_format($totalIncome, 0) }}</div>
            </td>
            <!-- Gastos -->
            <td class="kpi-card bg-red-light">
                <div class="kpi-label text-red">Gastos Totales</div>
                <div class="kpi-value text-red">${{ number_format($totalExpenses, 0) }}</div>
            </td>
            <!-- Costos Operativos -->
            <td class="kpi-card bg-orange-light">
                <div class="kpi-label text-orange">Costos Operativos</div>
                <div class="kpi-value text-orange">${{ number_format(($totalSupplyCosts ?? 0) + ($totalToolCosts ?? 0) + ($totalTaskCosts ?? 0), 0) }}</div>
            </td>
            <!-- Resultado Neto -->
            @php $netProfit = $totalIncome - $totalExpenses; $isPositive = $netProfit >= 0; @endphp
            <td class="kpi-card" style="background-color: {{ $isPositive ? '#ecfdf5' : '#fef2f2' }}; border: 1px solid {{ $isPositive ? '#10b981' : '#ef4444' }};">
                <div class="kpi-label" style="color: {{ $isPositive ? '#059669' : '#dc2626' }};">Resultado Neto</div>
                <div class="kpi-value" style="color: {{ $isPositive ? '#059669' : '#dc2626' }};">
                    {{ $isPositive ? '+' : '' }}${{ number_format($netProfit, 0) }}
                </div>
            </td>
        </tr>
    </table>

    <!-- Widgets: Income & Expenses Breakdown -->
    <div class="widgets-container">
        <!-- Widget A: Ingresos -->
        <div class="widget">
            <div class="widget-title">Resumen de Ingresos</div>
            <table class="widget-table">
                @forelse($incomeByCategory as $item)
                <tr>
                    <td class="category-name">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                    <td class="text-right font-bold text-green">${{ number_format($item->total, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center text-gray">Sin registros</td></tr>
                @endforelse
            </table>
        </div>

        <!-- Widget B: Gastos -->
        <div class="widget right">
            <div class="widget-title">Resumen de Gastos</div>
            <table class="widget-table">
                @forelse($expensesByCategory as $item)
                <tr>
                    <td class="category-name">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                    <td class="text-right font-bold text-red">${{ number_format($item->total, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center text-gray">Sin registros</td></tr>
                @endforelse
            </table>
        </div>
    </div>
    
    <!-- Cost Breakdown Section (Optional, mimicking dashboard details) -->
    @if(isset($totalSupplyCosts) && isset($totalToolCosts) && isset($totalTaskCosts))
    <div class="widgets-container" style="margin-top: 10px;">
        <div class="section-title" style="font-size: 10pt; border-left-color: #f59e0b;">Desglose de Costos Operativos</div>
        <table class="data-table">
            <thead>
                <tr style="background-color: #FFF8E1;"> 
                    <th style="background-color: #f59e0b; color: white;">Insumos</th>
                    <th style="background-color: #f59e0b; color: white;">Herramientas Total</th>
                    <th style="background-color: #f59e0b; color: white;">Mano de Obra (Tareas)</th>
                    <th class="text-right" style="background-color: #f59e0b; color: white;">Total Operativo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>${{ number_format($totalSupplyCosts, 0) }}</td>
                    <td>${{ number_format($totalToolCosts, 0) }}</td>
                    <td>${{ number_format($totalTaskCosts, 0) }}</td>
                    <td class="text-right font-bold text-orange">
                        ${{ number_format(($totalSupplyCosts ?? 0) + ($totalToolCosts ?? 0) + ($totalTaskCosts ?? 0), 0) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detailed Analysis Table -->
    @if(isset($cropAnalysis) && count($cropAnalysis) > 0)
    <div class="details-section">
        <div class="section-title">Análisis de Rentabilidad por Cultivo</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cultivo / Lote</th>
                    <th class="text-right">Ingresos</th>
                    <th class="text-right">Gastos Directos</th>
                    <th class="text-right">Costos Op.</th>
                    <th class="text-right">Total Gastos</th>
                    <th class="text-right">Resultado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cropAnalysis as $analysis)
                @php
                    $totalCosts = $analysis['expenses']['supply_consumption'] + 
                                 $analysis['expenses']['supply_movement'] + 
                                 $analysis['expenses']['tasks'] + 
                                 ($analysis['expenses']['tools'] ?? 0);
                    $totalGeneral = $analysis['expenses']['ledger'] + $totalCosts;
                    $profit = $analysis['profit'];
                    $isProfit = $profit >= 0;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight: bold;">{{ $analysis['crop']->name }}</div>
                        <div style="font-size: 8pt; color: #666;">
                            {{ $analysis['crop']->plot->name ?? 'Sin Lote' }}
                        </div>
                    </td>
                    <td class="text-right text-green font-bold">
                        ${{ number_format($analysis['income'], 0) }}
                    </td>
                    <td class="text-right text-red">
                        ${{ number_format($analysis['expenses']['ledger'], 0) }}
                    </td>
                    <td class="text-right text-orange">
                        ${{ number_format($totalCosts, 0) }}
                    </td>
                    <td class="text-right font-bold" style="color: #b91c1c;">
                        ${{ number_format($totalGeneral, 0) }}
                    </td>
                    <td class="text-right font-bold">
                        <span style="background-color: {{ $isProfit ? '#d1fae5' : '#fee2e2' }}; color: {{ $isProfit ? '#065f46' : '#991b1b' }}; padding: 2px 6px; border-radius: 4px;">
                            {{ $isProfit ? '+' : '' }}${{ number_format($profit, 0) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        AGROSAC - Sistema de Gestión Agrícola &copy; {{ date('Y') }}
        <br>
        Este documento es un reporte generado automáticamente. 
    </div>

</body>
</html>
