<?php

namespace App\Models;

class Pemberkasan extends Uuid
{
    protected $fillable = [
        'pendaftar_id',
        'data'
    ];

    public function getDataAttribute($val)
    {
        $val = str_replace('[URL_ORIGIN]', url('/'), $val);
        return json_decode($val);
    }
}
