<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Dashboard Contable - AGROSAC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #10b981;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #10b981;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .summary-card {
            display: table-cell;
            width: 33.33%;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            vertical-align: top;
        }
        .summary-card h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #666;
        }
        .summary-card .amount {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .income { color: #10b981; }
        .expense { color: #dc2626; }
        .profit { color: #10b981; }
        .loss { color: #dc2626; }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section-title {
            background: #f0fdf4;
            padding: 10px;
            border-left: 4px solid #10b981;
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background: #10b981;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        table tr:nth-child(even) {
            background: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-success {
            background: #dcfce7;
            color: #166534;
        }
        .badge-danger {
            background: #fef2f2;
            color: #991b1b;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AGROSAC</h1>
        <p>Dashboard Contable</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <!-- Resumen General -->
    <div class="summary">
        <div class="summary-card">
            <h3>Total Ingresos</h3>
            <div class="amount income">${{ number_format($totalIncome, 2) }}</div>
        </div>
        <div class="summary-card">
            <h3>Total Gastos</h3>
            <div class="amount expense">${{ number_format($totalExpenses, 2) }}</div>
        </div>
        <div class="summary-card">
            <h3>Resultado Neto</h3>
            <div class="amount {{ $netProfit >= 0 ? 'profit' : 'loss' }}">
                {{ $netProfit >= 0 ? '+' : '' }}${{ number_format($netProfit, 2) }}
            </div>
        </div>
    </div>

    <!-- Ingresos por Categoría -->
    @if($incomeByCategory->count() > 0)
    <div class="section">
        <div class="section-title">Ingresos por Categoría</div>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($incomeByCategory as $item)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                    <td class="text-right">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Gastos por Categoría -->
    @if($expensesByCategory->count() > 0)
    <div class="section">
        <div class="section-title">Gastos por Categoría</div>
        <table>
            <thead>
                <tr>
                    <th>Categoría</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($expensesByCategory as $item)
                <tr>
                    <td>{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td>
                    <td class="text-right">${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Análisis por Cultivo -->
    @if(isset($cropAnalysis) && count($cropAnalysis) > 0)
    <div class="section">
        <div class="section-title">Análisis de Rentabilidad por Cultivo</div>
        <table>
            <thead>
                <tr>
                    <th>Cultivo</th>
                    <th class="text-right">Ingresos</th>
                    <th class="text-right">Gastos</th>
                    <th class="text-right">Costos</th>
                    <th class="text-right">Total</th>
                    <th class="text-right">Ganancia/Pérdida</th>
                </tr>
                <tr style="background: #f0fdf4;">
                    <th></th>
                    <th></th>
                    <th class="text-right" style="font-size: 10px; font-weight: normal;">Contables</th>
                    <th class="text-right" style="font-size: 10px; font-weight: normal;">Insumos | Trabajadores | Herramientas</th>
                    <th class="text-right" style="font-size: 10px; font-weight: normal;">Gastos + Costos</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($cropAnalysis as $analysis)
                @php
                    $totalCosts = $analysis['expenses']['supply_consumption'] + $analysis['expenses']['supply_movement'] + $analysis['expenses']['tasks'] + ($analysis['expenses']['tools'] ?? 0);
                    $totalGeneral = $analysis['expenses']['ledger'] + $totalCosts;
                @endphp
                <tr>
                    <td>
                        {{ $analysis['crop']->name }}
                        @if($analysis['crop']->plot)
                            <br><small style="color: #666;">{{ $analysis['crop']->plot->name }}</small>
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($analysis['income'], 2) }}</td>
                    <td class="text-right">${{ number_format($analysis['expenses']['ledger'], 2) }}</td>
                    <td class="text-right">
                        <div>Insumos: ${{ number_format($analysis['expenses']['supply_consumption'] + $analysis['expenses']['supply_movement'], 2) }}</div>
                        <div style="font-size: 10px; margin-top: 2px;">Trabajadores: ${{ number_format($analysis['expenses']['tasks'], 2) }}</div>
                        <div style="font-size: 10px; margin-top: 2px;">Herramientas: ${{ number_format($analysis['expenses']['tools'] ?? 0, 2) }}</div>
                    </td>
                    <td class="text-right"><strong>${{ number_format($totalGeneral, 2) }}</strong></td>
                    <td class="text-right">
                        @if($analysis['profit'] >= 0)
                            <span class="badge badge-success">+${{ number_format($analysis['profit'], 2) }}</span>
                        @else
                            <span class="badge badge-danger">${{ number_format($analysis['profit'], 2) }}</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema AGROSAC</p>
        <p>Página {PAGENO} de {nbpg}</p>
    </div>
</body>
</html>

