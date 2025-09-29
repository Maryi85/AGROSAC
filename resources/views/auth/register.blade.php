<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-emerald-50 flex items-center justify-center p-6">
    <div class="w-full max-w-md bg-white shadow rounded-lg p-6 border border-emerald-200">
        <h1 class="text-xl font-semibold mb-4 text-emerald-700">Crear cuenta</h1>
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Nombre</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border border-emerald-200 focus:border-emerald-400 focus:ring-0 rounded px-3 py-2" />
                @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Correo</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-emerald-200 focus:border-emerald-400 focus:ring-0 rounded px-3 py-2" />
                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Contraseña</label>
                <input type="password" name="password" required class="w-full border border-emerald-200 focus:border-emerald-400 focus:ring-0 rounded px-3 py-2" />
                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm mb-1 text-emerald-800">Confirmar contraseña</label>
                <input type="password" name="password_confirmation" required class="w-full border border-emerald-200 focus:border-emerald-400 focus:ring-0 rounded px-3 py-2" />
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white rounded py-2">Registrarme</button>
        </form>
        <p class="text-sm text-center mt-4 text-emerald-800">¿Ya tienes cuenta? <a class="underline text-emerald-700 hover:text-emerald-800" href="{{ route('login') }}">Inicia sesión</a></p>
    </div>
</body>
</html>


