<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function show(string $id)
    {
        $notif = Notifikasi::find($id);
        $notif->dibaca = 1;
        $notif->update();

        return view('modal-notifikasi', [
            'notif' => $notif
        ])->render();
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