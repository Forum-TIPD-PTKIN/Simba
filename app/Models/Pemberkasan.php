<?php

namespace App\Models;

class Pemberkasan extends Uuid
{
    protected $fillable = [
        'pendaftar_id',
        'lengkap'
    ];

    public function pemberkasan_item()
    {
        return $this->hasMany(PemberkasanItem::class);
    }
}
