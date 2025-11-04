<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Surveyor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function read(Request $request, $id)
    {
        $notifikasi = Notifikasi::find($id);
        if ($notifikasi->user_id == Auth::id()) {
            $notifikasi->dibaca = 1;
            $notifikasi->update();
        }
        if ($notifikasi->key === 'ASSIGN_SURVEYOR') {
            session()->put('level', 3);
        }
        return redirect()->to($notifikasi->referensi);
    }

    public function destroy(Request $request)
    {
        try {
            Notifikasi::where('user_id', Auth::id())
                ->where('dibaca', 0)
                ->update([
                    'dibaca' => 1
                ]);
        } catch (\Illuminate\Database\QueryException $e) {
            return abort(422, $e->errorInfo[2] ?: 'Ada kesalahan');
        }
    }
}