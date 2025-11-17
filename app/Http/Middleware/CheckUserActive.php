<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Verificar que el usuario esté autenticado
        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar que el usuario esté activo (solo para mayordomos y trabajadores)
        // Los administradores siempre pueden acceder
        if (in_array($user->role, ['foreman', 'worker']) && !$user->email_verified_at) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->withErrors([
                    'email' => 'Su cuenta está inactiva. Contacte al administrador para activar su cuenta.',
                ]);
        }

        return $next($request);
    }
}
