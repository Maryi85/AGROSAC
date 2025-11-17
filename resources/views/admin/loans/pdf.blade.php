<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Préstamos - AGROSAC</title>
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
        .badge-approved { background: #dbeafe; color: #1e40af; }
        .badge-rejected { background: #fef2f2; color: #991b1b; }
        .badge-out { background: #dcfce7; color: #166534; }
        .badge-returned { background: #dcfce7; color: #166534; }
        .badge-lost { background: #f3f4f6; color: #374151; }
        .badge-damaged { background: #fef2f2; color: #991b1b; }
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
        <p>Reporte de Préstamos</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Herramienta</th>
                <th>Trabajador</th>
                <th>Cantidad</th>
                <th>Estado</th>
                <th>Fecha de Préstamo</th>
                <th>Fecha de Devolución</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
                <tr>
                    <td>{{ $loan->id }}</td>
                    <td>{{ $loan->tool->name ?? 'N/A' }}</td>
                    <td>{{ $loan->user->name ?? 'N/A' }}</td>
                    <td>{{ $loan->quantity }}</td>
                    <td>
                        <span class="badge badge-{{ $loan->status }}">
                            {{ $statuses[$loan->status] ?? ucfirst($loan->status) }}
                        </span>
                    </td>
                    <td>{{ $loan->out_at ? \Carbon\Carbon::parse($loan->out_at)->format('d/m/Y') : 'N/A' }}</td>
                    <td>{{ $loan->returned_at ? \Carbon\Carbon::parse($loan->returned_at)->format('d/m/Y') : 'Pendiente' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No hay préstamos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total de préstamos: {{ $loans->count() }}</p>
        <p>AGROSAC - Sistema de Gestión Agrícola</p>
    </div>
</body>
</html>
