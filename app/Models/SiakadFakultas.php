<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiakadFakultas extends Model
{
    protected $connection = 'siakad';
    protected $table = 'master.fakultas';
}
