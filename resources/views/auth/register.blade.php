<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro - AGROSAC</title>
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
            background-image: url('{{ asset("registroo.jpg") }}');
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
        
        .register-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 480px;
            margin: 20px auto;
            padding: 0 20px;
        }
        
        .register-card {
            background: rgba(20, 20, 20, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 1.75rem 1.75rem;
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
            margin-bottom: 1.25rem;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
        }
        
        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .register-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.25rem;
            text-align: center;
            letter-spacing: -0.5px;
        }
        
        .register-subtitle {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 0;
            font-weight: 400;
            line-height: 1.3;
        }
        
        .form-group {
            margin-bottom: 0.75rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.375rem;
            letter-spacing: -0.2px;
        }
        
        .form-input {
            width: 100%;
            padding: 0.7rem 0.875rem;
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
        
        .password-requirements {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            padding: 0.5rem;
            margin-top: 0.3rem;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .requirement {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin-bottom: 0.15rem;
        }
        
        .requirement:last-child {
            margin-bottom: 0;
        }
        
        .requirement-icon {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            color: white;
        }
        
        .requirement.valid .requirement-icon {
            background: #4CAF50;
        }
        
        .register-button {
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
            margin-top: 0.25rem;
        }
        
        .register-button:hover {
            background: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }
        
        .register-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .login-text {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.375rem;
            font-weight: 400;
        }
        
        .login-link a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .login-link a:hover {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: underline;
        }
        
        .error-message {
            color: #ff6b6b;
            font-size: 0.8rem;
            margin-top: 0.375rem;
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
        
        @media (max-width: 480px) {
            body {
                padding: 15px;
            }
            
            .register-container {
                margin: 10px auto;
                padding: 0 15px;
            }
            
            .register-card {
                padding: 2rem 1.5rem;
                border-radius: 20px;
            }
            
            .logo-section {
                margin-bottom: 1.5rem;
            }
            
            .logo-text {
                font-size: 1.5rem;
            }
            
            .register-title {
                font-size: 1.25rem;
            }
            
            .register-subtitle {
                font-size: 0.8rem;
            }
            
            .form-group {
                margin-bottom: 0.875rem;
            }
            
            .form-input {
                padding: 0.75rem 0.875rem;
                font-size: 0.9rem;
            }
            
            .register-button {
                padding: 0.75rem 1.25rem;
                font-size: 0.95rem;
            }
            
            .login-link {
                margin-top: 1.25rem;
                padding-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="logo-section">
                <div class="logo">
                    <span class="logo-text">AGROSAC</span>
                </div>
                <h1 class="register-title">Crear cuenta</h1>
                <p class="register-subtitle">Únete a AGROSAC y gestiona tu finca de manera profesional</p>
            </div>
            
            <form method="POST" action="{{ route('register') }}">
            @csrf
                <div class="form-group">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" name="name" value="{{ old('name') }}" required 
                           class="form-input" placeholder="Tu nombre completo" />
                    @error('name')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
            </div>
                
                <div class="form-group">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" value="{{ old('email') }}" required 
                           class="form-input" placeholder="tu@email.com" />
                    @error('email')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
            </div>
                
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" required 
                           class="form-input" placeholder="••••••••" />
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
            </div>
                
                <div class="form-group">
                    <label class="form-label">Confirmar contraseña</label>
                    <input type="password" name="password_confirmation" required 
                           class="form-input" placeholder="••••••••" />
            </div>
                
                <button type="submit" class="register-button">
                    Crear cuenta
                </button>
        </form>
            
            <div class="login-link">
                <p class="login-text">¿Ya tienes cuenta?</p>
                <a href="{{ route('login') }}">Inicia sesión aquí</a>
            </div>
            </div>
    </div>
</body>
</html>