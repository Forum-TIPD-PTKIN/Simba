<?php

namespace App\Models;

class FormData extends Uuid
{
    protected $hidden = ['config'];
    protected $appends = ['config_json'];

    public function getConfigJsonAttribute()
    {
        return json_decode($this->config);
    }
}