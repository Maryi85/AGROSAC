<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | AGROSAC</title>
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
                <div class="text-sm font-medium text-black">Administrador</div>
            </div>
            <nav class="space-y-1 flex-1">
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.index') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-gray-300 hover:bg-gray-100 text-black' }}" href="{{ route('admin.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-black"></i>
                        <span>Dashboard</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.foremen.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.foremen.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="user-cog" class="w-5 h-5 text-black"></i>
                        <span>Mayordomos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.workers.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.workers.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-black"></i>
                        <span>Trabajadores</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.plots.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.plots.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="grid-3x3" class="w-5 h-5 text-black"></i>
                        <span>Lotes</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.crops.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.crops.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="sprout" class="w-5 h-5 text-black"></i>
                        <span>Cultivos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.tasks.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.tasks.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-black"></i>
                        <span>Tareas</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.tools.*') || request()->routeIs('admin.tool-entries.*') || request()->routeIs('admin.tool-damage.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.tools.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="wrench" class="w-5 h-5 text-black"></i>
                        <span>Inventario</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.loans.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.loans.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="arrow-left-right" class="w-5 h-5 text-black"></i>
                        <span>Préstamos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.supplies.*') || request()->routeIs('admin.supply-movements.*') || request()->routeIs('admin.supply-consumptions.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.supplies.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="flask-round" class="w-5 h-5 text-black"></i>
                        <span>Insumos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border transition-colors {{ request()->routeIs('admin.ledger.*') ? 'border-emerald-400 bg-emerald-100 text-black' : 'border-transparent hover:border-emerald-400 hover:bg-emerald-200 text-black' }}" href="{{ route('admin.ledger.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="banknote" class="w-5 h-5 text-black"></i>
                        <span>Contable</span>
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
                            <span>{{ auth()->user()->name ?? 'Usuario' }}</span>
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

        // Función global para mostrar alertas de éxito
        window.showSuccessAlert = function(message) {
            Swal.fire({
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                customClass: { popup: 'rounded-lg border border-emerald-200 bg-white' },
            });
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
    });
</script>
@stack('scripts')


