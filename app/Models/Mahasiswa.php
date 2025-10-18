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

    protected $appends = ['fakultas_name', 'prodi_name', 'fakultas_prodi'];
    public function getFakultasProdiAttribute()
    {
        $fak = explode('|', $this->fakultas);
        $prod = explode('|', $this->prodi);
        return ($fak[1] . ' / ' . $prod[1]) ?? '';
    }

    public function getFakultasNameAttribute()
    {
        $fak = explode('|', $this->fakultas);
        return $fak[1] ?? '';
    }

    public function getProdiNameAttribute()
    {
        $prod = explode('|', $this->prodi);
        return $prod[1] ?? '';
    }
}