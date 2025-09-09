<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

class Beasiswa extends Uuid
{
    protected $fillable = ['nama', 'deskripsi', 'status'];
    protected $hidden = ['id'];
    protected $appends = ['encrypted_id'];

    public function getEncryptedIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }
}