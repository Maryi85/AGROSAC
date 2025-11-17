<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Herramientas - AGROSAC</title>
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
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-operational { background: #dcfce7; color: #166534; }
        .badge-damaged { background: #fef2f2; color: #991b1b; }
        .badge-lost { background: #f3f4f6; color: #374151; }
        .badge-retired { background: #fef3c7; color: #92400e; }
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
        <p>Reporte de Herramientas</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Cantidad Total</th>
                <th>Cantidad Disponible</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tools as $tool)
                <tr>
                    <td>{{ $tool->id }}</td>
                    <td>{{ $tool->name }}</td>
                    <td>{{ $tool->category ?? 'N/A' }}</td>
                    <td>{{ $tool->total_qty ?? 0 }}</td>
                    <td>{{ $tool->available_qty ?? 0 }}</td>
                    <td>
                        <span class="badge badge-{{ $tool->status }}">
                            {{ $statuses[$tool->status] ?? ucfirst($tool->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No hay herramientas registradas</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total de herramientas: {{ $tools->count() }}</p>
        <p>AGROSAC - Sistema de Gestión Agrícola</p>
    </div>
</body>
</html>

