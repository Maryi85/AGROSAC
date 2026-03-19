<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mis Herramientas - AGROSAC</title>
    <style>
        @page {
            margin: 10mm 15mm;
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }

        /* --- Colors --- */
        /* Corporate Green: #10b981 */
        
        /* --- Utilities --- */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* --- Header --- */
        .header {
            width: 100%;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #10b981;
        }
        .header-table { width: 100%; }
        .header-logo { width: 60px; height: auto; }
        .header-title { 
            font-size: 18pt; 
            font-weight: bold; 
            color: #10b981; 
            margin: 0;
        }
        .header-subtitle { 
            font-size: 10pt; 
            color: #666; 
            margin-top: 5px;
        }

        /* --- Data Table --- */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            margin-bottom: 20px;
        }
        .data-table th {
            background-color: #10b981;
            color: #fff;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .data-table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        
        /* --- Badges --- */
        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            display: inline-block;
        }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-approved { background: #dbeafe; color: #1e40af; }
        .badge-out { background: #dcfce7; color: #166534; }
        .badge-returned { background: #dcfce7; color: #166534; }
        .badge-returned_by_worker { background: #fef3c7; color: #92400e; }
        .badge-lost { background: #fee2e2; color: #991b1b; }
        .badge-damaged { background: #fee2e2; color: #991b1b; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }

        /* --- Footer --- */
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }

        /* --- Summary Section --- */
        .summary-box {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            width: 50%;
        }
        .summary-title {
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 5px;
        }
        .summary-row {
            display: block;
            margin-bottom: 5px;
        }
        .summary-label { color: #6b7280; }
        .summary-value { font-weight: bold; color: #111; float: right; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 60px; vertical-align: middle;">
                    <img src="{{ public_path('AGROSACLOGO.png') }}" class="header-logo" alt="Logo">
                </td>
                <td style="vertical-align: middle; padding-left: 15px;">
                    <h1 class="header-title">AGROSAC</h1>
                    <div class="header-subtitle">Sistema de Gestión Agrícola</div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div style="font-size: 14pt; font-weight: bold; color: #333;">MIS HERRAMIENTAS</div>
                    <div style="font-size: 9pt; color: #666;">Generado: {{ now()->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Summary -->
    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td style="vertical-align: top; width: 60%;">
            </td>
            <td style="vertical-align: top; width: 40%;">
                <div class="summary-box" style="width: 100%;">
                    <div class="summary-title">Resumen</div>
                    <div class="summary-row">
                        <span class="summary-label">Total Préstamos:</span>
                        <span class="summary-value">{{ $myLoans->count() }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    @if($myLoans->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Fecha Préstamo</th>
                <th style="width: 15%;">Fecha Dev.</th>
                <th style="width: 25%;">Herramienta</th>
                <th style="width: 10%; text-align: center;">Cant.</th>
                <th style="width: 15%; text-align: center;">Estado</th>
                <th style="width: 20%;">Notas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($myLoans as $loan)
            <tr>
                <td>{{ $loan->out_at ? $loan->out_at->format('d/m/Y') : ($loan->created_at->format('d/m/Y')) }}</td>
                <td>{{ $loan->due_at ? $loan->due_at->format('d/m/Y') : '-' }}</td>
                <td>
                    <div class="font-bold">{{ $loan->tool ? $loan->tool->name : 'Herramienta no disponible' }}</div>
                </td>
                <td class="text-center">{{ $loan->quantity }}</td>
                <td class="text-center">
                    <span class="badge badge-{{ str_replace('_', '-', $loan->status) }}">
                        @if($loan->status === 'out')
                            Prestado
                        @elseif($loan->status === 'pending')
                            Pendiente
                        @elseif($loan->status === 'returned')
                            Devuelto
                        @elseif($loan->status === 'returned_by_worker')
                            Devuelto (Pendiente)
                        @else
                            {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                        @endif
                    </span>
                </td>
                <td style="font-size: 8pt; color: #666;">
                    {{ $loan->request_notes ?: '-' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="text-center" style="padding: 20px; color: #666; border: 1px dashed #ccc; border-radius: 8px;">
            No tienes préstamos registrados.
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        AGROSAC - Sistema de Gestión Agrícola &copy; {{ date('Y') }}
        <br>
        Este documento es un reporte generado automáticamente.
    </div>

</body>
</html>
