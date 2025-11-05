<?php

namespace Database\Seeders;

use App\Models\MasterStatis;
use Illuminate\Database\Seeder;

class PekerjaanSeeder extends Seeder
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
                'label' => 'PNS/TNI/POLRI',
            ],
            [
                'value' => 2,
                'label' => 'Pensiunan',
            ],
            [
                'value' => 3,
                'label' => 'Pedagang',
            ],
            [
                'value' => 4,
                'label' => 'Petani/Nelayan',
            ],
            [
                'value' => 5,
                'label' => 'Wirausaha',
            ],
            [
                'value' => 6,
                'label' => 'Buruh Tetap',
            ],
            [
                'value' => 7,
                'label' => 'Buruh Tidak Tetap',
            ],
            [
                'value' => 8,
                'label' => 'Tidak Bekerja',
            ],
            [
                'value' => 'LAINNYA',
                'label' => 'Lainnya',
            ],
        ];


        $peng->create([
            'nama' => 'pekerjaan',
            'data' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        ]);
    }
}
