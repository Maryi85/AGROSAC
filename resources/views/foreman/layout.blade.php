<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('AGROSACLOGO.png') }}">
    <title>Mayordomo | AGROSAC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Alpine.js Plugins -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('status'))
        <meta name="app-status" content="{{ session('status') }}">
    @endif
    @if (session('error'))
        <meta name="app-error" content="{{ session('error') }}">
    @endif
</head>
<body class="h-[100dvh] overflow-hidden bg-gray-50 text-[#1b1b18]" x-data="{ sidebarOpen: false }">
    <div class="flex h-full">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             x-transition:enter="transition-opacity ease-linear duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="transition-opacity ease-linear duration-300" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" style="display: none;"></div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 p-4 flex flex-col shadow-sm transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto lg:flex lg:flex-shrink-0 lg:overflow-y-auto">
            
            <div class="mb-6 px-2 text-center flex justify-between items-center lg:block">
                <div class="flex flex-col items-center gap-1 w-full">
                    <img src="{{ asset('AGROSACLOGO.png') }}" alt="AGROSAC Logo" class="w-20 h-20 lg:w-28 lg:h-28 object-contain">
                </div>
                <!-- Close button for mobile -->
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <nav class="space-y-1 flex-1 overflow-y-auto">
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
                <div x-data="{ open: {{ request()->routeIs('foreman.tools.*') || request()->routeIs('foreman.tool-entries.*') || request()->routeIs('foreman.tool-damage.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.tools.*') || request()->routeIs('foreman.tool-entries.*') || request()->routeIs('foreman.tool-damage.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}">
                        <span class="inline-flex items-center gap-2">
                            <i data-lucide="wrench" class="w-5 h-5 text-black"></i>
                            <span>Inventario</span>
                        </span>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                        <a class="block px-3 py-2 rounded border text-sm transition-colors {{ request()->routeIs('foreman.tools.index') || (request()->routeIs('foreman.tools.*') && !request()->routeIs('foreman.tool-entries.*') && !request()->routeIs('foreman.tool-damage.*')) ? 'border-emerald-400 bg-emerald-50 text-black' : 'border-transparent hover:border-emerald-300 hover:bg-emerald-100 text-black' }}" href="{{ route('foreman.tools.index') }}">
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="list" class="w-4 h-4 text-black"></i>
                                <span>Lista de Herramientas</span>
                            </span>
                        </a>
                        <a class="block px-3 py-2 rounded border text-sm transition-colors {{ request()->routeIs('foreman.tool-entries.*') ? 'border-emerald-400 bg-emerald-50 text-black' : 'border-transparent hover:border-emerald-300 hover:bg-emerald-100 text-black' }}" href="{{ route('foreman.tool-entries.index') }}">
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="package" class="w-4 h-4 text-black"></i>
                                <span>Gestionar Entradas</span>
                            </span>
                        </a>
                        <a class="block px-3 py-2 rounded border text-sm transition-colors {{ request()->routeIs('foreman.tool-damage.*') ? 'border-emerald-400 bg-emerald-50 text-black' : 'border-transparent hover:border-emerald-300 hover:bg-emerald-100 text-black' }}" href="{{ route('foreman.tool-damage.index') }}">
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="alert-triangle" class="w-4 h-4 text-black"></i>
                                <span>Daños y Pérdidas</span>
                            </span>
                        </a>
                    </div>
                </div>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.loans.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('foreman.loans.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="arrow-left-right" class="w-5 h-5 text-black"></i>
                        <span>Préstamos</span>
                    </span>
                </a>
                
                <!-- Insumos Dropdown -->
                <div x-data="{ open: {{ request()->routeIs('foreman.supplies.*') || request()->routeIs('foreman.supply-movements.*') || request()->routeIs('foreman.supply-consumptions.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 rounded border transition-colors {{ request()->routeIs('foreman.supplies.*') || request()->routeIs('foreman.supply-movements.*') || request()->routeIs('foreman.supply-consumptions.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}">
                        <span class="inline-flex items-center gap-2">
                            <i data-lucide="flask-round" class="w-5 h-5 text-black"></i>
                            <span>Insumos</span>
                        </span>
                        <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    <div x-show="open" x-collapse class="ml-4 mt-1 space-y-1">
                        <a class="block px-3 py-2 rounded border text-sm transition-colors {{ request()->routeIs('foreman.supplies.index') || (request()->routeIs('foreman.supplies.*') && !request()->routeIs('foreman.supply-movements.*') && !request()->routeIs('foreman.supply-consumptions.*')) ? 'border-emerald-400 bg-emerald-50 text-black' : 'border-transparent hover:border-emerald-300 hover:bg-emerald-100 text-black' }}" href="{{ route('foreman.supplies.index') }}">
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="list" class="w-4 h-4 text-black"></i>
                                <span>Lista de Insumos</span>
                            </span>
                        </a>
                        <a class="block px-3 py-2 rounded border text-sm transition-colors {{ request()->routeIs('foreman.supply-movements.*') ? 'border-emerald-400 bg-emerald-50 text-black' : 'border-transparent hover:border-emerald-300 hover:bg-emerald-100 text-black' }}" href="{{ route('foreman.supply-movements.index') }}">
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="arrow-right-left" class="w-4 h-4 text-black"></i>
                                <span>Entradas/Salidas</span>
                            </span>
                        </a>
                        <a class="block px-3 py-2 rounded border text-sm transition-colors {{ request()->routeIs('foreman.supply-consumptions.*') ? 'border-emerald-400 bg-emerald-50 text-black' : 'border-transparent hover:border-emerald-300 hover:bg-emerald-100 text-black' }}" href="{{ route('foreman.supply-consumptions.index') }}">
                            <span class="inline-flex items-center gap-2">
                                <i data-lucide="bar-chart" class="w-4 h-4 text-black"></i>
                                <span>Ver Consumos</span>
                            </span>
                        </a>
                    </div>
                </div>


            </nav>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="border-b border-gray-200 bg-white backdrop-blur text-black shadow-sm z-30">
                <div class="w-full px-6 py-3 flex items-center justify-between">
                    <div class="flex-1 flex items-center gap-3">
                        <!-- Botón Hamburguesa (Solo Mobile) -->
                        <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none bg-gray-100 p-2 rounded-md">
                            <i data-lucide="menu" class="w-6 h-6"></i>
                        </button>

                        <div class="flex-1">
                            <style>
                                header h2 { color: black !important; }
                                header div[class*="text-emerald"], header div[class*="text-gray"], header div[class*="text-slate"] { color: black !important; }
                            </style>
                            @yield('header')
                        </div>
                    </div>
                    <div class="flex items-center gap-2 lg:gap-4 ml-2">
                        <div class="hidden sm:flex items-center gap-2 text-sm text-black">
                            @if(auth()->user()->photo)
                                <img src="{{ storage_asset(auth()->user()->photo) }}" 
                                     alt="{{ auth()->user()->name }}" 
                                     class="w-8 h-8 rounded-full object-cover border-2 border-gray-200">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                                </div>
                            @endif
                            <span>{{ auth()->user()->name ?? 'Mayordomo' }}</span>
                        </div>
                        {{-- Botón Ayuda --}}
                        <a href="{{ asset('manuals/foreman.pdf') }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-emerald-700 hover:text-white hover:bg-emerald-600 rounded-lg transition-colors border border-emerald-300 hover:border-emerald-600">
                            <i data-lucide="circle-help" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Ayuda</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="button" id="logout-btn" class="inline-flex items-center gap-2 px-3 lg:px-4 py-2 text-sm font-medium text-white hover:text-white bg-amber-900/60 hover:bg-amber-900/70 rounded-lg transition-colors backdrop-blur-sm border border-amber-900/50">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span class="hidden sm:inline">Cerrar Sesión</span>
                            </button>
                        </form>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto w-full p-6">
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

    // Forzar recarga si se regresa con el botón atrás (evita bfcache)
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });

    // SweetAlert2 - Sistema centralizado de confirmaciones y alertas
    document.addEventListener('DOMContentLoaded', () => {
        // Configuración global de SweetAlert2
        const swalConfig = {
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-lg bg-white',
                confirmButton: 'px-4 py-2 rounded bg-emerald-500 hover:bg-emerald-600 text-white border border-emerald-600 transition-colors',
                cancelButton: 'px-4 py-2 rounded border border-gray-300 text-gray-700 hover:bg-gray-100 ml-2',
            },
        };

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
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    ...swalConfig,
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit();
                    }
                });
            });
        }
        
        // Confirmaciones para formularios con data-confirm
        document.querySelectorAll('form[data-confirm="true"]').forEach((form) => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const message = form.getAttribute('data-message') || '¿Confirmar acción?';
                const type = form.getAttribute('data-type') || 'warning';
                const confirmText = form.getAttribute('data-confirm-text') || 'Aceptar';
                const cancelText = form.getAttribute('data-cancel-text') || 'Cancelar';
                
                Swal.fire({
                    title: message,
                    icon: type,
                    showCancelButton: true,
                    confirmButtonText: confirmText,
                    cancelButtonText: cancelText,
                    reverseButtons: true,
                    ...swalConfig,
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Confirmaciones para botones con data-confirm-action
        document.querySelectorAll('[data-confirm-action]').forEach((button) => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const message = this.getAttribute('data-confirm-message') || '¿Confirmar acción?';
                const action = this.getAttribute('data-confirm-action');
                const type = this.getAttribute('data-confirm-type') || 'warning';
                
                Swal.fire({
                    title: message,
                    icon: type,
                    showCancelButton: true,
                    confirmButtonText: 'Aceptar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true,
                    ...swalConfig,
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (action === 'submit' && this.form) {
                            this.form.submit();
                        } else if (action === 'click' && this.onclick) {
                            this.onclick();
                        } else if (this.href) {
                            window.location.href = this.href;
                        }
                    }
                });
            });
        });

        // Reemplazar alert() nativos por SweetAlert2
        window.originalAlert = window.alert;
        window.alert = function(message, type = 'info') {
            Swal.fire({
                title: message,
                icon: type,
                confirmButtonText: 'Aceptar',
                ...swalConfig,
            });
        };

        // Reemplazar confirm() nativos por SweetAlert2
        window.originalConfirm = window.confirm;
        window.confirm = function(message) {
            return Swal.fire({
                title: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                ...swalConfig,
            }).then((result) => result.isConfirmed);
        };

        // Toast de estado exitoso
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

        // Toast de error
        const error = document.querySelector('meta[name="app-error"]')?.getAttribute('content');
        if (error) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'rounded-lg border border-red-200 bg-white' },
            });
            Toast.fire({ icon: 'error', title: error });
        }

        // Función global para mostrar alertas de éxito (como toast)
        window.showSuccessAlert = function(message) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: { popup: 'rounded-lg border border-emerald-200 bg-white' },
            });
            Toast.fire({ icon: 'success', title: message });
        };

        // Función global para mostrar alertas de error
        window.showErrorAlert = function(message) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message,
                confirmButtonText: 'Aceptar',
                customClass: { popup: 'rounded-lg border border-red-200 bg-white' },
            });
        };

        // Función global para confirmaciones
        window.showConfirmDialog = function(message, type = 'warning') {
            return Swal.fire({
                title: message,
                icon: type,
                showCancelButton: true,
                confirmButtonText: 'Aceptar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true,
                ...swalConfig,
            });
        };

        // Prevenir doble envío de formularios globalmente
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                const submitBtn = e.target.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    if (e.target.dataset.preventDisable === 'true') return;
                    
                    const rect = submitBtn.getBoundingClientRect();
                    if(rect.width > 0) submitBtn.style.width = rect.width + 'px';
                    
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                    
                    const span = submitBtn.querySelector('span');
                    if (span) {
                        submitBtn.dataset.originalText = span.textContent;
                        span.textContent = 'Enviando...';
                    } else if (!submitBtn.innerHTML.includes('animate-spin')) {
                        submitBtn.dataset.originalHtml = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mx-auto inline text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> ' + submitBtn.innerHTML;
                    }
                }
            }
        });
    });
</script>
@stack('scripts')
