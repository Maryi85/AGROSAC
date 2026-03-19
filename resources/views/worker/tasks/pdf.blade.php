<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mis Tareas - AGROSAC</title>
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
        .badge-in_progress { background: #dbeafe; color: #1e40af; }
        .badge-completed { background: #dcfce7; color: #166534; }
        .badge-approved { background: #dcfce7; color: #166534; }
        .badge-rejected { background: #fef2f2; color: #991b1b; }
        .badge-cancelled { background: #f3f4f6; color: #374151; }

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
                    <div style="font-size: 14pt; font-weight: bold; color: #333;">MIS TAREAS</div>
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
                        <span class="summary-label">Total Tareas:</span>
                        <span class="summary-value">{{ $tasks->count() }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Data Table -->
    @if($tasks->count() > 0)
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Fecha</th>
                <th style="width: 15%;">Tipo</th>
                <th style="width: 25%;">Descripción</th>
                <th style="width: 15%;">Lote/Cultivo</th>
                <th style="width: 15%; text-align: center;">Estado</th>
                <th style="width: 15%; text-align: right;">Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td>{{ $task->scheduled_for ? \Carbon\Carbon::parse($task->scheduled_for)->format('d/m/Y') : 'N/A' }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $task->type ?? 'N/A')) }}</td>
                <td>{{ $task->description ?: 'Sin descripción' }}</td>
                <td>
                    <div style="font-weight: bold;">{{ $task->plot ? $task->plot->name : 'Sin lote' }}</div>
                    <div style="font-size: 8pt; color: #666;">{{ $task->crop ? $task->crop->name : 'Sin cultivo' }}</div>
                </td>
                <td class="text-center">
                    <span class="badge badge-{{ $task->status }}">
                        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                    </span>
                </td>
                <td class="text-right">
                    @if($task->hours > 0)
                        <div>{{ number_format($task->hours, 0) }} hrs</div>
                    @endif
                    @if($task->kilos > 0)
                        <div>{{ number_format($task->kilos, 0) }} kg</div>
                    @endif
                    @if($task->hours == 0 && $task->kilos == 0)
                        <span style="color: #999;">-</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
        <div class="text-center" style="padding: 20px; color: #666; border: 1px dashed #ccc; border-radius: 8px;">
            No tienes tareas registradas.
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
