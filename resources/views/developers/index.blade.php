<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desarrolladores - AGROSAC</title>
    <link rel="icon" href="{{ asset('AGROSACLOGO.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes floatLeaf {
            0%, 100% { transform: translateY(0) rotate(-6deg); }
            50%       { transform: translateY(-10px) rotate(6deg); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .fade-in-up { animation: fadeInUp 0.6s ease both; }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }

        .hero-bg {
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(16,185,129,.18) 0%, transparent 70%),
                linear-gradient(160deg, #f0fdf4 0%, #ecfdf5 40%, #f8fafc 100%);
            position: relative;
            overflow: hidden;
        }
        .hero-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(circle, rgba(16,185,129,.07) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
        }

        .leaf-deco {
            animation: floatLeaf 5s ease-in-out infinite;
            filter: drop-shadow(0 4px 12px rgba(16,185,129,.25));
        }

        .dev-card {
            position: relative;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.35s cubic-bezier(.4,0,.2,1), box-shadow 0.35s cubic-bezier(.4,0,.2,1), border-color 0.35s ease;
        }
        .dev-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #059669, #34d399);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
            opacity: 0;
            transition: opacity 0.35s ease;
        }
        .dev-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px -8px rgba(16,185,129,.18); border-color: #a7f3d0; }
        .dev-card:hover::before { opacity: 1; }

        .avatar-ring {
            padding: 3px;
            background: linear-gradient(135deg, #10b981, #059669);
            border-radius: 9999px;
            display: inline-block;
            box-shadow: 0 0 0 4px rgba(16,185,129,.12);
        }
        .avatar-img {
            width: 88px; height: 88px;
            border-radius: 9999px;
            border: 3px solid white;
            object-fit: cover;
            display: block;
        }

        .social-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px;
            border-radius: 10px;
            background: #f1f5f9; border: 1px solid #e2e8f0; color: #64748b;
            transition: background 0.25s, color 0.25s, transform 0.25s, border-color 0.25s;
            text-decoration: none;
        }
        .social-btn:hover { background: #ecfdf5; color: #059669; border-color: #a7f3d0; transform: translateY(-2px); }

        .tech-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: 9999px; border: 1.5px solid;
            font-size: 0.8125rem; font-weight: 600;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            cursor: default;
        }
        .tech-badge:hover { transform: translateY(-3px) scale(1.04); box-shadow: 0 6px 20px -4px rgba(0,0,0,.12); }

        .section-title { position: relative; display: inline-block; }
        .section-title::after {
            content: '';
            position: absolute; bottom: -6px; left: 50%; transform: translateX(-50%);
            width: 60%; height: 3px; border-radius: 9999px;
            background: linear-gradient(90deg, #10b981, #34d399);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

    {{-- Botón volver al inicio --}}
    <div class="fixed top-4 right-4 z-50">
        <a href="{{ url('/') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-full shadow-lg transition-all hover:shadow-xl hover:-translate-y-0.5">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 12H5M12 5l-7 7 7 7"/>
            </svg>
            Volver al inicio
        </a>
    </div>

    <div class="max-w-6xl mx-auto px-6 py-10">

        {{-- ── Hero ── --}}
        <div class="rounded-2xl hero-bg px-6 py-14 mb-8 text-center">
            <div class="flex justify-center mb-5">
                <img src="{{ asset('AGROSACLOGO.png') }}" alt="AGROSAC Logo" style="height:80px;width:auto;">
            </div>
            <h1 class="fade-in-up text-3xl sm:text-4xl font-extrabold text-gray-800 leading-tight">
                Conoce al equipo detrás de
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-green-500">AGROSAC</span>
            </h1>
            <p class="fade-in-up delay-1 mt-4 text-gray-500 max-w-xl mx-auto text-base sm:text-lg">
                Transformando la gestión agrícola a través de la tecnología, con pasión por el software y compromiso con el campo colombiano.
            </p>
            <div class="fade-in-up delay-2 mt-8 inline-flex flex-wrap justify-center gap-6 sm:gap-10 py-4 px-6 bg-white/70 backdrop-blur border border-emerald-100 rounded-2xl shadow-sm">
                <div class="text-center"><p class="text-2xl font-black text-emerald-700">3</p><p class="text-xs text-gray-500 font-medium">Desarrolladores</p></div>
                <div class="w-px bg-gray-200 hidden sm:block"></div>
                <div class="text-center"><p class="text-2xl font-black text-emerald-700">14</p><p class="text-xs text-gray-500 font-medium">Tecnologías</p></div>
                <div class="w-px bg-gray-200 hidden sm:block"></div>
                <div class="text-center"><p class="text-2xl font-black text-emerald-700">2026</p><p class="text-xs text-gray-500 font-medium">Año de lanzamiento</p></div>
            </div>
        </div>

        {{-- ── Team Grid ── --}}
        <div class="mb-10">
            <div class="text-center mb-8 fade-in-up delay-1">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full mb-3">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    Nuestro Equipo
                </span>
                <h2 class="section-title text-2xl font-bold text-gray-800">Los artífices del sistema</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Developer 1 --}}
                <div class="dev-card p-6 fade-in-up delay-1">
                    <div class="relative text-center mb-5">

                        <div class="flex justify-center mb-4">
                            <div class="avatar-ring">
                                <img src="{{ asset('images/developer/Maryi.jpeg') }}" alt="Maryi Gaviria Perdomo" class="avatar-img">
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Maryi Gaviria Perdomo</h3>
                        <p class="text-sm text-emerald-600 font-medium mt-0.5">Desarrolladora de Software</p>
                    </div>
                    <p class="text-xs text-gray-500 text-center leading-relaxed mb-5">
                        Arquitecto de la solución, responsable del módulo de administración, APIs REST y la integración con Mapbox.
                    </p>
                    <div class="flex justify-center gap-2">
                        <a href="https://github.com/Maryi85" target="_blank" rel="noopener" class="social-btn" title="GitHub">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.645.35-1.085.636-1.334-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0 1 12 6.836c.85.004 1.705.114 2.504.337 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.202 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/in/maryi-perdomo-235a253aa" target="_blank" rel="noopener" class="social-btn" title="LinkedIn">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Developer 2 --}}
                <div class="dev-card p-6 fade-in-up delay-2">
                    <div class="relative text-center mb-5">
                        <div class="flex justify-center mb-4">
                            <div class="avatar-ring">
                                <img src="{{ asset('images/developer/Heidy.jpeg') }}" alt="Heidy Daniela Suaza" class="avatar-img">
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Heidy Daniela Suaza</h3>
                        <p class="text-sm text-emerald-600 font-medium mt-0.5">Desarrolladora de Software</p>
                    </div>
                    <p class="text-xs text-gray-500 text-center leading-relaxed mb-5">
                        Responsable del sistema de diseño, experiencia de usuario y la implementación de interfaces con Tailwind CSS & Alpine.js.
                    </p>
                    <div class="flex justify-center gap-2">
                        <a href="https://github.com/heidysuaza" target="_blank" rel="noopener" class="social-btn" title="GitHub">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.645.35-1.085.636-1.334-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0 1 12 6.836c.85.004 1.705.114 2.504.337 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.202 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/in/heidy-daniela-suaza-pel%C3%A1ez-45aa323aa/" target="_blank" rel="noopener" class="social-btn" title="LinkedIn">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Developer 3 --}}
                <div class="dev-card p-6 fade-in-up delay-3">
                    <div class="relative text-center mb-5">

                        <div class="flex justify-center mb-4">
                            <div class="avatar-ring">
                                <img src="{{ asset('images/developer/Byron.png') }}" alt="Byron Falla Suaza" class="avatar-img">
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-800">Byron Falla Suaza</h3>
                        <p class="text-sm text-emerald-600 font-medium mt-0.5">Desarrollador de Software</p>
                    </div>
                    <p class="text-xs text-gray-500 text-center leading-relaxed mb-5">
                        Encargado del modelado de la base de datos, lógica de negocio, generación de reportes PDF con DomPDF y autenticación.
                    </p>
                    <div class="flex justify-center gap-2">
                        <a href="https://github.com/Byronsuaza" target="_blank" rel="noopener" class="social-btn" title="GitHub">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.865 8.17 6.839 9.49.5.092.682-.217.682-.482 0-.237-.008-.866-.013-1.7-2.782.603-3.369-1.34-3.369-1.34-.454-1.156-1.11-1.463-1.11-1.463-.908-.62.069-.608.069-.608 1.003.07 1.531 1.03 1.531 1.03.892 1.529 2.341 1.087 2.91.831.092-.645.35-1.085.636-1.334-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.27.098-2.647 0 0 .84-.269 2.75 1.025A9.578 9.578 0 0 1 12 6.836c.85.004 1.705.114 2.504.337 1.909-1.294 2.747-1.025 2.747-1.025.546 1.377.202 2.394.1 2.647.64.699 1.028 1.592 1.028 2.683 0 3.842-2.339 4.687-4.566 4.935.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48C19.138 20.167 22 16.418 22 12c0-5.523-4.477-10-10-10z"/></svg>
                        </a>
                        <a href="https://www.linkedin.com/in/byron-eduardo-falla-suaza-9a233539a/" target="_blank" rel="noopener" class="social-btn" title="LinkedIn">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── Tech Stack ── --}}
        <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-sm fade-in-up delay-4 mb-6">
            <div class="text-center mb-8">
                <span class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full mb-3">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="4" width="16" height="16" rx="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="2" x2="9" y2="4"/><line x1="15" y1="2" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="22"/><line x1="15" y1="20" x2="15" y2="22"/><line x1="20" y1="9" x2="22" y2="9"/><line x1="20" y1="14" x2="22" y2="14"/><line x1="2" y1="9" x2="4" y2="9"/><line x1="2" y1="14" x2="4" y2="14"/></svg>
                    Tech Stack
                </span>
                <h2 class="section-title text-2xl font-bold text-gray-800">Tecnologías que impulsan AGROSAC</h2>
                <p class="text-sm text-gray-500 mt-3">Una selección cuidadosa de herramientas modernas y robustas</p>
            </div>

            <style>
                .tech-tile {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    background: white;
                    border: 1px solid #e5e7eb;
                    border-radius: 20px;
                    padding: 20px 14px 14px;
                    width: 108px;
                    cursor: default;
                    transition: transform 0.3s cubic-bezier(.4,0,.2,1), box-shadow 0.3s cubic-bezier(.4,0,.2,1), border-color 0.3s ease;
                }
                .tech-tile:hover { transform: translateY(-8px) scale(1.04); border-color: transparent; }
                .tech-icon-box {
                    width: 64px; height: 64px;
                    border-radius: 16px;
                    display: flex; align-items: center; justify-content: center;
                    box-shadow: 0 6px 16px -4px rgba(0,0,0,.22);
                    overflow: hidden;
                    margin-bottom: 12px;
                    flex-shrink: 0;
                    transition: transform 0.3s ease;
                }
                .tech-tile:hover .tech-icon-box { transform: scale(1.1); }
                .tech-icon-box img { width: 38px; height: 38px; object-fit: contain; }
                .tech-tile-label {
                    font-size: 0.68rem; font-weight: 700;
                    color: #374151; text-align: center; line-height: 1.25;
                    letter-spacing: 0.01em;
                }
                /* Brand glow on hover */
                .tech-tile[data-glow="red"]:hover    { box-shadow: 0 16px 32px -8px rgba(227,79,38,.4); }
                .tech-tile[data-glow="orange"]:hover { box-shadow: 0 16px 32px -8px rgba(255,45,32,.4); }
                .tech-tile[data-glow="purple"]:hover { box-shadow: 0 16px 32px -8px rgba(79,91,147,.4); }
                .tech-tile[data-glow="blue"]:hover   { box-shadow: 0 16px 32px -8px rgba(21,114,182,.4); }
                .tech-tile[data-glow="teal"]:hover   { box-shadow: 0 16px 32px -8px rgba(13,148,136,.4); }
                .tech-tile[data-glow="cyan"]:hover   { box-shadow: 0 16px 32px -8px rgba(31,186,214,.4); }
                .tech-tile[data-glow="green"]:hover  { box-shadow: 0 16px 32px -8px rgba(29,53,87,.4); }
                .tech-tile[data-glow="yellow"]:hover { box-shadow: 0 16px 32px -8px rgba(200,170,0,.45); }
                .tech-tile[data-glow="dark"]:hover   { box-shadow: 0 16px 32px -8px rgba(24,23,23,.45); }
                .tech-tile[data-glow="slate"]:hover  { box-shadow: 0 16px 32px -8px rgba(82,141,211,.4); }
                .tech-tile[data-glow="sky"]:hover    { box-shadow: 0 16px 32px -8px rgba(10,116,218,.4); }
                .tech-tile[data-glow="white"]:hover  { box-shadow: 0 16px 32px -8px rgba(100,116,139,.3); }
            </style>

            <div class="flex flex-wrap justify-center gap-4">

                {{-- Laravel --}}
                <div class="tech-tile" data-glow="orange">
                    <div class="tech-icon-box" style="background:#FF2D20;">
                        <img src="https://cdn.simpleicons.org/laravel/ffffff" alt="Laravel">
                    </div>
                    <p class="tech-tile-label">Laravel 12</p>
                </div>

                {{-- PHP --}}
                <div class="tech-tile" data-glow="purple">
                    <div class="tech-icon-box" style="background:#4F5B93;">
                        <img src="https://cdn.simpleicons.org/php/ffffff" alt="PHP">
                    </div>
                    <p class="tech-tile-label">PHP 8.2</p>
                </div>

                {{-- MySQL --}}
                <div class="tech-tile" data-glow="blue">
                    <div class="tech-icon-box" style="background:#00618A;">
                        <img src="https://cdn.simpleicons.org/mysql/ffffff" alt="MySQL">
                    </div>
                    <p class="tech-tile-label">MySQL</p>
                </div>

                {{-- Tailwind --}}
                <div class="tech-tile" data-glow="dark">
                    <div class="tech-icon-box" style="background:#0F172A;">
                        <img src="https://cdn.simpleicons.org/tailwindcss/ffffff" alt="Tailwind CSS">
                    </div>
                    <p class="tech-tile-label">Tailwind CSS</p>
                </div>

                {{-- Alpine.js --}}
                <div class="tech-tile" data-glow="green">
                    <div class="tech-icon-box" style="background:#1D3557;">
                        <svg viewBox="0 0 25 27" width="38" height="38" fill="white">
                            <path d="M0 13.5L6.75 0l18.25 13.5L18.25 27 6.75 27 0 13.5z" opacity=".45"/>
                            <path d="M6.75 27L0 13.5 12.5 13.5 6.75 27z"/>
                            <path d="M12.5 13.5L25 13.5 18.25 27 12.5 13.5z"/>
                        </svg>
                    </div>
                    <p class="tech-tile-label">Alpine.js</p>
                </div>

                {{-- DomPDF --}}
                <div class="tech-tile" data-glow="red">
                    <div class="tech-icon-box" style="background:#C2410C;">
                        <svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="white" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <line x1="10" y1="9" x2="8" y2="9"/>
                        </svg>
                    </div>
                    <p class="tech-tile-label">DomPDF</p>
                </div>

                {{-- Mapbox --}}
                <div class="tech-tile" data-glow="cyan">
                    <div class="tech-icon-box" style="background:#1FBAD6;">
                        <img src="https://icon-icons.com/icons2/2699/PNG/512/mapbox_logo_icon_169974.png" alt="Mapbox GL" style="width:40px;height:40px;object-fit:contain;">
                    </div>
                    <p class="tech-tile-label">Mapbox GL</p>
                </div>

                {{-- HTML5 --}}
                <div class="tech-tile" data-glow="red">
                    <div class="tech-icon-box" style="background:#E34F26;">
                        <img src="https://cdn.simpleicons.org/html5/ffffff" alt="HTML5">
                    </div>
                    <p class="tech-tile-label">HTML5</p>
                </div>

                {{-- CSS3 --}}
                <div class="tech-tile" data-glow="blue">
                    <div class="tech-icon-box" style="background:#1572B6;">
                        <img src="https://cdn.jsdelivr.net/gh/devicons/devicon@v2.16.0/icons/css3/css3-original.svg" alt="CSS3">
                    </div>
                    <p class="tech-tile-label">CSS3</p>
                </div>

                {{-- JavaScript --}}
                <div class="tech-tile" data-glow="yellow">
                    <div class="tech-icon-box" style="background:#F7DF1E;">
                        <img src="https://cdn.simpleicons.org/javascript/000000" alt="JavaScript">
                    </div>
                    <p class="tech-tile-label">JavaScript</p>
                </div>

                {{-- GitHub --}}
                <div class="tech-tile" data-glow="dark">
                    <div class="tech-icon-box" style="background:#181717;">
                        <img src="https://cdn.simpleicons.org/github/ffffff" alt="GitHub">
                    </div>
                    <p class="tech-tile-label">GitHub</p>
                </div>

                {{-- Font Awesome --}}
                <div class="tech-tile" data-glow="slate">
                    <div class="tech-icon-box" style="background:#528DD3;">
                        <img src="https://cdn.simpleicons.org/fontawesome/ffffff" alt="Font Awesome">
                    </div>
                    <p class="tech-tile-label">Font Awesome</p>
                </div>

                {{-- SweetAlert2 --}}
                <div class="tech-tile" data-glow="white">
                    <div class="tech-icon-box" style="background:#fff; border:1px solid #e5e7eb;">
                        <img src="https://raw.githubusercontent.com/DmitriyRusov/DmitriyRusov/main/libraries/sweetalert.png" alt="SweetAlert2" style="width:42px;height:42px;object-fit:contain;">
                    </div>
                    <p class="tech-tile-label">SweetAlert2</p>
                </div>

                {{-- Laragon --}}
                <div class="tech-tile" data-glow="sky">
                    <div class="tech-icon-box" style="background:#0A74DA;">
                        <img src="https://cdn.simpleicons.org/laragon/ffffff" alt="Laragon">
                    </div>
                    <p class="tech-tile-label">Laragon</p>
                </div>

            </div>
        </div>

    </div>{{-- /tech stack section --}}



    </div>{{-- /max-w --}}
</body>
</html>
