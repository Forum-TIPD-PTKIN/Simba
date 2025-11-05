<?php

namespace Database\Seeders;

use App\Models\MasterStatis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BangunanRumahSeeder extends Seeder
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
                'label' => 'Permanen',
            ],
            [
                'value' => 2,
                'label' => 'Semi Permanen',
            ],
            [
                'value' => 3,
                'label' => 'Tidak Permanen',
            ]
        ];


        $peng->create([
            'nama' => 'bangunan_rumah',
            'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
    }
}
