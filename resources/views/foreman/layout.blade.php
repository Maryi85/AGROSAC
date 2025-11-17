<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mayordomo | AGROSAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('status'))
        <meta name="app-status" content="{{ session('status') }}">
    @endif
</head>
<body class="min-h-screen bg-gray-50 text-[#1b1b18]">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white border-r border-gray-200 p-4 flex flex-col shadow-sm">
            
            <div class="mb-4 px-2 text-center">
                <div class="uppercase tracking-wide text-black font-extrabold text-2xl">AGROSAC</div>
                <div class="text-sm font-medium text-black">Mayordomo</div>
            </div>
            <nav class="space-y-1 flex-1">
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.index') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-gray-300 hover:bg-gray-100 text-black' }}" href="{{ route('foreman.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-black"></i>
                        <span>Dashboard</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.tasks.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('foreman.tasks.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-black"></i>
                        <span>Tareas</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.workers.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('foreman.workers.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-black"></i>
                        <span>Trabajadores</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.tools.*') || request()->routeIs('foreman.tool-entries.*') || request()->routeIs('foreman.tool-damage.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('foreman.tools.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="wrench" class="w-5 h-5 text-black"></i>
                        <span>Inventario</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.loans.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('foreman.loans.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="arrow-left-right" class="w-5 h-5 text-black"></i>
                        <span>Préstamos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.supplies.*') || request()->routeIs('foreman.supply-movements.*') || request()->routeIs('foreman.supply-consumptions.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('foreman.supplies.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="flask-round" class="w-5 h-5 text-black"></i>
                        <span>Insumos</span>
                    </span>
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">
            <header class="border-b border-gray-200 bg-white backdrop-blur text-black shadow-sm">
                <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
                    <div class="flex-1">
                        <style>
                            header h2 { color: black !important; }
                            header div[class*="text-emerald"], header div[class*="text-gray"], header div[class*="text-slate"] { color: black !important; }
                        </style>
                        @yield('header')
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 text-sm text-black">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span>{{ auth()->user()->name ?? 'Mayordomo' }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="button" id="logout-btn" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white hover:text-white bg-amber-900/60 hover:bg-amber-900/70 rounded-lg transition-colors backdrop-blur-sm border border-amber-900/50">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>
            <main class="flex-1 max-w-6xl mx-auto px-4 py-6 w-full">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
<script>
    if (window.lucide) {
        window.lucide.createIcons();
    }
    // SweetAlert2 - Confirmaciones genéricas para formularios con data-confirm
    document.addEventListener('DOMContentLoaded', () => {
        // Interceptar botón de logout
        const logoutBtn = document.getElementById('logout-btn');
        const logoutForm = document.getElementById('logout-form');
        
        if (logoutBtn && logoutForm) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Cerrar sesión?',
                    text: '¿Estás seguro de que deseas cerrar tu sesión?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cerrar sesión',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-lg bg-white',
                        confirmButton: 'px-4 py-2 rounded bg-red-500 hover:bg-red-600 text-white border border-red-600 transition-colors',
                        cancelButton: 'px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-100 ml-2',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit();
                    }
                });
            });
        }
        
        document.querySelectorAll('form[data-confirm="true"]').forEach((form) => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const message = form.getAttribute('data-message') || '¿Confirmar acción?';
                Swal.fire({
                    title: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-lg bg-white',
                        confirmButton: 'px-4 py-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white border border-emerald-600 transition-colors',
                        cancelButton: 'px-4 py-2 rounded border border-emerald-300 text-emerald-700 hover:bg-emerald-100 ml-2',
                    },
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Toast de estado
        const status = document.querySelector('meta[name="app-status"]')?.getAttribute('content');
        if (status) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: { popup: 'rounded-lg border border-emerald-200 bg-white' },
            });
            Toast.fire({ icon: 'success', title: status });
        }
    });
</script>
