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

    protected $appends = ['fakultas_prodi'];
    public function getFakultasProdiAttribute()
    {
        $fak = explode('|', $this->fakultas);
        $prod = explode('|', $this->prodi);
        return ($fak[1] . ' / ' . $prod[1]) ?? '';
    }
}
