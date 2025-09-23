<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiakadMahasiswa extends Model
{
    protected $connection = 'siakad';
    protected $table = 'mahasiswa.mahasiswa';

    public function prodi()
    {
        return $this->belongsTo(SiakadProdi::class, 'id_prodi', 'id_prodi');
    }
}
