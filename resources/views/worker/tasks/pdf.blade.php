<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mis Tareas - AGROSAC</title>
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
        .badge-in_progress { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-approved { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AGROSAC</h1>
        <p>Reporte de Mis Tareas</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($tasks->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Fecha Programada</th>
                <th>Tipo</th>
                <th>Descripción</th>
                <th>Lote</th>
                <th>Cultivo</th>
                <th>Estado</th>
                <th class="text-right">Horas</th>
                <th class="text-right">Kilos</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td>{{ $task->scheduled_for ? $task->scheduled_for->format('d/m/Y') : '-' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $task->type ?? 'N/A')) }}</td>
                <td>{{ $task->description ?: 'Sin descripción' }}</td>
                <td>{{ $task->plot ? $task->plot->name : 'Sin lote' }}</td>
                <td>{{ $task->crop ? $task->crop->name : 'Sin cultivo' }}</td>
                <td>
                    <span class="badge badge-{{ str_replace('_', '-', $task->status) }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </td>
                <td class="text-right">{{ $task->hours > 0 ? number_format($task->hours, 2) : '-' }}</td>
                <td class="text-right">{{ $task->kilos > 0 ? number_format($task->kilos, 3) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; color: #666; margin-top: 30px;">No hay tareas registradas.</p>
    @endif
</body>
</html>




