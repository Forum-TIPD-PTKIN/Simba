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
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Conditional;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplatePelulusanPesertaTpa implements
    FromCollection,
    ShouldAutoSize,
    WithMapping,
    WithHeadings,
    WithStyles,
    WithTitle,
    WithEvents
{
    use Exportable;

    /**
     * @return \Illuminate\Support\Collection
     */

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
        $this->results = Pendaftar::with(['mahasiswa', 'beasiswa', 'tahun_kegiatan'])
            ->selectRaw('pendaftars.*')
            ->join('mahasiswas', 'pendaftars.id', 'mahasiswas.pendaftar_id')
            ->where('tahun_kegiatan_id', $this->tahun)
            ->where('beasiswa_id', $this->beasiswa)
            // ->whereHas('map_ujian')
            ->whereHas('latestStatus', function ($query) {
                $query->where('status', 'LOLOS ADMINISTRASI');
            })
            ->whereDoesntHave('latestStatus', function ($query) {
                $query->whereIn('status', ['LOLOS TPA', 'GAGAL TPA']);
            })
            ->orderBy('mahasiswas.prodi')
            ->orderBy('mahasiswas.nama')
            ->get();

        return $this->results;
    }

    public function map($data): array
    {
        $row = [
            $this->nomor++,
            $data->mahasiswa?->nim,
            $data->mahasiswa?->nama,
            $data->mahasiswa?->prodi_name,
            $data->mahasiswa?->fakultas_name,
            $data->beasiswa?->nama,
            $data->tahun_kegiatan?->tahun,
        ];

        return $row;
    }

    public function headings(): array
    {
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

        return $heading;
    }

    public function title(): string
    {
        return 'template_pelulusan_peserta_tpa';
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

                // set dropdown column
                $drop_column = ['H'];

                $data_options_status = ['LOLOS', 'GAGAL'];

                foreach ($drop_column as $value) {
                    // set dropdown options
                    $options = $data_options_status;

                    // set dropdown list for first data row
                    $validation = $event->sheet->getCell("{$value}2")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Value is not in list.');
                    $validation->setPromptTitle('Pick from list');
                    $validation->setPrompt('Please pick a value from the drop-down list.');
                    $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

                    // clone validation to remaining rows
                    for ($i = 3; $i <= $totalRow; $i++) {
                        $event->sheet->getCell("{$value}{$i}")->setDataValidation(clone $validation);
                    }
                }

                for ($i = 2; $i <= $totalRow; $i++) {
                    $cell = "{$value}{$i}";

                    // Conditional formatting: jika "LOLOS", beri warna hijau
                    $conditionalLolos = new Conditional();
                    $conditionalLolos->setConditionType(Conditional::CONDITION_CELLIS);
                    $conditionalLolos->setOperatorType(Conditional::OPERATOR_EQUAL);
                    $conditionalLolos->addCondition('"LOLOS"');
                    $conditionalLolos->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
                    $conditionalLolos->getStyle()->getFill()->getStartColor()->setARGB(Color::COLOR_GREEN);

                    // Conditional formatting: jika "GAGAL", beri warna merah
                    $conditionalGagal = new Conditional();
                    $conditionalGagal->setConditionType(Conditional::CONDITION_CELLIS);
                    $conditionalGagal->setOperatorType(Conditional::OPERATOR_EQUAL);
                    $conditionalGagal->addCondition('"GAGAL"');
                    $conditionalGagal->getStyle()->getFill()->setFillType(Fill::FILL_SOLID);
                    $conditionalGagal->getStyle()->getFill()->getStartColor()->setARGB('FFF08080');

                    // Gabungkan dan set ke cell
                    $conditionalStyles = $event->sheet->getStyle($cell)->getConditionalStyles();
                    $conditionalStyles[] = $conditionalLolos;
                    $conditionalStyles[] = $conditionalGagal;
                    $event->sheet->getStyle($cell)->setConditionalStyles($conditionalStyles);
                }
            }
        ];
    }
}