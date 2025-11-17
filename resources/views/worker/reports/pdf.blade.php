<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Trabajador - {{ $user->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #10b981;
            margin: 0;
            font-size: 24px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #065f46;
            margin-bottom: 15px;
            border-bottom: 1px solid #10b981;
            padding-bottom: 5px;
        }
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            font-weight: bold;
            padding: 5px 10px 5px 0;
            width: 150px;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
        }
        .summary-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .summary-cell {
            display: table-cell;
            width: 25%;
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f9fafb;
        }
        .summary-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #065f46;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #10b981;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            padding: 6px 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background-color: #d1fae5 !important;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Trabajador</h1>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Información del Trabajador -->
    <div class="section">
        <div class="section-title">Información del Trabajador</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nombre:</div>
                <div class="info-value">{{ $user->name }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value">{{ $user->email }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Estado:</div>
                <div class="info-value">{{ $user->email_verified_at ? 'Activo' : 'Inactivo' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Fecha de Registro:</div>
                <div class="info-value">{{ $user->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="section">
        <div class="section-title">Resumen General</div>
        <div class="summary-grid">
            <div class="summary-cell">
                <div class="summary-label">Total de Tareas</div>
                <div class="summary-value">{{ $totalTasks }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">Total Horas</div>
                <div class="summary-value">{{ number_format($totalHours ?? 0, 2) }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">Total Kilos</div>
                <div class="summary-value">{{ number_format($totalKilos ?? 0, 3) }}</div>
            </div>
            <div class="summary-cell">
                <div class="summary-label">Total Acumulado</div>
                <div class="summary-value">${{ number_format($totalPayment ?? 0, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Resumen por Cultivo -->
    @if(count($cropTotals) > 0)
    <div class="section">
        <div class="section-title">Resumen por Cultivo</div>
        <table>
            <thead>
                <tr>
                    <th>Cultivo</th>
                    <th class="text-right">Tareas</th>
                    <th class="text-right">Horas</th>
                    <th class="text-right">Kilos</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cropTotals as $cropTotal)
                <tr>
                    <td>{{ $cropTotal['crop'] }}</td>
                    <td class="text-right">{{ $cropTotal['tasks_count'] }}</td>
                    <td class="text-right">{{ number_format($cropTotal['total_hours'], 2) }}</td>
                    <td class="text-right">{{ number_format($cropTotal['total_kilos'], 3) }}</td>
                    <td class="text-right">${{ number_format($cropTotal['total_payment'] ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detalle de Tareas -->
    <div class="section">
        <div class="section-title">Detalle de Tareas</div>
        @if($tasks->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Cultivo</th>
                    <th>Lote</th>
                    <th class="text-right">Horas</th>
                    <th class="text-right">Kilos</th>
                    <th class="text-right">Precio</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->scheduled_for->format('d/m/Y') }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $task->type)) }}</td>
                    <td>{{ $task->description ?: 'Sin descripción' }}</td>
                    <td>{{ $task->crop ? $task->crop->name : 'Sin cultivo' }}</td>
                    <td>{{ $task->plot ? $task->plot->name : 'Sin lote' }}</td>
                    <td class="text-right">{{ $task->hours > 0 ? number_format($task->hours, 2) : '-' }}</td>
                    <td class="text-right">{{ $task->kilos > 0 ? number_format($task->kilos, 3) : '-' }}</td>
                    <td class="text-right">
                        @if($task->price_per_hour)
                            ${{ number_format($task->price_per_hour, 2) }}/h
                        @elseif($task->price_per_day)
                            ${{ number_format($task->price_per_day, 2) }}/d
                        @elseif($task->price_per_kg)
                            ${{ number_format($task->price_per_kg, 2) }}/kg
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">${{ number_format($task->calculated_payment ?? $task->total_payment ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="8" class="text-right"><strong>Total Acumulado:</strong></td>
                    <td class="text-right"><strong>${{ number_format($totalPayment ?? 0, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
        @else
        <p>No hay tareas aprobadas para este trabajador.</p>
        @endif
    </div>

    <div class="footer">
        <p>Reporte generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>

