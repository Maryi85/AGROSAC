<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido a AGROSAC - Tus Credenciales</title>
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
        
        .credentials-box {
            background-color: #f8f9fa;
            border: 2px solid #4CAF50;
            border-radius: 8px;
            padding: 25px;
            margin: 30px 0;
        }
        
        .credentials-title {
            font-size: 18px;
            font-weight: 700;
            color: #2e7d32;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .credential-item {
            margin-bottom: 15px;
            padding: 12px;
            background-color: #ffffff;
            border-radius: 6px;
            border-left: 4px solid #4CAF50;
        }
        
        .credential-label {
            font-size: 13px;
            color: #666666;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .credential-value {
            font-size: 18px;
            color: #1a1a1a;
            font-weight: 600;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        
        .password-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        
        .password-warning p {
            color: #856404;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }
        
        .button-wrapper {
            text-align: center;
            margin: 40px 0;
        }
        
        .login-button {
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
        
        .login-button:hover {
            background-color: #45a049;
        }
        
        .info-section {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e0e0e0;
        }
        
        .info-item {
            margin-bottom: 15px;
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
            
            .login-button {
                padding: 16px 40px;
                font-size: 15px;
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
                ¡Bienvenido{{ $notifiable->name ? ', ' . $notifiable->name : '' }}!
            </div>
            
            <div class="message">
                <p>Tu cuenta de <strong>Mayordomo</strong> ha sido creada exitosamente en AGROSAC. A continuación encontrarás tus credenciales de acceso:</p>
            </div>
            
            <div class="credentials-box">
                <div class="credentials-title">Tus Credenciales de Acceso</div>
                
                <div class="credential-item">
                    <div class="credential-label">Correo Electrónico</div>
                    <div class="credential-value">{{ $notifiable->email }}</div>
                </div>
                
                <div class="credential-item">
                    <div class="credential-label">Contraseña Temporal</div>
                    <div class="credential-value">{{ $password }}</div>
                </div>
            </div>
            
            <div class="password-warning">
                <p><strong>⚠️ Importante:</strong> Por seguridad, te recomendamos cambiar esta contraseña temporal después de iniciar sesión por primera vez.</p>
            </div>
            
            <div class="button-wrapper">
                <a href="{{ $loginUrl }}" class="login-button">Iniciar Sesión</a>
            </div>
            
            <div class="info-section">
                <div class="info-item">
                    <p><strong>¿Primera vez?</strong> Utiliza las credenciales proporcionadas arriba para acceder al sistema. Una vez dentro, podrás cambiar tu contraseña desde tu perfil.</p>
                </div>
                
                <div class="info-item">
                    <p><strong>¿Necesitas ayuda?</strong> Si tienes alguna pregunta o problema para acceder, contacta al administrador del sistema.</p>
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





