<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (isset($user->status_aktif) && ! $user->status_aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan oleh Administrator.',
            ]);
        }

        $hasRole = false;
        foreach ($roles as $role) {
            if ($user->role === $role) {
                $hasRole = true;
                break;
            }
            // Super Admin can access all admin and admin_fakultas routes
            if ($user->isSuperAdmin() && in_array($role, ['admin', 'admin_fakultas', 'super_admin'], true)) {
                $hasRole = true;
                break;
            }
            // Allow admin_fakultas to match admin requirement and vice versa
            if (($role === 'admin' || $role === 'admin_fakultas') && ($user->role === 'admin' || $user->role === 'admin_fakultas')) {
                $hasRole = true;
                break;
            }
        }

        if (! $hasRole) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak akses untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
