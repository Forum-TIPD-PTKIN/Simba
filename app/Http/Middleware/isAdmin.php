<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class isAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('level') && session()->get('level') === 0) {
            return $next($request);
        }

        if (session()->get('level') === 1) {
            // verifikator
            return redirect()->route('verifikator.dashboard');
        } else if (session()->get('level') === 2) {
            // mahasiswa
            return redirect()->route('pendaftar.dashboard');
        }
        return redirect()->route('login');
    }
}
