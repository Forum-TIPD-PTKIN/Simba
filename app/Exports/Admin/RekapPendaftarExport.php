<?php

namespace App\Exports\Admin;

use App\Models\FormData;
use App\Models\Pendaftar;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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
        $this->results = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan', 'pemberkasan', 'biodata_pendaftar'])
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
        $biodata = collect($data->biodata_pendaftar?->data?->biodata)
            ->map(fn($item) => ($item->type === 'select' || $item->type === 'radio' ? $item->value . ' - ' . $item->valOption : $item->value))
            ->values()
            ->toArray();
        $berkas = collect($data->pemberkasan?->data?->pemberkasan)
            ->filter(fn($item) => $item->type === 'file')
            ->map(fn($item) => $item->value?->url . '|||' . $item->text ?? '')
            ->values()
            ->toArray();
        $kategori = $data->pemberkasan?->data?->pemberkasan?->kategori?->valOption ?? '';

        $row = [
            $this->nomor++,
            $data->mahasiswa?->nim,
            $data->mahasiswa?->nama,
            $data->mahasiswa?->prodi_name,
            $data->mahasiswa?->fakultas_name,
            $data->beasiswa?->nama,
            $data->tahun_kegiatan?->tahun,
            $data->latest_status?->status
        ];
        array_push($row, $kategori);
        if (!empty($biodata)) array_push($row, ...$biodata);
        if (!empty($berkas)) array_push($row, ...$berkas);

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
        $berkas = FormData::where('tahun_kegiatan_id', $this->tahun)
            ->where('beasiswa_id', $this->beasiswa)
            ->where('jenis', 'PEMBERKASAN')
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
            'Status'
        ];
        array_push($heading, $kategori);
        array_push($heading, ...$biodata);
        array_push($heading, ...$berkas);

        return $heading;
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

                $sheet = $event->sheet->getDelegate();

                $highestRow = $sheet->getHighestRow();
                $highestColumn = Coordinate::columnIndexFromString(
                    $sheet->getHighestColumn()
                );

                for ($row = 2; $row <= $highestRow; $row++) {

                    for ($col = 1; $col <= $highestColumn; $col++) {

                        $cell = Coordinate::stringFromColumnIndex($col) . $row;

                        $value = $sheet->getCell($cell)->getValue();

                        if (
                            is_string($value)
                            && str_contains($value, '|||')
                        ) {

                            [$url, $text] = explode('|||', $value, 2);

                            $sheet->setCellValue($cell, $text);

                            $sheet->getCell($cell)
                                ->getHyperlink()
                                ->setUrl($url);

                            $sheet->getStyle($cell)
                                ->getFont()
                                ->setUnderline(true)
                                ->getColor()
                                ->setARGB('0000FF');
                        }
                    }
                }
            }
        ];
    }
}
