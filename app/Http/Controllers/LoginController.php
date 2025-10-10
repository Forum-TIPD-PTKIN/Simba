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
            return redirect()->route('login')->with('error', 'Token autentikasi gagal divefirikasi!');
        } else if (!$akun->data->status) {
            return redirect()->route('login')->with('error', 'Token autentikasi gagal divefirikasi!');
        }

        $user = User::whereUsername($akun->data->data->user->kode)->first();
        if ($akun->data->data->user->level == 1 && $user) {
            Auth::loginUsingId($user->id);

            if (in_array(0, $user->access)) {
                // admin
                session([
                    'is_admin' => true,
                    'level' => 0,
                    'profil' => $akun?->data?->data?->user?->profil
                ]);
                return redirect()->route('admin.dashboard');
            } else if (in_array(1, $user->access)) {
                //verifikator
                session([
                    'is_verifikator' => true,
                    'level' => 1,
                    'profil' => $akun?->data?->data?->user?->profil
                ]);
                return redirect()->route('verifikator.dashboard');
            }
        } else if ($akun->data->data->user->level == 1) {
            return redirect()->route('login')->with('error', 'Anda tidak memiliki akses pada sistem ini!');
        }

        $profil = $akun->data->data->user->profil;

        if (!$user) {
            $user = new User;
            $user->name = $profil->nama;
            $user->username = $akun->data->data->user->kode;
            $user->email = $akun->data->data->user->kode . '@student.iainmadura.ac.id';
            $user->access = $akun->data->data->user->level == 2 ? 2 : null;
        } else {
            $user->name = $akun->data->data->user->profil->nama;
        }
        $user->save();

        Auth::loginUsingId($user->id);
        if (isset($akun?->data?->data?->user?->profil->avatar)) {
            $akun->data->data->user->profil->avatar = 'https://be.iainmadura.ac.id/api/v1/external/mahasiswa/foto?nim=' . $akun?->data?->data?->user?->kode . '&key=6321afccabf95b9ec00ac8d193479f4f6a849d46ffbe50fc7e14a74011554fc1';
        }
        session([
            'is_pendaftar' => true,
            'level' => 2,
            'profil' => $akun?->data?->data?->user?->profil
        ]);
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
                session([
                    'is_admin' => true,
                    'level' => 0
                ]);
                return redirect()->route('admin.dashboard');
            } else if (in_array(1, $user->access)) {
                //verifikator
                session([
                    'is_verifikator' => true,
                    'level' => 1
                ]);
                return redirect()->route('verifikator.dashboard');
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

    public function change_access($access)
    {
        if (in_array($access, Auth::user()->access)) {
            session()->put('level', intval($access));
            if (session()->get('level') === 0) {
                // admin
                return redirect()->route('admin.dashboard');
            } else if (session()->get('level') === 1) {
                // verifikator
                return redirect()->route('verifikator.dashboard');
            } else if (session()->get('level') === 2) {
                // mahasiswa
                return redirect()->route('pendaftar.dashboard');
            }
        }

        return redirect()->back();
    }

    public function view_control(Request $request)
    {
        $html = view('view-controller.content', ['data' => $request->data['data'], 'active' => $request->data['active']])->render();
        return $html;
    }
}