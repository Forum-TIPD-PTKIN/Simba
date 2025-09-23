<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login_sso(Request $request)
    {
        $key = $request->key;
        $akun = api()->get('https://api.iainmadura.ac.id/api/onhand/detect?key=' . $key);
        if (!$akun->status) {
            return response()->json('Token autentikasi gagal divefirikasi!');
        } else if (!$akun->data->status) {
            return response()->json('Token autentikasi gagal divefirikasi!');
        }

        $user = User::whereUsername($akun->data->data->user->kode)->first();
        if ($akun->data->data->user->level == 1 && $user) {
            Auth::loginUsingId($user->id);

            if (in_array(0, $user->access)) {
                // admin
                return redirect()->route('admin.dashboard');
            } else if (in_array(1, $user->access)) {
                //verifikator
                return redirect()->route('verifikator.dashboard');
            }
        } else if ($akun->data->data->user->level == 1) {
            return response()->json('Anda tidak memiliki akses pada sistem ini!');
        }

        $profil = $akun->data->data->user->profil;

        if (!$user) {
            $user = new User;
            $user->name = $profil->nama;
            $user->username = $akun->data->data->user->kode;
            $user->email = $akun->data->data->user->kode . '@student.iainmadura.ac.id';
            $user->password = bcrypt('user' . $akun->data->data->user->kode);
            $user->access = $akun->data->data->user->level == 2 ? 2 : 0;
        } else {
            $user->name = $akun->data->data->user->profil->nama;
        }

        $user->save();

        Auth::loginUsingId($user->id);
        return redirect()->route('pendaftar.dashboard');
    }

    public function login_view()
    {
        if (Auth::check()) {
            if (in_array(0, Auth::user()->access)) {
                // admin
                return redirect(route('admin.dashboard'));
            } else if (in_array(1, Auth::user()->access)) {
                //verifikator
                return redirect(route('verifikator.dashboard'));
            } else if (in_array(2, Auth::user()->access)) {
                //pendaftar
                return redirect(route('pendaftar.dashboard'));
            }
        }

        return view('login');
    }

    public function login_secret()
    {
        return view('login-secret');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if (in_array(0, $user->access)) {
                // admin
                return redirect()->route('admin.dashboard');
            } else if (in_array(1, $user->access)) {
                //verifikator
                return redirect()->route('verifikator.dashboard');
            } else if (in_array(2, $user->access)) {
                //pendaftar
                return redirect()->route('pendaftar.dashboard');
            }
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        if ($request->ajax()) {
            Auth::logout();

            $request->session()->invalidate();

            $request->session()->regenerateToken();

            return response()->json([
                'icon' => 'success',
                'title' => 'Berhasil',
                'message' => 'Sedang dialihkan...',
                'redirect' => route('login')
            ]);
        }
    }
}