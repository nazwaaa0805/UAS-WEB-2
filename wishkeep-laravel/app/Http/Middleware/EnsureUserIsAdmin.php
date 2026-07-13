<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Middleware ini dipasang setelah middleware 'auth', jadi di sini
     * $request->user() dijamin sudah login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Hanya untuk Admin.');
        }

        return $next($request);
    }
}
