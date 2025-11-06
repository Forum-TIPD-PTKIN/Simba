<?php

namespace App\Imports\Admin;

use App\Models\Pendaftar;
use App\Models\PendaftarStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ImporPelulusanPesertaTpa implements
    ToCollection,
    WithStartRow,
    WithBatchInserts,
    WithChunkReading,
    WithMultipleSheets,
    // ShouldQueue,
    SkipsOnFailure
{
    /**
     * @param Collection $collection
     */
    use Importable;
    use RemembersChunkOffset;
    use SkipsFailures;
    use SkipsErrors;

    private $tahun;
    private $beasiswa;
    private $row_success = 0;

    public function __construct($tahun, $beasiswa)
    {
        $this->tahun = $tahun;
        $this->beasiswa = $beasiswa;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $pendaftar = Pendaftar::whereHas('mahasiswa', function ($query) use ($row) {
                $query->where('nim', $row[1]);
            })
                ->where('tahun_kegiatan_id', $this->tahun)
                ->where('beasiswa_id', $this->beasiswa)
                ->whereHas('latestStatus', function ($query) {
                    $query->where('status', 'LOLOS ADMINISTRASI');
                })
                ->first();

            if (!$pendaftar) {
                continue;
            } else {
                $pendaftar_status = new PendaftarStatus();
                $pendaftar_status->pendaftar_id = $pendaftar->id;
                $pendaftar_status->status = (string) $row[7] === 'LOLOS' ? 'LOLOS TPA' : 'GAGAL TPA';
                $insert = $pendaftar_status->save();
            }

            if ($insert) $this->row_success++;
        }
    }

    public function getRowCount(): int
    {
        return $this->row_success;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function batchSize(): int
    {
        return 50;
    }

    public function chunkSize(): int
    {
        return 50;
    }
}
