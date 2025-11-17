<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Cultivos - AGROSAC</title>
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
        <p>Reporte de Cultivos</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Variedad</th>
                <th>Lote</th>
                <th>Estado</th>
                <th>Fecha de Siembra</th>
            </tr>
        </thead>
        <tbody>
            @forelse($crops as $crop)
                <tr>
                    <td>{{ $crop->id }}</td>
                    <td>{{ $crop->name }}</td>
                    <td>{{ $crop->variety ?? 'N/A' }}</td>
                    <td>{{ $crop->plot->name ?? 'Sin lote' }}</td>
                    <td>
                        <span class="badge {{ $crop->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                            {{ $crop->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td>{{ $crop->planting_date ? \Carbon\Carbon::parse($crop->planting_date)->format('d/m/Y') : 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No hay cultivos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total de cultivos: {{ $crops->count() }}</p>
        <p>AGROSAC - Sistema de Gestión Agrícola</p>
    </div>
</body>
</html>
