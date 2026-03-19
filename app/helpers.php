<?php

if (!function_exists('route_prefix')) {
    /**
     * Get the route prefix based on the current route
     */
    function route_prefix(): string
    {
        $route = request()->route();
        if (!$route) {
            return 'admin.';
        }

        $routeName = $route->getName();
        if (str_starts_with($routeName, 'foreman.')) {
            return 'foreman.';
        }

        return 'admin.';
    }
}

if (!function_exists('upload_base_path')) {
    /**
     * Devuelve la ruta base pública para subidas de archivos.
     * En Hostinger, apunta a public_html; en local usa public_path().
     */
    function upload_base_path(string $path = ''): string
    {
        $base = config('filesystems.upload_public_path') ?: public_path();
        $base = rtrim($base, '/\\');
        if ($path === '') return $base;
        return $base . '/' . ltrim(str_replace('\\', '/', $path), '/');
    }
}

if (!function_exists('storage_asset')) {
    /**
     * Genera la URL de una imagen almacenada en storage,
     * usando la ruta /storage-file/ para evitar problemas de symlink
     * en hosting compartido (Hostinger).
     */
    function storage_asset(string $path): string
    {
        return asset('storage-file/' . ltrim(str_replace('\\', '/', $path), '/'));
    }
}
