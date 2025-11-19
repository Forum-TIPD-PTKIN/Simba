<?php

namespace App\Exports\Admin;

use App\Models\Surveyor;
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

class DataSurveyorExport implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $nomor = 1;
    private $results;
    private $tahun;
    private $beasiswa;
    private $status;

    public function __construct($tahun, $beasiswa, $status)
    {
        $this->tahun = $tahun;
        $this->beasiswa = $beasiswa;
        $this->status  = $status;
    }

    public function collection()
    {
        $this->results = Surveyor::with([
            'user',
            'beasiswa',
            'tahun_kegiatan'
        ])
            ->select('surveyors.*')
            ->join('users', 'users.id', 'surveyors.user_id')
            ->where('surveyors.tahun_kegiatan_id', $this->tahun)
            ->where('surveyors.beasiswa_id', $this->beasiswa)
            ->where(function ($query) {
                if ($this->status === 'u') {
                    $query->whereNull('surveyors.bersedia');
                } elseif ($this->status === 't') {
                    $query->where('surveyors.bersedia', 0);
                } else {
                    $query->where('surveyors.bersedia', 1);
                }
            })
            ->orderBy('users.name')
            ->get();

        return $this->results;
    }

    public function map($data): array
    {
        return [
            $this->nomor++,
            $data->user?->name ?? '-',
            $data->hp,
            $data->alamat,
            $data->rekening_bank_formatted['no_rekening'] ?? '-',
            $data->rekening_bank_formatted['nama_rekening'] ?? '-',
            $data->rekening_bank_formatted['nama_bank'] ?? '-',
            $data->rekening_bank_formatted['file_rekening'] ?? '-',
            $data->bersedia === 1 ? 'Bersedia' : ($data->status === 0 ? 'Tidak Bersedia' : 'Menunggu Persetujuan'),
            $data->beasiswa?->nama ?? '-',
            $data->tahun_kegiatan?->tahun ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'No. HP',
            'Alamat',
            'No. Rekening',
            'Nama Rekening',
            'Nama Bank',
            'File Buku Rekening',
            'Status',
            'Beasiswa',
            'Tahun',
        ];
    }

    public function title(): string
    {
        return 'Data Surveyor';
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        foreach ([1] as $row) {
            $styles[$row] = [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'DEDEDE'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
        }
        return $styles;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $sheet = $event->sheet;
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

                // Wrap text
                $sheet->getStyle('D2:D' . $totalRow)->getAlignment()->setWrapText(true); // Alamat
                $sheet->getStyle('H2:H' . $totalRow)->getAlignment()->setWrapText(true); // File rekening

                $sheet->getDelegate()->getColumnDimension('D')->setAutoSize(false);
                $sheet->getDelegate()->getColumnDimension('D')->setWidth(40);
                $sheet->getDelegate()->getColumnDimension('H')->setAutoSize(false);
                $sheet->getDelegate()->getColumnDimension('H')->setWidth(50);

                // Loop semua baris mulai dari 2 (skip header) untuk membuat hyperlink
                for ($row = 2; $row <= $totalRow; $row++) {
                    $url = $sheet->getDelegate()->getCell("H{$row}")->getValue();

                    if (!empty($url)) {
                        // Set teks di kolom H
                        $sheet->getDelegate()->setCellValue("H{$row}", $url);
                        // Tambahkan hyperlink
                        $sheet->getCell("H{$row}")->getHyperlink()->setUrl($url);

                        // Styling agar terlihat seperti hyperlink
                        $sheet->getStyle("H{$row}")->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => '0000FF'],
                                'underline' => 'single',
                                'italic' => true,
                            ]
                        ]);
                    }
                }
            }
        ];
    }
}
