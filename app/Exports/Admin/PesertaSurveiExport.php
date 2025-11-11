<?php

namespace App\Exports\Admin;

use App\Models\Pendaftar;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PesertaSurveiExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $nomor = 1;
    private $results;
    private $tahun;
    private $beasiswa;
    private $surveyor;

    public function __construct($tahun, $beasiswa, $surveyor)
    {
        $this->tahun = $tahun;
        $this->beasiswa = $beasiswa;
        $this->surveyor = $surveyor;
    }

    public function collection()
    {
        $this->results = Pendaftar::with('mahasiswa', 'beasiswa', 'tahun_kegiatan', 'biodata_pendaftar', 'surveyor_detail.surveyor.user')
            ->select('pendaftars.*')
            ->join('mahasiswas', 'mahasiswas.pendaftar_id', 'pendaftars.id')
            ->join('biodata_pendaftars', 'biodata_pendaftars.pendaftar_id', 'pendaftars.id')
            ->join('surveyor_details', 'surveyor_details.pendaftar_id', 'pendaftars.id')
            ->join('surveyors', 'surveyors.id', 'surveyor_details.surveyor_id')
            ->join('users', 'users.id', 'surveyors.user_id')
            ->where('pendaftars.tahun_kegiatan_id', $this->tahun)
            ->where('pendaftars.beasiswa_id', $this->beasiswa)
            ->whereHas('pendaftar_status', fn($query) => $query->where('status', 'LOLOS TPA'))
            ->whereHas('surveyor_detail', function ($query) {
                $query->whereHas('surveyor', fn($q) => $q->when($this->surveyor, fn($qr) => $qr->where('user_id', $this->surveyor)));
            })
            ->orderBy('users.name')
            ->orderBy('mahasiswas.prodi')
            ->orderBy('mahasiswas.nama')
            ->get();

        return $this->results;
    }

    public function map($data): array
    {
        return [
            $this->nomor++,
            $data->mahasiswa?->nim,
            $data->mahasiswa?->nama,
            $data->mahasiswa?->prodi_name,
            $data->mahasiswa?->fakultas_name,
            $data->biodata_pendaftar?->data?->biodata?->alamat_ktp?->value,
            $data->beasiswa?->nama,
            $data->tahun_kegiatan?->tahun,
            $data->surveyor_detail?->surveyor?->user?->name
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'NIM',
            'Nama',
            'Prodi',
            'Fakultas',
            'Alamat',
            'Beasiswa',
            'Tahun',
            'Surveyor'
        ];
    }

    public function title(): string
    {
        return 'Peserta Survei';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'DEDEDE',
                    ]
                ]
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {

                $alphabet       = $event->sheet->getHighestDataColumn();
                $totalRow       = $event->sheet->getHighestDataRow();
                $cellRange      = 'A1:' . $alphabet . $totalRow;

                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ])->getAlignment();
            }
        ];
    }
}
