<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Trabajador | SACRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('status'))
        <meta name="app-status" content="{{ session('status') }}">
    @endif
    @if (session('error'))
        <meta name="app-error" content="{{ session('error') }}">
    @endif
</head>
<body class="min-h-screen bg-emerald-50 text-[#1b1b18]">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white border-r border-emerald-200 p-4 flex flex-col fixed h-screen">
            <div class="mb-6 px-2">
                <div class="text-sm uppercase tracking-wide text-emerald-600">SACRO</div>
                <div class="text-base font-semibold text-emerald-700">Trabajador</div>
            </div>
            <nav class="space-y-1 flex-1">
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100 {{ request()->routeIs('worker.index') ? 'border-emerald-200 bg-emerald-100' : '' }}" href="{{ route('worker.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-emerald-600"></i>
                        <span>Dashboard</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100 {{ request()->routeIs('worker.tasks.*') ? 'border-emerald-200 bg-emerald-100' : '' }}" href="{{ route('worker.tasks') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-emerald-600"></i>
                        <span>Mis Tareas</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100 {{ request()->routeIs('worker.tools.*') ? 'border-emerald-200 bg-emerald-100' : '' }}" href="{{ route('worker.tools') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="wrench" class="w-5 h-5 text-emerald-600"></i>
                        <span>Herramientas</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100 {{ request()->routeIs('worker.reports.*') ? 'border-emerald-200 bg-emerald-100' : '' }}" href="{{ route('worker.reports') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                        <span>Reportes</span>
                    </span>
                </a>
            </nav>
            <div class="mt-auto pt-4 border-t border-emerald-100">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full px-3 py-2 border border-emerald-300 text-emerald-700 rounded hover:bg-emerald-100 transition-colors">
                        <span class="inline-flex items-center justify-center gap-2">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span>Salir</span>
                        </span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col ml-64">
            <header class="border-b bg-white/90 backdrop-blur">
                <div class="max-w-6xl mx-auto px-4 py-3">
                    @yield('header')
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
                        popup: 'rounded-lg',
                        confirmButton: 'px-4 py-2 rounded bg-emerald-600 text-white hover:bg-emerald-700',
                        cancelButton: 'px-4 py-2 rounded border border-emerald-300 text-emerald-700 hover:bg-emerald-100 ml-2',
                    },
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // Toast de estado exitoso
        const status = document.querySelector('meta[name="app-status"]')?.getAttribute('content');
        if (status) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true,
                customClass: { popup: 'rounded-lg border border-emerald-200' },
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
                customClass: { popup: 'rounded-lg border border-red-200' },
            });
            Toast.fire({ icon: 'error', title: error });
        }
    });
</script>
