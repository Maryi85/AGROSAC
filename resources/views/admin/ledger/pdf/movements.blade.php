<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Movimientos Contables - AGROSAC</title>
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
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-income {
            background: #dcfce7;
            color: #166534;
        }
        .badge-expense {
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
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid #10b981;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AGROSAC</h1>
        <p>Movimientos Contables</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    @if($entries->count() > 0)
    <div class="summary">
        <strong>Total de movimientos: {{ $entries->count() }}</strong><br>
        <strong>Total ingresos: ${{ number_format($entries->where('type', 'income')->sum('amount'), 2) }}</strong><br>
        <strong>Total gastos: ${{ number_format($entries->where('type', 'expense')->sum('amount'), 2) }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Categoría</th>
                <th class="text-right">Monto</th>
                <th>Cultivo</th>
                <th>Lote</th>
                <th>Referencia</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
            <tr>
                <td>{{ $entry->occurred_at->format('d/m/Y') }}</td>
                <td>
                    <span class="badge {{ $entry->type === 'income' ? 'badge-income' : 'badge-expense' }}">
                        {{ $entry->type === 'income' ? 'Ingreso' : 'Gasto' }}
                    </span>
                </td>
                <td>{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</td>
                <td class="text-right">
                    <strong>{{ $entry->type === 'income' ? '+' : '-' }}${{ number_format($entry->amount, 2) }}</strong>
                </td>
                <td>{{ $entry->crop ? $entry->crop->name : '—' }}</td>
                <td>{{ $entry->plot ? $entry->plot->name : '—' }}</td>
                <td>{{ $entry->reference ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No hay movimientos contables para mostrar.</p>
    @endif

    <div class="footer">
        <p>Este reporte fue generado automáticamente por el sistema AGROSAC</p>
        <p>Página {PAGENO} de {nbpg}</p>
    </div>
</body>
</html>




