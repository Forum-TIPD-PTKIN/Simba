<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

class Beasiswa extends Uuid
{
    protected $fillable = ['nama', 'deskripsi', 'status'];
    protected $hidden = ['id'];
    protected $appends = ['encrypted_id', 'config_data_json'];

    public function getEncryptedIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }

    public function getConfigDataJsonAttribute()
    {
        if (is_string($this->config_data) && json_decode($this->config_data) !== null) {
            return json_decode($this->config_data, true);
        }
        return [];
    }


    public function scopeDescriptId($query, $enc)
    {
        $id = Crypt::decryptString($enc);
        $query->whereId($id);
    }

    public function jadwal_kegiatan()
    {
        return $this->hasMany(JadwalKegiatan::class);
    }

    public function pendaftar()
    {
        return $this->hasMany(Pendaftar::class);
    }
}