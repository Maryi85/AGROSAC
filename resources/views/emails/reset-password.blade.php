<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - AGROSAC</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333333;
        }
        .email-wrapper {
            width: 100%;
            background-color: #f4f7f6;
            padding: 40px 0;
        }
        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .email-header {
            background-color: #ffffff;
            padding: 30px;
            text-align: center;
            border-bottom: 3px solid #10b981; /* Emerald 500 */
        }
        .logo-img {
            max-width: 180px;
            height: auto;
        }
        .email-body {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 22px;
            font-weight: 600;
            color: #065f46; /* Emerald 800 */
            margin-bottom: 20px;
            text-align: center;
        }
        .message-text {
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563; /* Gray 600 */
            margin-bottom: 30px;
            text-align: center;
        }
        .role-badge {
            display: inline-block;
            background-color: #ecfdf5; /* Emerald 50 */
            color: #059669; /* Emerald 600 */
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 30px;
            border: 1px solid #d1fae5;
        }
        .action-button {
            display: block;
            width: 220px;
            margin: 0 auto;
            background-color: #10b981; /* Emerald 500 */
            color: #ffffff;
            text-align: center;
            padding: 14px 0;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .action-button:hover {
            background-color: #059669; /* Emerald 600 */
        }
        .info-box {
            background-color: #f8fafc;
            border-left: 4px solid #3b82f6; /* Blue 500 */
            padding: 15px;
            border-radius: 4px;
            margin: 30px 0;
            font-size: 14px;
            color: #1e293b;
        }
        .warning-box {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
            color: #92400e;
        }
        .link-container {
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 15px;
            margin-top: 10px;
            word-break: break-all;
            font-size: 12px;
            color: #10b981;
        }
        .link-container a {
            color: #10b981;
            text-decoration: none;
        }
        .help-text {
            font-size: 14px;
            color: #6b7280;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        .email-footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #f3f4f6;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                {{-- Logo del software --}}
                <img src="{{ $message->embed(public_path('AGROSACLOGO.png')) }}" alt="AGROSAC" class="logo-img">
            </div>
            
            <div class="email-body">
                <h1 class="greeting">¡Hola{{ $notifiable->name ? ', ' . $notifiable->name : '' }}!</h1>
                
                <p class="message-text">
                    Has recibido este correo porque solicitaste recuperar tu contraseña en <strong>AGROSAC</strong>.
                </p>

                @if(isset($notifiable->role))
                <div style="text-align: center;">
                    <span class="role-badge">Rol: {{ ucfirst($notifiable->role) }}</span>
                </div>
                @endif
                
                <a href="{{ $url }}" class="action-button">Recuperar Contraseña</a>

                <div class="separator" style="margin: 30px 0; border-top: 1px solid #e5e7eb;"></div>

                <div class="info-box">
                    <strong>⏰ Este enlace expirará en 60 minutos</strong> por seguridad.
                </div>

                <div class="warning-box" style="background-color: #f8fafc; border-left: 4px solid #10b981; color: #334155;">
                    <p style="margin: 0 0 10px 0;"><strong>Si no puedes hacer clic en el botón</strong>, copia y pega este enlace en tu navegador:</p>
                    <div class="link-container">
                        <a href="{{ $url }}">{{ $url }}</a>
                    </div>
                </div>

                <div class="warning-box" style="margin-top: 20px;">
                    <strong>Si no solicitaste este cambio</strong>, puedes ignorar este correo de forma segura. Tu cuenta permanecerá sin cambios.
                </div>
            </div>

            <div class="email-footer">
                <p>&copy; {{ date('Y') }} AGROSAC. Todos los derechos reservados.</p>
                <p>Software de Administración Contable Rural Organizado</p>
            </div>
        </div>
    </div>
</body>
</html>
