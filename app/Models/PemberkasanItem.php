<?php

namespace App\Models;

class PemberkasanItem extends Uuid
{

    protected $fillable = [
        'pemberkasan_id',
        'key',
        'name',
        'path',
        'md5',
        'size',
        'extension',
        'description',
    ];
}
