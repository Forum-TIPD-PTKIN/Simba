<?php

namespace App\Models;

class Pendaftar extends Uuid
{
    protected $fillable = [
        'beasiswa_id',
        'tahun_kegiatan_id',
        'user_id'
    ];

    public function pemberkasan()
    {
        return $this->hasOne(Pemberkasan::class);
    }

    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }

    public function tahun_kegiatan()
    {
        return $this->belongsTo(TahunKegiatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
