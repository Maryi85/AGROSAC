<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Lotes - AGROSAC</title>
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
            font-size: 10px;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 10px;
            vertical-align: top;
        }
        .crop-info {
            margin-bottom: 3px;
            line-height: 1.4;
        }
        .crop-name {
            font-weight: bold;
            color: #166534;
        }
        .crop-variety {
            color: #666;
            font-size: 9px;
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
        <p>Reporte de Lotes</p>
        <p>Generado el: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Ubicación</th>
                <th>Área (hectáreas)</th>
                <th>Cultivos</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($plots as $plot)
                <tr>
                    <td>{{ $plot->id }}</td>
                    <td>{{ $plot->name }}</td>
                    <td>{{ $plot->location ?? 'N/A' }}</td>
                    <td>{{ number_format($plot->area ?? 0, 2) }}</td>
                    <td>
                        @if($plot->crops && $plot->crops->count() > 0)
                            @foreach($plot->crops as $crop)
                                <div style="margin-bottom: 4px;">
                                    <strong>{{ $crop->name }}</strong>
                                    @if($crop->variety)
                                        <span style="color: #666; font-size: 10px;">({{ $crop->variety }})</span>
                                    @endif
                                    @if($crop->status === 'active')
                                        <span class="badge badge-success" style="margin-left: 5px;">Activo</span>
                                    @else
                                        <span class="badge badge-danger" style="margin-left: 5px;">Inactivo</span>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <span style="color: #999;">Sin cultivo</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $plot->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                            {{ $plot->status === 'active' ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">No hay lotes registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Total de lotes: {{ $plots->count() }}</p>
        <p>AGROSAC - Sistema de Gestión Agrícola</p>
    </div>
</body>
</html>
