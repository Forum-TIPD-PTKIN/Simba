<?php

namespace App\Models;

class PendaftarStatus extends Uuid
{
    protected $fillable = [
        'pendaftar_id',
        'status',
        'deskripsi'
    ];
}
