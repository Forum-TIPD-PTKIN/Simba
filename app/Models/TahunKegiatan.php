<?php

namespace App\Models;

use Illuminate\Support\Facades\Crypt;

class TahunKegiatan extends Uuid
{
    protected $fillable = ['tahun', 'status'];
    protected $hidden = ['id'];
    protected $appends = ['encrypted_id'];

    public function getEncryptedIdAttribute()
    {
        return Crypt::encryptString($this->id);
    }
}
