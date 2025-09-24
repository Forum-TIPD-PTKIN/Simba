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
    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        if ($this->attributes['path']) {
            return url('attachment/' . str_replace('.', '/', $this->attributes['path']));
        } else if ($this->attributes['link_direct']) {
            return $this->attributes['link_direct'];
        }
    }
}
