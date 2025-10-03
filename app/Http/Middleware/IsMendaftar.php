<?php

namespace App\Http\Middleware;

use App\Models\Pendaftar;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsMendaftar
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = Auth::id();
        $cek = Pendaftar::whereUserId($id)->count();

        if ($cek) {
            session()->put('MENDAFTAR', true);
        } else {
            session()->put('MENDAFTAR', false);
        }
        return $next($request);
    }
}
