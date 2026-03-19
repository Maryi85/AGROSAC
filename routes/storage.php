<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// --- SISTEMA DE VISUALIZACIÓN DE IMÁGENES (HOSTINGER WORKAROUND) ---
// Esta función lee directamente el disco saltándose el symlink roto.
$serveStorageFile = function (string $path) {
    // Protección contra path traversal
    $path = str_replace(['../', '..\\'], '', $path);
    $fullPath = null;

    // 1. Intenta buscar en storage/app/public (disco 'public' de Laravel)
    if (Storage::disk('public')->exists($path)) {
        $fullPath = Storage::disk('public')->path($path);
    }

    // 2. Intenta buscar usando el Helper upload_base_path (public_html de Hostinger)
    if (!$fullPath && function_exists('upload_base_path')) {
        $publicPath = upload_base_path('storage/' . $path);
        if (file_exists($publicPath) && is_file($publicPath)) {
            $fullPath = $publicPath;
        }
    }

    // 3. Fallback: Si no existe la imagen, devuelve un ícono SVG gris (sin pantalla rota)
    if (!$fullPath || !file_exists($fullPath)) {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100">'
            . '<rect width="100" height="100" fill="#e5e7eb"/>'
            . '<path d="M30 35h40v30H30z" fill="#9ca3af"/>'
            . '<circle cx="50" cy="42" r="8" fill="#6b7280"/>'
            . '<path d="M32 65l8-10 6 8 12-14 14 16H32z" fill="#6b7280"/>'
            . '</svg>';
        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    $mime = mime_content_type($fullPath) ?: 'application/octet-stream';
    return response()->file($fullPath, ['Content-Type' => $mime]);
};

// Rutas que atienden las peticiones de imágenes del storage
Route::get('/storage/{path}',      $serveStorageFile)->where('path', '.*')->name('storage.serve');
Route::get('/storage-file/{path}', $serveStorageFile)->where('path', '.*')->name('storage.file');
