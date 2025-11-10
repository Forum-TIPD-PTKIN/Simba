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

    protected $appends = ['fakultas_name', 'prodi_name', 'fakultas_prodi', 'foto'];
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
    public function getFotoAttribute()
    {
        $nim = $this->attributes['nim'];
        return 'https://be.iainmadura.ac.id/api/v1/external/mahasiswa/foto?nim=' . $nim . '&key=6321afccabf95b9ec00ac8d193479f4f6a849d46ffbe50fc7e14a74011554fc1';
    }
}
