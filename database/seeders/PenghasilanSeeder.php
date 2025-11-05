<?php

namespace Database\Seeders;

use App\Models\MasterStatis;
use Illuminate\Database\Seeder;

class PenghasilanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $peng = new MasterStatis();
        $data = [
            [
                'value' => 10,
                'label' => '< Rp 1.000.000',
            ],
            [
                'value' => 9,
                'label' => 'Rp 1.000.000 - Rp 2.000.000',
            ],
            [
                'value' => 8,
                'label' => 'Rp 2.000.001 - Rp 3.000.000',
            ],
            [
                'value' => 7,
                'label' => 'Rp 3.000.001 - Rp 4.000.000',
            ],
            [
                'value' => 6,
                'label' => 'Rp 4.000.001 - Rp 5.000.000',
            ],
            [
                'value' => 5,
                'label' => 'Rp 5.000.001 - Rp 6.000.000',
            ],
            [
                'value' => 4,
                'label' => 'Rp 6.000.001 - Rp 7.000.000',
            ],
            [
                'value' => 3,
                'label' => 'Rp 7.000.001 - Rp 8.000.000',
            ],
            [
                'value' => 2,
                'label' => 'Rp 8.000.001 - Rp 9.999.999',
            ],
            [
                'value' => 1,
                'label' => '>= Rp 10.000.000',
            ],
        ];

        $peng->create([
            'nama' => 'penghasilan',
            'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
    }
}
