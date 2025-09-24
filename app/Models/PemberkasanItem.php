<?php

namespace App\Models;

class PemberkasanItem extends Uuid
{

    protected $fillable = [
        'pemberkasan_id',
        'link_direct',
        'key',
        'name',
        'path',
        'md5',
        'size',
        'extension',
        'description',
    ];
}
