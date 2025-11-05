<?php

namespace Database\Seeders;

use App\Models\MasterStatis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KepemilikanListrikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peng = new MasterStatis();
        $data = [
            [
                'value' => 1,
                'label' => 'Milik Sendiri',
            ],
            [
                'value' => 2,
                'label' => 'Sewa/Kontrak',
            ],
            [
                'value' => 3,
                'label' => 'Menumpang',
            ]
        ];


        $peng->create([
            'nama' => 'kepemilikan_listrik',
            'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
    }
}
