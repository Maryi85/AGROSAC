<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Trabajador - {{ $worker->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 20px;
        }
        h1 {
            color: #047857;
            font-size: 20px;
            margin-bottom: 5px;
        }
        h2 {
            color: #059669;
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
            border-bottom: 2px solid #047857;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 11px;
        }
        th {
            background-color: #d1fae5;
            color: #047857;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            border: 1px solid #a7f3d0;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #d1fae5;
        }
        tr:nth-child(even) {
            background-color: #f0fdf4;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }
        .info-item {
            padding: 8px;
            background-color: #f9fafb;
            border-left: 3px solid #059669;
        }
        .info-label {
            font-weight: bold;
            color: #047857;
            font-size: 10px;
            text-transform: uppercase;
        }
        .info-value {
            font-size: 13px;
            margin-top: 3px;
        }
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .summary-card {
            padding: 12px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-card.blue { background-color: #dbeafe; border: 1px solid #93c5fd; }
        .summary-card.green { background-color: #d1fae5; border: 1px solid #a7f3d0; }
        .summary-card.orange { background-color: #fed7aa; border: 1px solid #fdba74; }
        .summary-card.emerald { background-color: #d1fae5; border: 1px solid #6ee7b7; }
        
        .summary-label {
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-card.blue .summary-label { color: #1e40af; }
        .summary-card.green .summary-label { color: #047857; }
        .summary-card.orange .summary-label { color: #c2410c; }
        .summary-card.emerald .summary-label { color: #047857; }
        
        .summary-value {
            font-size: 18px;
            font-weight: bold;
        }
        .summary-card.blue .summary-value { color: #1e3a8a; }
        .summary-card.green .summary-value { color: #065f46; }
        .summary-card.orange .summary-value { color: #9a3412; }
        .summary-card.emerald .summary-value { color: #065f46; }
        
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-active {
            background-color: #d1fae5;
            color: #047857;
        }
        .status-inactive {
            background-color: #e5e7eb;
            color: #374151;
        }
        .footer {
            margin-top: 30px;
            padding-top: 10px;
            border-top: 1px solid #d1d5db;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }
        tfoot td {
            background-color: #d1fae5;
            font-weight: bold;
            color: #047857;
        }
    </style>
</head>
<body>
    <h1>Reporte de Trabajador</h1>
    <p style="color: #6b7280; margin-bottom: 20px;">Generado el {{ now()->format('d/m/Y H:i') }}</p>

    <h2>Información del Trabajador</h2>
    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Nombre</div>
            <div class="info-value">{{ $worker->name }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value">{{ $worker->email }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Estado</div>
            <div class="info-value">
                @if($worker->email_verified_at)
                    <span class="status-badge status-active">Activo</span>
                @else
                    <span class="status-badge status-inactive">Inactivo</span>
                @endif
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Fecha de Registro</div>
            <div class="info-value">{{ $worker->created_at->format('d/m/Y H:i') }}</div>
        </div>
    </div>

    <h2>Resumen General</h2>
    <div class="summary-cards">
        <div class="summary-card blue">
            <div class="summary-label">Total de Tareas</div>
            <div class="summary-value">{{ $totalTasks }}</div>
        </div>
        <div class="summary-card green">
            <div class="summary-label">Total Horas</div>
            <div class="summary-value">{{ number_format($totalHours ?? 0, 2) }}</div>
        </div>
        <div class="summary-card orange">
            <div class="summary-label">Total Kilos</div>
            <div class="summary-value">{{ number_format($totalKilos ?? 0, 3) }}</div>
        </div>
        <div class="summary-card emerald">
            <div class="summary-label">Total Acumulado</div>
            <div class="summary-value">${{ number_format($totalPayment ?? 0, 2) }}</div>
        </div>
    </div>

    @if(count($cropTotals) > 0)
    <h2>Resumen por Cultivo</h2>
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
                <td class="font-bold">{{ $cropTotal['crop'] }}</td>
                <td class="text-right">{{ $cropTotal['tasks_count'] }}</td>
                <td class="text-right">{{ number_format($cropTotal['total_hours'], 2) }}</td>
                <td class="text-right">{{ number_format($cropTotal['total_kilos'], 3) }}</td>
                <td class="text-right font-bold">${{ number_format($cropTotal['total_payment'] ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h2>Detalle de Tareas</h2>
    @if($tasks->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Cultivo</th>
                <th>Lote</th>
                <th class="text-right">Horas</th>
                <th class="text-right">Kilos</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td>{{ $task->scheduled_for->format('d/m/Y') }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $task->type)) }}</td>
                <td>{{ $task->crop ? $task->crop->name : 'Sin cultivo' }}</td>
                <td>{{ $task->plot ? $task->plot->name : 'Sin lote' }}</td>
                <td class="text-right">
                    @if($task->hours > 0)
                        {{ number_format($task->hours, 2) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">
                    @if($task->kilos > 0)
                        {{ number_format($task->kilos, 3) }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-right font-bold">${{ number_format($task->calculated_payment ?? $task->total_payment ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">Total Acumulado:</td>
                <td class="text-right">${{ number_format($totalPayment ?? 0, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
    <p style="text-align: center; color: #6b7280; padding: 20px;">No hay tareas aprobadas para este trabajador.</p>
    @endif

    <div class="footer">
        <p>AGROSAC - Sistema de Gestión Agrícola</p>
        <p>Documento generado automáticamente el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
