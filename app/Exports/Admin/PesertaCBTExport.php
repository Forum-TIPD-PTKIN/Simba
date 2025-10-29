<?php

namespace App\Exports\Admin;

use App\Models\Pendaftar;
use Carbon\Carbon;
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

class PesertaCBTExport implements FromCollection, WithMapping, WithHeadings, WithStyles, WithTitle, ShouldAutoSize, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    use Exportable;

    private $nomor = 1;
    private $results;
    private $tahun;
    private $beasiswa;

    public function __construct($tahun, $beasiswa)
    {
        $this->tahun = $tahun;
        $this->beasiswa = $beasiswa;
    }

    public function collection()
    {
        $this->results = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'map_ujian'])
            ->selectRaw('pendaftars.*')
            ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
            ->where('tahun_kegiatan_id', $this->tahun)
            ->where('beasiswa_id', $this->beasiswa)
            ->whereHas('map_ujian')
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
            Carbon::parse($data->map_ujian?->tanggal_mulai)->translatedFormat('d-m-Y'),
            $data->map_ujian?->sesi . '(' . Carbon::parse($data->map_ujian?->tanggal_mulai)->translatedFormat('H:i') . ' - ' . Carbon::parse($data->map_ujian?->tanggal_selesai)->translatedFormat('H:i') . ')',
            $data->map_ujian?->ruang
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
            'Tanggal Ujian',
            'Sesi',
            'Ruang'
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