<?php

namespace App\Models;

class Mahasiswa extends Uuid
{
    protected $fillable = [
        'pendaftar_id',
        'nim',
        'nama',
        'fakultas',
        'prodi',
    ];
}
