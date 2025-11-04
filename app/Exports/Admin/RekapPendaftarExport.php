<?php

namespace App\Exports\Admin;

use App\Models\Pendaftar;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapPendaftarExport implements
    FromCollection,
    WithMapping,
    WithHeadings,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    private $nomor = 1;
    private $results;
    private $tahun;
    private $beasiswa;
    private $status;

    public function __construct($tahun, $beasiswa, $status)
    {
        $this->tahun = $tahun;
        $this->beasiswa = $beasiswa;
        $this->status = $status;
    }

    public function collection()
    {
        $this->results = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan'])
            ->selectRaw('pendaftars.*')
            ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
            ->when($this->status !== null, function ($query) {
                $query->whereHas('pendaftar_status', function ($query) {
                    $query->where('status', $this->status);
                });
            })
            ->where('tahun_kegiatan_id', $this->tahun)
            ->where('beasiswa_id', $this->beasiswa)
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
            $data->beasiswa?->nama,
            $data->tahun_kegiatan?->tahun,
            $data->latest_status?->status
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
            'Beasiswa',
            'Tahun',
            'Status'
        ];
    }

    public function title(): string
    {
        return 'Rekap Pendaftar';
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