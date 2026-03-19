<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Trabajador - {{ $worker->name }}</title>
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
        .text-green { color: #059669; }
        .bg-green-light { background-color: #ecfdf5; }
        .text-blue { color: #2563eb; }
        .bg-blue-light { background-color: #eff6ff; }
        .text-orange { color: #d97706; }
        .bg-orange-light { background-color: #fffbeb; }
        .text-purple { color: #7c3aed; }
        .bg-purple-light { background-color: #f5f3ff; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .text-xs { font-size: 8pt; }

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

        /* --- User Info --- */
        .user-info-table {
            width: 100%;
            margin-bottom: 25px;
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e5e7eb;
        }
        .user-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 2px;
        }
        .user-value {
            font-size: 11pt;
            font-weight: bold;
            color: #111;
        }

        /* --- KPIs Cards --- */
        .kpi-table {
            width: 100%;
            border-spacing: 10px;
            margin-bottom: 25px;
        }
        .kpi-card {
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            width: 25%;
            vertical-align: middle;
        }
        .kpi-label {
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #555;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .kpi-value {
            font-size: 14pt;
            font-weight: bold;
        }
        
        /* --- Detailed Table --- */
        .details-section {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            color: #111;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-left: 5px solid #10b981;
            padding-left: 10px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }
        .data-table th {
            background-color: #10b981; /* Standardized Green */
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
            vertical-align: top;
        }
        .data-table tr:nth-child(even) { background-color: #f9fafb; }
        
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
        }
        .badge-green { background-color: #d1fae5; color: #065f46; }
        .badge-gray { background-color: #f3f4f6; color: #374151; }

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
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td style="width: 60px; vertical-align: middle;">
                    <!-- Logo -->
                   <img src="{{ public_path('AGROSACLOGO.png') }}" class="header-logo" alt="Logo">
                </td>
                <td style="vertical-align: middle; padding-left: 15px;">
                    <h1 class="header-title">AGROSAC</h1>
                    <div class="header-subtitle">Reporte Individual de Trabajador</div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    <div style="font-size: 14pt; font-weight: bold; color: #333;">REPORTE DE RENDIMIENTO</div>
                    <div style="font-size: 9pt; color: #666;">Generado: {{ now()->format('d/m/Y H:i') }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- User Info -->
    <div class="user-info-table">
        <table style="width: 100%;">
            <tr>
                 <td style="width: 40%;">
                    <div class="user-label">Trabajador</div>
                    <div class="user-value">{{ $worker->name }}</div>
                    <div style="font-size: 9pt; color: #666;">{{ $worker->email }}</div>
                </td>
                <td style="width: 20%;">
                    <div class="user-label">Estado</div>
                    <div>
                        <span class="badge {{ $worker->email_verified_at ? 'badge-green' : 'badge-gray' }}">
                            {{ $worker->email_verified_at ? 'ACTIVO' : 'INACTIVO' }}
                        </span>
                    </div>
                </td>
                <td style="width: 20%;">
                    <div class="user-label">Fecha Registro</div>
                    <div class="user-value" style="font-size: 10pt;">{{ $worker->created_at->format('d/m/Y') }}</div>
                </td>
                <td style="width: 20%; text-align: right;">
                    <div class="user-label">Rol</div>
                    <div class="user-value" style="font-size: 10pt;">TRABAJADOR</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- KPIs Cards -->
    <table class="kpi-table">
        <tr>
            <!-- Tareas -->
            <td class="kpi-card bg-blue-light">
                <div class="kpi-label text-blue">Total Tareas</div>
                <div class="kpi-value text-blue">{{ $totalTasks }}</div>
            </td>
            <!-- Horas -->
            <td class="kpi-card bg-orange-light">
                <div class="kpi-label text-orange">Total Horas</div>
                <div class="kpi-value text-orange">{{ number_format($totalHours ?? 0, 0) }}</div>
            </td>
            <!-- Kilos -->
            <td class="kpi-card bg-purple-light">
                <div class="kpi-label text-purple">Total Kilos</div>
                <div class="kpi-value text-purple">{{ number_format($totalKilos ?? 0, 3) }}</div>
            </td>
            <!-- Total -->
            <td class="kpi-card bg-green-light" style="border: 1px solid #10b981;">
                <div class="kpi-label text-green">Total Acumulado</div>
                <div class="kpi-value text-green">${{ number_format($totalPayment ?? 0, 0) }}</div>
            </td>
        </tr>
    </table>

    <!-- Resumen por Cultivo -->
    @if(count($cropTotals) > 0)
    <div class="details-section">
        <div class="section-title">Resumen por Cultivo</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cultivo</th>
                    <th class="text-right">Tareas</th>
                    <th class="text-right">Horas</th>
                    <th class="text-right">Kilos</th>
                    <th class="text-right">Total Generado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cropTotals as $cropTotal)
                <tr>
                    <td style="font-weight: bold;">{{ $cropTotal['crop'] }}</td>
                    <td class="text-right">{{ $cropTotal['tasks_count'] }}</td>
                    <td class="text-right">{{ number_format($cropTotal['total_hours'], 0) }}</td>
                    <td class="text-right">{{ number_format($cropTotal['total_kilos'], 3) }}</td>
                    <td class="text-right font-bold text-green">${{ number_format($cropTotal['total_payment'] ?? 0, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Detalle de Tareas -->
    @if($tasks->count() > 0)
    <div class="details-section">
        <div class="section-title">Detalle de Actividades</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 15%;">Fecha</th>
                    <th style="width: 25%;">Actividad / Ubicación</th>
                    <th style="width: 25%;">Descripción</th>
                    <th class="text-right" style="width: 10%;">Medición</th>
                    <th class="text-right" style="width: 10%;">Tarifa</th>
                    <th class="text-right" style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr>
                    <td>{{ $task->scheduled_for->format('d/m/Y') }}</td>
                    <td>
                        <div style="font-weight: bold; font-size: 8pt; margin-bottom: 2px;">
                            {{ ucfirst(str_replace('_', ' ', $task->type)) }}
                        </div>
                        <div style="color: #666; font-size: 8pt;">
                            {{ $task->plot ? $task->plot->name : 'Sin lote' }} - {{ $task->crop ? $task->crop->name : 'Sin cultivo' }}
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 8pt; color: #444;">{{ $task->description ?: '—' }}</div>
                    </td>
                    <td class="text-right">
                        @if($task->hours > 0)
                            <div>{{ number_format($task->hours, 0) }} h</div>
                        @endif
                        @if($task->kilos > 0)
                            <div>{{ number_format($task->kilos, 3) }} kg</div>
                        @endif
                        @if($task->hours <= 0 && $task->kilos <= 0)
                            -
                        @endif
                    </td>
                    <td class="text-right" style="font-size: 8pt;">
                        @if((int)$task->price_per_hour)
                            ${{ number_format((int)$task->price_per_hour, 0) }}/h
                        @elseif((int)$task->price_per_day)
                            ${{ number_format((int)$task->price_per_day, 0) }}/d
                        @elseif((int)$task->price_per_kg)
                            ${{ number_format((int)$task->price_per_kg, 0) }}/kg
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right font-bold text-green">
                        ${{ number_format($task->calculated_payment ?? $task->total_payment ?? 0, 0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Total Summary Box -->
        <div style="margin-top: 15px; text-align: right;">
            <div style="display: inline-block; background-color: #ecfdf5; padding: 10px 20px; border-radius: 6px; border: 1px solid #10b981;">
                <span style="font-size: 10pt; font-weight: bold; color: #065f46; margin-right: 15px;">TOTAL ACUMULADO:</span>
                <span style="font-size: 14pt; font-weight: bold; color: #059669;">${{ number_format($totalPayment ?? 0, 0) }}</span>
            </div>
        </div>
    </div>
    @else
    <div style="padding: 30px; text-align: center; color: #666; font-style: italic; background-color: #f9fafb; margin-top: 20px; border-radius: 8px;">
        No hay registros de tareas aprobadas para este trabajador.
    </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        AGROSAC - Sistema de Gestión Agrícola &copy; {{ date('Y') }}
        <br>
        Este documento es un reporte oficial de rendimiento y pagos.
    </div>

</body>
</html>
