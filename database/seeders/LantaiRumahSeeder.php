<?php

namespace Database\Seeders;

use App\Models\MasterStatis;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LantaiRumahSeeder extends Seeder
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
                'label' => 'Granit',
            ],
            [
                'value' => 2,
                'label' => 'Keramik',
            ],
            [
                'value' => 3,
                'label' => 'Tegel',
            ],
            [
                'value' => 4,
                'label' => 'Semen',
            ],
            [
                'value' => 5,
                'label' => 'Tanah',
            ]
        ];


        $peng->create([
            'nama' => 'lantai_rumah',
            'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
    }
}
