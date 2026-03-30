<?php

namespace App\Exports\Admin;

use App\Models\FormData;
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

class HasilSeleksiAdministrasiExport implements
    FromCollection,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithStyles,
    WithTitle,
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
        $this->results = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'pemberkasan', 'biodata_pendaftar', 'pendaftar_status'])
            ->selectRaw('pendaftars.*')
            ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
            ->where(function ($query) {
                if ($this->status === null) {
                    $query->whereHas('pendaftar_status', function ($query) {
                        $query->whereIn('status', ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']);
                    });
                } else {
                    $query->whereHas('pendaftar_status', function ($query) {
                        $query->where('status', $this->status);
                    });
                }
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
        $biodata = collect($data->biodata_pendaftar?->data?->biodata)
            ->map(fn($item) => ($item->type === 'select' || $item->type === 'radio' ? $item->value . ' - ' . $item->valOption : $item->value))
            ->values();
        $kategori = $data->pemberkasan?->data?->pemberkasan?->kategori?->valOption ?? '';
        $status_seleksi_administrasi = collect($data->pendaftar_status)
            ->filter(fn($item) => in_array($item->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']))
            ->first();
        $catatan_verifikator = json_decode($status_seleksi_administrasi?->deskripsi)->catatan;

        $row = [
            $this->nomor++,
            $data->mahasiswa?->nim,
            $data->mahasiswa?->nama,
            $data->mahasiswa?->prodi_name,
            $data->mahasiswa?->fakultas_name,
            $data->beasiswa?->nama,
            $data->tahun_kegiatan?->tahun,
            $data->latest_status?->status ?? '',
            preg_replace('/\x{00A0}/u', ' ', html_entity_decode(strip_tags($catatan_verifikator)))
        ];
        array_push($row, $kategori);
        array_push($row, ...$biodata);

        return $row;
    }

    public function headings(): array
    {
        $biodata = FormData::where('tahun_kegiatan_id', $this->tahun)
            ->where('beasiswa_id', $this->beasiswa)
            ->where('jenis', 'BIODATA')
            ->orderBy('indexed')
            ->get()
            ->map(fn($item) => $item->config_json['title']);
        $kategori = FormData::where('tahun_kegiatan_id', $this->tahun)
            ->where('beasiswa_id', $this->beasiswa)
            ->where('jenis', 'PEMBERKASAN')
            ->where('config->name', 'kategori')
            ->orderBy('indexed')
            ->first()?->config_json['title'];

        $heading = [
            'No',
            'NIM',
            'Nama',
            'Prodi',
            'Fakultas',
            'Beasiswa',
            'Tahun',
            'Status',
            'Catatan Verifikator'
        ];
        array_push($heading, $kategori);
        array_push($heading, ...$biodata);

        return $heading;
    }

    public function title(): string
    {
        return 'Data Seleksi Administrasi';
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
                ])
                    ->getAlignment()
                    ->setWrapText(true);
            }
        ];
    }
}
