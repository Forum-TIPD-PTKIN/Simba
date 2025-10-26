<?php

namespace App\Http\Controllers\Pendaftar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Barryvdh\Snappy\Facades\SnappyPdf;

class TesPotensiAkademikController extends Controller
{
    public function generate_kartu(Request $request)
    {
        $data = ['title' => 'Laporan Bootstrap'];
        $style = public_path('assets/admin/css/style.css');
        $style_kartu = public_path('assets/admin/css/style-kartu.css');
        $html = view('pendaftar.kartu-ujian', [
            'data' => $data,
            'style' => $style,
            'style_kartu' => $style_kartu,
        ])->render();

        $pdf = SnappyPdf::loadHTML($html);
        return $pdf->download('laporan.pdf');
    }
}