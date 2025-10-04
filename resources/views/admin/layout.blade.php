<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin | SACRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('status'))
        <meta name="app-status" content="{{ session('status') }}">
    @endif
</head>
<body class="min-h-screen bg-emerald-50 text-[#1b1b18]">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white border-r border-emerald-200 p-4 flex flex-col">
            <div class="mb-4 px-2">
                <div class="text-sm uppercase tracking-wide text-emerald-600">SACRO</div>
                <div class="text-base font-semibold text-emerald-700">Administrador</div>
            </div>
            <nav class="space-y-1 flex-1">
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="layout-dashboard" class="w-5 h-5 text-emerald-600"></i>
                        <span>Dashboard</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.foremen.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="user-cog" class="w-5 h-5 text-emerald-600"></i>
                        <span>Mayordomos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.workers.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="users" class="w-5 h-5 text-emerald-600"></i>
                        <span>Trabajadores</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.plots.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="grid-3x3" class="w-5 h-5 text-emerald-600"></i>
                        <span>Lotes</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.crops.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="sprout" class="w-5 h-5 text-emerald-600"></i>
                        <span>Cultivos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.tasks.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="clipboard-check" class="w-5 h-5 text-emerald-600"></i>
                        <span>Tareas</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.tools.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="wrench" class="w-5 h-5 text-emerald-600"></i>
                        <span>Inventario</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.loans.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="arrow-left-right" class="w-5 h-5 text-emerald-600"></i>
                        <span>Préstamos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.supplies.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="flask-round" class="w-5 h-5 text-emerald-600"></i>
                        <span>Insumos</span>
                    </span>
                </a>
                <a class="block px-3 py-2 rounded border border-transparent hover:border-emerald-200 hover:bg-emerald-100" href="{{ route('admin.ledger.index') }}">
                    <span class="inline-flex items-center gap-2">
                        <i data-lucide="banknote" class="w-5 h-5 text-emerald-600"></i>
                        <span>Contable</span>
                    </span>
                </a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button class="w-full px-3 py-2 border border-emerald-300 text-emerald-700 rounded hover:bg-emerald-100">Salir</button>
            </form>
        </aside>

        <div class="flex-1 flex flex-col">
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

        // Toast de estado
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
    });
</script>


