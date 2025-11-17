<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Tareas - AGROSAC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background: #10b981;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        table td {
            padding: 6px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background: #f9fafb;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-progress { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fef2f2; color: #991b1b; }
        .badge-invalid { background: #f3f4f6; color: #374151; }
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
        <p>Reporte de Tareas</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Trabajador</th>
                <th>Lote</th>
                <th>Cultivo</th>
                <th>Fecha Programada</th>
                <th>Estado</th>
                <th>Pago Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td>{{ $task->id }}</td>
                    <td>{{ Str::limit($task->description, 40) }}</td>
                    <td>{{ $task->assignee->name ?? 'Sin asignar' }}</td>
                    <td>{{ $task->plot->name ?? 'N/A' }}</td>
                    <td>{{ $task->crop->name ?? 'N/A' }}</td>
                    <td>{{ $task->scheduled_for ? \Carbon\Carbon::parse($task->scheduled_for)->format('d/m/Y') : 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ str_replace('_', '-', $task->status) }}">
                            {{ $statuses[$task->status] ?? ucfirst($task->status) }}
                        </span>
                    </td>
                    <td>${{ number_format($task->total_payment ?? 0, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No hay tareas registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total de tareas: {{ $tasks->count() }}</p>
        <p>AGROSAC - Sistema de Gestión Agrícola</p>
    </div>
</body>
</html>
