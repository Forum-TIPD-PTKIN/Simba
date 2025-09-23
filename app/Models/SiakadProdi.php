<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiakadProdi extends Model
{
    protected $connection = 'siakad';
    protected $table = 'master.prodi';


    public function fakultas()
    {
        return $this->belongsTo(SiakadFakultas::class, 'id_fakultas', 'id_fakultas');
    }
}
