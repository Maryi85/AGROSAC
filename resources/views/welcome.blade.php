<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AGROSAC - Sistema de Gestión Agrícola</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                line-height: 1.6;
                color: #333;
                min-height: 100vh;
            }
            
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 0 20px;
            }
            
            .hero {
                min-height: 100vh;
                display: flex;
                align-items: center;
                position: relative;
                overflow: hidden;
            }
            
            .carousel-container {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 1;
            }
            
            .carousel-slide {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                opacity: 0;
                transition: opacity 3s ease-in-out;
            }
            
            .carousel-slide.active {
                opacity: 1;
            }
            
            .carousel-slide:nth-child(1) {
                background-image: url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            }
            
            .carousel-slide:nth-child(2) {
                background-image: url('https://images.unsplash.com/photo-1560493676-04071c5f467b?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            }
            
            .carousel-slide:nth-child(3) {
                background-image: url('https://images.unsplash.com/photo-1464226184884-fa280b87c399?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            }
            
            .carousel-slide:nth-child(4) {
                background-image: url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            }
            
            .carousel-slide:nth-child(5) {
                background-image: url('https://images.unsplash.com/photo-1574943320219-553eb213f72d?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');
            }
            
            .hero-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4));
                z-index: 2;
            }
            
            .hero-content {
                position: relative;
                z-index: 3;
                text-align: center;
                color: white;
            }
            
            .hero h1 {
                font-size: 3.5rem;
                font-weight: 700;
                margin-bottom: 1rem;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                background: linear-gradient(45deg, #fff, #f0f8ff);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            .hero p {
                font-size: 1.3rem;
                margin-bottom: 2rem;
                opacity: 0.9;
                max-width: 600px;
                margin-left: auto;
                margin-right: auto;
            }
            
            .cta-buttons {
                display: flex;
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
                margin-top: 2rem;
            }
            
            .btn {
                padding: 12px 30px;
                border: none;
                border-radius: 50px;
                font-size: 1.1rem;
                font-weight: 600;
                text-decoration: none;
                transition: all 0.3s ease;
                cursor: pointer;
                display: inline-block;
            }
            
            .btn-primary {
                background: linear-gradient(45deg, #4CAF50, #45a049);
                color: white;
                box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(76, 175, 80, 0.6);
            }
            
            .btn-secondary {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                border: 2px solid rgba(255, 255, 255, 0.3);
                backdrop-filter: blur(10px);
            }
            
            .btn-secondary:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
            }
            
            .features {
                padding: 80px 0;
                background: linear-gradient(135deg, #e8f5e8, #d4edda, #e8f5e8);
                position: relative;
                overflow: hidden;
            }
            
            .features::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="0.5" fill="%234CAF50" opacity="0.1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grain)"/></svg>');
                opacity: 0.3;
            }
            
            .features-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 1.5rem;
                margin-top: 3rem;
                position: relative;
                z-index: 2;
            }
            
            .feature-card {
                background: linear-gradient(145deg, #ffffff, #f8f9fa);
                padding: 2rem 1.5rem;
                border-radius: 18px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.08), 0 5px 15px rgba(0,0,0,0.05), inset 0 1px 0 rgba(255,255,255,0.8);
                text-align: center;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border: 1px solid rgba(76, 175, 80, 0.15);
                position: relative;
                overflow: hidden;
                backdrop-filter: blur(10px);
            }
            
            .feature-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 3px;
                background: linear-gradient(90deg, #4CAF50, #45a049, #66BB6A, #81C784);
                border-radius: 18px 18px 0 0;
                box-shadow: 0 1px 4px rgba(76, 175, 80, 0.3);
            }
            
            .feature-card::after {
                content: '';
                position: absolute;
                top: -50%;
                left: -50%;
                width: 200%;
                height: 200%;
                background: radial-gradient(circle, rgba(76, 175, 80, 0.05) 0%, transparent 70%);
                opacity: 0;
                transition: opacity 0.5s ease;
                pointer-events: none;
            }
            
            .feature-card:hover {
                transform: translateY(-12px) scale(1.03);
                box-shadow: 0 30px 60px rgba(76, 175, 80, 0.2), 0 15px 30px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.9);
                border-color: rgba(76, 175, 80, 0.4);
            }
            
            .feature-card:hover::after {
                opacity: 1;
            }
            
            .feature-icon {
                width: 70px;
                height: 70px;
                margin: 0 auto 1.5rem;
                background: linear-gradient(135deg, #4CAF50, #45a049, #66BB6A, #81C784);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.8rem;
                color: white;
                box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3), 0 3px 10px rgba(0,0,0,0.1);
                transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                position: relative;
                border: 2px solid rgba(255,255,255,0.2);
            }
            
            .feature-icon svg {
                width: 32px;
                height: 32px;
                stroke: white;
            }
            
            .feature-icon::before {
                content: '';
                position: absolute;
                top: -2px;
                left: -2px;
                right: -2px;
                bottom: -2px;
                background: linear-gradient(135deg, #4CAF50, #45a049, #66BB6A);
                border-radius: 50%;
                z-index: -1;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .feature-card:hover .feature-icon {
                transform: scale(1.1) rotate(5deg);
                box-shadow: 0 12px 35px rgba(76, 175, 80, 0.4);
            }
            
            .feature-card:hover .feature-icon::before {
                opacity: 0.3;
            }
            
            .feature-card h3 {
                font-size: 1.3rem;
                margin-bottom: 1rem;
                color: #2c3e50;
                font-weight: 700;
                letter-spacing: -0.5px;
                transition: all 0.3s ease;
                text-shadow: 0 1px 2px rgba(0,0,0,0.1);
            }
            
            .feature-card:hover h3 {
                color: #4CAF50;
                transform: translateY(-1px);
            }
            
            .feature-card p {
                color: #5a6c7d;
                line-height: 1.6;
                font-size: 0.9rem;
                font-weight: 400;
                transition: all 0.3s ease;
                text-shadow: 0 1px 2px rgba(0,0,0,0.05);
            }
            
            .feature-card:hover p {
                color: #34495e;
                transform: translateY(-1px);
            }
            
            .stats {
                background: linear-gradient(135deg, #2c3e50, #34495e);
                color: white;
                padding: 60px 0;
            }
            
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 2rem;
                text-align: center;
            }
            
            .stat-item h3 {
                font-size: 3rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                color: #4CAF50;
            }
            
            .stat-item p {
                font-size: 1.1rem;
                opacity: 0.9;
            }
            
            .floating-elements {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                overflow: hidden;
                z-index: 1;
            }
            
            .floating-element {
                position: absolute;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 50%;
                animation: float 6s ease-in-out infinite;
            }
            
            .floating-element:nth-child(1) {
                width: 80px;
                height: 80px;
                top: 20%;
                left: 10%;
                animation-delay: 0s;
            }
            
            .floating-element:nth-child(2) {
                width: 120px;
                height: 120px;
                top: 60%;
                right: 10%;
                animation-delay: 2s;
            }
            
            .floating-element:nth-child(3) {
                width: 60px;
                height: 60px;
                top: 80%;
                left: 20%;
                animation-delay: 4s;
            }
            
            @keyframes float {
                0%, 100% { transform: translateY(0px) rotate(0deg); }
                50% { transform: translateY(-20px) rotate(180deg); }
            }
            
            .navbar {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                padding: 1rem 0;
                z-index: 10;
            }
            
            .navbar-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            
            .logo {
                font-size: 1.5rem;
                font-weight: 700;
                color: white;
                text-decoration: none;
            }
            
            .nav-links {
                display: flex;
                gap: 2rem;
                list-style: none;
            }
            
            .nav-links a {
                color: white;
                text-decoration: none;
                font-weight: 500;
                transition: opacity 0.3s ease;
            }
            
            .nav-links a:hover {
                opacity: 0.8;
            }
            
            @media (max-width: 768px) {
                .hero h1 {
                    font-size: 2.5rem;
                }
                
                .hero p {
                    font-size: 1.1rem;
                }
                
                .cta-buttons {
                    flex-direction: column;
                    align-items: center;
                }
                
                .nav-links {
                    display: none;
                }
            }
            </style>
        @endif
    </head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-content">
                <a href="/" class="logo">
                    <svg style="display: inline-block; width: 24px; height: 24px; vertical-align: middle; margin-right: 8px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                    AGROSAC
                </a>
                <ul class="nav-links">
                    <li><a href="#features">Características</a></li>
                    <li><a href="#about">Acerca de</a></li>
            @if (Route::has('login'))
                    @auth
                            <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
                    @else
                            <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                            @if (Route::has('register'))
                                <li><a href="{{ route('register') }}">Registrarse</a></li>
                            @endif
                        @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="carousel-container">
            <div class="carousel-slide active"></div>
            <div class="carousel-slide"></div>
            <div class="carousel-slide"></div>
            <div class="carousel-slide"></div>
            <div class="carousel-slide"></div>
        </div>
        
        <div class="hero-overlay"></div>
        
        <div class="floating-elements">
            <div class="floating-element"></div>
            <div class="floating-element"></div>
            <div class="floating-element"></div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <h1>AGROSAC</h1>
                <p>Software de Administración Contable Rural Organizado</p>
               
                <div class="cta-buttons">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">Ir al Dashboard</a>
                        @endif
            @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2 style="text-align: center; font-size: 2.5rem; margin-bottom: 1rem; color: #333;">Características Principales</h2>
            <p style="text-align: center; font-size: 1.2rem; color: #666; margin-bottom: 3rem;">Herramientas completas para la administración contable rural</p>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <h3>Control de Ingresos</h3>
                    <p>Registra y controla todos los ingresos de tu finca agrícola de manera organizada y eficiente.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2v20M6 5h5.5a3.5 3.5 0 0 1 0 7H6M18 12h-5.5a3.5 3.5 0 0 0 0 7H18"></path>
                        </svg>
                    </div>
                    <h3>Control de Egresos</h3>
                    <p>Gestiona todos los gastos y egresos de tu operación rural con herramientas especializadas.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 7h-4M4 7h4m-4 0v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2m-6 0V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"></path>
                        </svg>
                    </div>
                    <h3>Gestión de Inventarios</h3>
                    <p>Controla inventarios de insumos, herramientas y productos agrícolas de forma centralizada.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 3v18h18M7 16l4-4 4 4 6-6"></path>
                        </svg>
                    </div>
                    <h3>Análisis Financiero</h3>
                    <p>Optimiza recursos y mejora la toma de decisiones con análisis financieros detallados.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <h3>Administración Rural</h3>
                    <p>Herramientas especializadas para la gestión administrativa en entornos rurales.</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="22 6 13.5 15.5 8.5 10.5 2 17"></polyline>
                            <polyline points="16 6 22 6 22 12"></polyline>
                        </svg>
                    </div>
                    <h3>Reportes Contables</h3>
                    <p>Genera reportes contables completos para una gestión financiera transparente.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer style="background: linear-gradient(135deg, #1a365d, #2d3748); color: white; padding: 2rem 0 1.5rem; position: relative;">
        <div class="container" style="text-align: center;">
            <div style="margin-bottom: 1rem;">
                <h3 style="color: #68d391; font-size: 1.3rem; font-weight: 700; margin-bottom: 0.5rem; letter-spacing: -0.5px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <svg style="width: 24px; height: 24px; stroke: #68d391;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                    </svg>
                    AGROSAC
                </h3>
                <p style="color: #a0aec0; font-size: 0.9rem; margin: 0; font-weight: 400;">Transformando la gestión agrícola con tecnología moderna</p>
            </div>
            
            <div style="display: flex; justify-content: center; gap: 3rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                <div style="text-align: center;">
                    <h4 style="color: #68d391; font-size: 1rem; font-weight: 600; margin-bottom: 0.3rem;">Innovación</h4>
                    <p style="color: #a0aec0; font-size: 0.8rem; margin: 0;">Tecnología de vanguardia</p>
                </div>
                <div style="text-align: center;">
                    <h4 style="color: #68d391; font-size: 1rem; font-weight: 600; margin-bottom: 0.3rem;">Confiabilidad</h4>
                    <p style="color: #a0aec0; font-size: 0.8rem; margin: 0;">Sistema seguro y estable</p>
                </div>
                <div style="text-align: center;">
                    <h4 style="color: #68d391; font-size: 1rem; font-weight: 600; margin-bottom: 0.3rem;">Eficiencia</h4>
                    <p style="color: #a0aec0; font-size: 0.8rem; margin: 0;">Optimización de recursos</p>
                </div>
        </div>

            <div style="border-top: 1px solid rgba(104, 211, 145, 0.2); padding-top: 1rem;">
                <p style="color: #718096; font-size: 0.8rem; margin: 0; font-weight: 400;">
                    &copy; {{ date('Y') }} AGROSAC - Software de Administración Contable Rural Organizado. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.carousel-slide');
            let currentSlide = 0;
            
            // Función para mostrar una slide específica
            function showSlide(index) {
                // Remover clase active de todas las slides
                slides.forEach(slide => {
                    slide.classList.remove('active');
                });
                
                // Agregar clase active a la slide actual
                if (slides[index]) {
                    slides[index].classList.add('active');
                }
            }
            
            // Función para ir a la siguiente slide
            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }
            
            // Inicializar con la primera imagen
            showSlide(0);
            
            // Cambiar slide cada 4 segundos
            setInterval(nextSlide, 4000);
        });
    </script>
    </body>
</html>