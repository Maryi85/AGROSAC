<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mis Herramientas - AGROSAC</title>
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
        .badge-out { background: #d1fae5; color: #065f46; }
        .badge-returned { background: #d1fae5; color: #065f46; }
        .badge-returned_by_worker { background: #fef3c7; color: #92400e; }
        .badge-lost { background: #fee2e2; color: #991b1b; }
        .badge-damaged { background: #fee2e2; color: #991b1b; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>AGROSAC</h1>
        <p>Reporte de Mis Herramientas Prestadas</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    @if($myLoans->count() > 0)
    <table>
        <thead>
            <tr>
                <th>Herramienta</th>
                <th>Cantidad</th>
                <th>Fecha Préstamo</th>
                <th>Fecha Devolución</th>
                <th>Estado</th>
                <th>Notas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($myLoans as $loan)
            <tr>
                <td>{{ $loan->tool ? $loan->tool->name : 'Herramienta no disponible' }}</td>
                <td class="text-center">{{ $loan->quantity }}</td>
                <td>{{ $loan->out_at ? $loan->out_at->format('d/m/Y') : ($loan->created_at->format('d/m/Y')) }}</td>
                <td>{{ $loan->due_at ? $loan->due_at->format('d/m/Y') : '-' }}</td>
                <td>
                    <span class="badge badge-{{ str_replace('_', '-', $loan->status) }}">
                        @if($loan->status === 'out')
                            Prestado
                        @elseif($loan->status === 'pending')
                            Pendiente
                        @elseif($loan->status === 'returned')
                            Devuelto
                        @elseif($loan->status === 'returned_by_worker')
                            Devuelto (Pendiente Confirmación)
                        @else
                            {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                        @endif
                    </span>
                </td>
                <td>{{ $loan->request_notes ?: '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p style="text-align: center; color: #666; margin-top: 30px;">No hay préstamos registrados.</p>
    @endif
</body>
</html>





