<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperar Contraseña - AGROSAC</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px;
            background: #1a1a1a;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('{{ asset("recuperacion.jpg") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            filter: blur(8px);
            transform: scale(1.1);
            z-index: 0;
        }
        
        body::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1;
        }
        
        .reset-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .reset-card {
            background: rgba(20, 20, 20, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2.5rem 2rem;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            overflow: visible;
            width: 100%;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }
        
        .logo-text {
            font-size: 1.75rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .reset-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
            text-align: center;
            letter-spacing: -0.5px;
        }
        
        .reset-subtitle {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 0;
            font-weight: 400;
            line-height: 1.4;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.2px;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 0.875rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }
        
        .form-input:focus {
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.15);
        }
        
        .form-input:hover {
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.12);
        }
        
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
            font-weight: 400;
        }
        
        .reset-button {
            width: 100%;
            padding: 0.75rem 1.25rem;
            background: #000000;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
            margin-top: 0.5rem;
        }
        
        .reset-button:hover {
            background: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }
        
        .reset-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .back-to-login {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .back-text {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.5rem;
            font-weight: 400;
        }
        
        .back-link {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: underline;
        }
        
        .error-message {
            color: #ff6b6b;
            font-size: 0.8rem;
            margin-top: 0.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255, 107, 107, 0.1);
            padding: 0.4rem 0.5rem;
            border-radius: 6px;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .error-message::before {
            content: '⚠';
            font-size: 1rem;
        }
        
        .success-message {
            color: #4CAF50;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(76, 175, 80, 0.1);
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }
        
        .success-message::before {
            content: '✓';
            font-size: 1rem;
        }
        
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .reset-container {
                margin: 10px auto;
                padding: 0 15px;
            }
            
            .reset-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
            
            .logo-text {
                font-size: 1.5rem;
            }
            
            .reset-title {
                font-size: 1.25rem;
            }
            
            .reset-subtitle {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="logo-section">
                <div class="logo">
                    <span class="logo-text">AGROSAC</span>
                </div>
                <h1 class="reset-title">Recuperar Contraseña</h1>
                <p class="reset-subtitle">Ingresa tu correo electrónico para recibir un enlace de recuperación</p>
            </div>
            
            @if (session('status'))
                <div class="success-message">
                    {{ session('status') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="form-input" placeholder="tu@email.com" />
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" class="reset-button">
                    Enviar Enlace de Recuperación
                </button>
            </form>
            
            <div class="back-to-login">
                <p class="back-text">¿Recordaste tu contraseña?</p>
                <a href="{{ route('login') }}" class="back-link">Volver al inicio de sesión</a>
            </div>
        </div>
    </div>
</body>
</html>

