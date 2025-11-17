<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - AGROSAC</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            background-color: #f5f5f5;
        }
        
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            background-color: #4CAF50;
            padding: 25px 30px;
            text-align: center;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.25);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid rgba(255, 255, 255, 0.4);
            flex-shrink: 0;
        }
        
        .logo-icon span {
            font-size: 24px;
        }
        
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .content {
            padding: 50px 40px;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 20px;
        }
        
        .message {
            color: #555555;
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 30px;
        }
        
        .role-info {
            text-align: center;
            margin: 30px 0;
        }
        
        .role-badge {
            display: inline-block;
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .button-wrapper {
            text-align: center;
            margin: 40px 0;
        }
        
        .reset-button {
            display: inline-block;
            background-color: #4CAF50;
            color: #ffffff !important;
            text-decoration: none;
            padding: 18px 50px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.3px;
            transition: background-color 0.3s ease;
        }
        
        .reset-button:hover {
            background-color: #45a049;
        }
        
        .info-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .info-item {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid #4CAF50;
        }
        
        .info-item p {
            color: #555555;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }
        
        .info-item strong {
            color: #333333;
        }
        
        .link-box {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
            margin-top: 15px;
            word-break: break-all;
        }
        
        .link-box a {
            color: #4CAF50;
            text-decoration: none;
            font-size: 13px;
            word-break: break-all;
        }
        
        .footer {
            background-color: #2c3e50;
            padding: 30px;
            text-align: center;
        }
        
        .footer-brand {
            color: #68d391;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .footer-text {
            color: #a0aec0;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 10px;
        }
        
        .footer-copyright {
            color: #718096;
            font-size: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 20px auto;
                border-radius: 0;
            }
            
            .header {
                padding: 20px 20px;
            }
            
            .header-content {
                flex-direction: column;
                gap: 8px;
            }
            
            .header h1 {
                font-size: 22px;
            }
            
            .content {
                padding: 40px 25px;
            }
            
            .reset-button {
                padding: 16px 40px;
                font-size: 15px;
            }
            
            .header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <div class="logo-icon">
                    <span>🌱</span>
                </div>
                <h1>AGROSAC</h1>
            </div>
        </div>
        
        <!-- Content -->
        <div class="content">
            <div class="greeting">
                ¡Hola{{ $notifiable->name ? ', ' . $notifiable->name : '' }}!
            </div>
            
            <div class="message">
                <p>Has recibido este correo porque solicitaste recuperar tu contraseña en AGROSAC.</p>
            </div>
            
            <div class="role-info">
                <span class="role-badge">Rol: {{ ucfirst($notifiable->role ?? 'usuario') }}</span>
            </div>
            
            <div class="button-wrapper">
                <a href="{{ $url }}" class="reset-button">Recuperar Contraseña</a>
            </div>
            
            <div class="info-section">
                <div class="info-item">
                    <p><strong>⏰ Este enlace expirará en 60 minutos</strong> por seguridad.</p>
                </div>
                
                <div class="info-item">
                    <p><strong>Si no puedes hacer clic en el botón</strong>, copia y pega este enlace en tu navegador:</p>
                    <div class="link-box">
                        <a href="{{ $url }}">{{ $url }}</a>
                    </div>
                </div>
                
                <div class="info-item">
                    <p><strong>Si no solicitaste este cambio</strong>, puedes ignorar este correo de forma segura. Tu cuenta permanecerá sin cambios.</p>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-brand">AGROSAC</div>
            <div class="footer-text">
                Software de Administración Contable Rural Organizado
            </div>
            <div class="footer-copyright">
                &copy; {{ date('Y') }} AGROSAC. Todos los derechos reservados.
            </div>
        </div>
    </div>
</body>
</html>
