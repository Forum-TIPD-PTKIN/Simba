<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

class JadwalKegiatan extends Uuid
{
    protected $appends = ['encrypted_id'];

    public function getEncryptedIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }

    public function tahun_kegiatan()
    {
        return $this->belongsTo(TahunKegiatan::class);
    }

    public function beasiswa()
    {
        return $this->belongsTo(Beasiswa::class);
    }
}
