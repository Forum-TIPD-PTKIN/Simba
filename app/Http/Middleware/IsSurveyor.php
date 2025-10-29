<?php

namespace App\Http\Middleware;

use App\Models\Surveyor;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsSurveyor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('level') && session()->get('level') === 3) {
            $cek = Surveyor::whereUserId(Auth::id())->count();
            if ($cek) {
                return $next($request);
            } else {
                return redirect()->route('login');
            }
        }

        if (session()->get('level') === 1) {
            // verifikator
            return redirect()->route('verifikator.dashboard');
        } else if (session()->get('level') === 2) {
            // mahasiswa
            return redirect()->route('pendaftar.dashboard');
        } else if (session()->get('level') === 0) {
            // admin
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('login');
    }
}
