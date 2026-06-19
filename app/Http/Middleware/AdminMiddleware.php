<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->withErrors([
                'message' => 'Silakan login terlebih dahulu.'
            ]);
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard')->withErrors([
                'message' => 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.'
            ]);
        }

        return $next($request);
    }
}