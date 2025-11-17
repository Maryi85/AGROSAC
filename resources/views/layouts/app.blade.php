<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGROSAC - Sistema de Administración de Cultivos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">AGROSAC</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    @if(auth()->check())
                        @if(auth()->user()->role === 'foreman')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('foreman.index') ? 'active' : '' }}" href="{{ route('foreman.index') }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('foreman.tasks.*') ? 'active' : '' }}" href="{{ route('foreman.tasks.index') }}">Tareas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('foreman.workers.*') ? 'active' : '' }}" href="{{ route('foreman.workers.index') }}">Trabajadores</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('foreman.supplies.*') ? 'active' : '' }}" href="{{ route('foreman.supplies.index') }}">Insumos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('foreman.tools.*') ? 'active' : '' }}" href="{{ route('foreman.tools.index') }}">Herramientas</a>
                            </li>
                        @endif
                    @endif
                </ul>
                @if(auth()->check())
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endif
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @stack('scripts')
</body>
</html>