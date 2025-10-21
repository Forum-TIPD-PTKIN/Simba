<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalCbt extends Model
{
    protected $connection = 'cbt';
    protected $table = 'jadwal';
    protected $primaryKey = 'id_jadwal';
    public $timestamps = false;
}
