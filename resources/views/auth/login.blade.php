<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión - AGROSAC</title>
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
            overflow: hidden;
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
            background-image: url('{{ asset("login.jpg") }}');
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
        
        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .login-card {
            background: rgba(20, 20, 20, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 3rem 2.5rem;
            box-shadow: 
                0 25px 50px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255, 255, 255, 0.1) inset;
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            overflow: hidden;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .logo-text {
            font-size: 2rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        
        .login-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 0.5rem;
            text-align: center;
            letter-spacing: -0.5px;
        }
        
        .login-subtitle {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 0;
            font-weight: 400;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 0.5rem;
            letter-spacing: -0.2px;
        }
        
        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            font-size: 0.95rem;
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
        
        .password-input-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.6);
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.3s ease;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .password-toggle svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }
        
        .password-input-wrapper .form-input {
            padding-right: 3rem;
        }
        
        .login-button {
            width: 100%;
            padding: 0.875rem 1.5rem;
            background: #000000;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            letter-spacing: 0.5px;
            margin-top: 1rem;
        }
        
        .login-button:hover {
            background: #1a1a1a;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }
        
        .login-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        
        .register-link {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .register-text {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.5rem;
            font-weight: 400;
        }
        
        .register-link a {
            color: #ffffff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .register-link a:hover {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: underline;
        }
        
        .forgot-password-link {
            text-align: right;
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .forgot-password-link a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            position: relative;
            display: inline-block;
        }
        
        .forgot-password-link a:hover {
            color: #ffffff;
            text-decoration: underline;
        }
        
        .error-message {
            color: #ff6b6b;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 107, 107, 0.1);
            padding: 0.5rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 107, 107, 0.3);
        }
        
        .error-message::before {
            content: '⚠';
            font-size: 1rem;
        }
        
        
        @media (max-width: 480px) {
            .login-card {
                padding: 2.5rem 2rem;
                border-radius: 24px;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .logo-text {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="logo-section">
                <div class="logo">
                    <span class="logo-text">AGROSAC</span>
                </div>
                <h1 class="login-title">Iniciar sesión</h1>
                <p class="login-subtitle">Accede a tu cuenta para gestionar tu finca</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}">
            @csrf
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
                    <div class="password-input-wrapper">
                        <input type="password" name="password" id="password" required 
                               class="form-input" placeholder="••••••••" />
                        <button type="button" class="password-toggle" id="togglePassword" aria-label="Mostrar contraseña">
                            <svg id="eyeIcon" viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eyeOffIcon" viewBox="0 0 24 24" style="display: none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                <line x1="1" y1="1" x2="23" y2="23"></line>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
            </div>
                
                <div class="forgot-password-link">
                    <a href="{{ route('password.request') }}">Olvidé mi contraseña</a>
                </div>
                
                <button type="submit" class="login-button">
                    Entrar
                </button>
        </form>
            
            <div class="register-link">
                <p class="register-text">¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></p>
            </div>
            </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'block';
                    togglePassword.setAttribute('aria-label', 'Ocultar contraseña');
                } else {
                    eyeIcon.style.display = 'block';
                    eyeOffIcon.style.display = 'none';
                    togglePassword.setAttribute('aria-label', 'Mostrar contraseña');
                }
            });
        });
    </script>
</body>
</html>