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

class HasilSurveiExport implements FromCollection, WithMapping, WithHeadings, WithStyles, ShouldAutoSize, WithTitle, WithEvents
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
        $this->results = Pendaftar::with([
            'mahasiswa',
            'beasiswa',
            'tahun_kegiatan',
            'biodata_pendaftar',
            'pemberkasan',
            'surveyor_detail.surveyor.user'
        ])
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
                $query->whereHas('surveyor', fn($q) => $q->when(!empty($this->surveyor), fn($qr) => $qr->where('user_id', $this->surveyor)));
            })
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
            $data->pemberkasan?->data?->pemberkasan?->kategori?->valOption,
            $data->surveyor_detail?->surveyor?->user?->name,
            $data->hasil_survei?->nilai?->ayahNama ?? '',
            $data->hasil_survei?->nilai?->ayahKesehatan?->text ?? '',
            $data->hasil_survei?->nilai?->ayahKesehatan?->value ?? '',
            $data->hasil_survei?->nilai?->ayahPekerjaan?->text ?? '',
            $data->hasil_survei?->nilai?->ayahPekerjaan?->value ?? '',
            $data->hasil_survei?->nilai?->ayahPenghasilan?->text ?? '',
            $data->hasil_survei?->nilai?->ayahPenghasilan?->value ?? '',
            $data->hasil_survei?->nilai?->ibuNama ?? '',
            $data->hasil_survei?->nilai?->ibuKondisi?->text ?? '',
            $data->hasil_survei?->nilai?->ibuKondisi?->value ?? '',
            $data->hasil_survei?->nilai?->ibuPekerjaan?->text ?? '',
            $data->hasil_survei?->nilai?->ibuPekerjaan?->value ?? '',
            $data->hasil_survei?->nilai?->ibuPenghasilan?->text ?? '',
            $data->hasil_survei?->nilai?->ibuPenghasilan?->value ?? '',
            $data->hasil_survei?->nilai?->tanggunganKeluarga?->text ?? '',
            $data->hasil_survei?->nilai?->tanggunganKeluarga?->value ?? '',
            $data->hasil_survei?->nilai?->kepemilikanRumah?->text ?? '',
            $data->hasil_survei?->nilai?->kepemilikanRumah?->value ?? '',
            $data->hasil_survei?->nilai?->bangunanRumah?->text ?? '',
            $data->hasil_survei?->nilai?->bangunanRumah?->value ?? '',
            $data->hasil_survei?->nilai?->lantaiRumah?->text ?? '',
            $data->hasil_survei?->nilai?->lantaiRumah?->value ?? '',
            $data->hasil_survei?->nilai?->kondisiDapur?->text ?? '',
            $data->hasil_survei?->nilai?->kondisiDapur?->value ?? '',
            $data->hasil_survei?->nilai?->kondisiKamarMandi?->text ?? '',
            $data->hasil_survei?->nilai?->kondisiKamarMandi?->value ?? '',
            $data->hasil_survei?->nilai?->kondisiWc?->text ?? '',
            $data->hasil_survei?->nilai?->kondisiWc?->value ?? '',
            $data->hasil_survei?->nilai?->kepemilikanListrik?->text ?? '',
            $data->hasil_survei?->nilai?->kepemilikanListrik?->value ?? '',
            $data->hasil_survei?->nilai?->catatan ?? '',
            $data->hasil_survei?->point ?? ''
        ];
    }

    public function headings(): array
    {
        return [[
            'No',
            'NIM',
            'Nama',
            'Prodi',
            'Fakultas',
            'Alamat',
            'Beasiswa',
            'Tahun',
            'Kategori',
            'Surveyor',
            'Hasil Survei',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ], [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Nama Ayah',
            'Kondisi Ayah',
            'Nilai',
            'Pekerjaan Ayah',
            'Nilai',
            'Penghasilan Ayah',
            'Nilai',
            'Nama Ibu',
            'Kondisi Ibu',
            'Nilai',
            'Pekerjaan Ibu',
            'Nilai',
            'Penghasilan Ibu',
            'Nilai',
            'Tanggungan Keluarga',
            'Nilai',
            'Kepemilikan Rumah',
            'Nilai',
            'Bangunan Rumah',
            'Nilai',
            'Lantai Rumah',
            'Nilai',
            'Kondisi Dapur',
            'Nilai',
            'Kondisi Kamar Mandi',
            'Nilai',
            'Kondisi WC',
            'Nilai',
            'Kepemilikan Listrik',
            'Nilai',
            'Catatan',
            'Total Nilai'
        ]];
    }

    public function title(): string
    {
        return 'Hasil Survei';
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [];
        foreach ([1, 2] as $row) {
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

                // Merge untuk header utama
                $sheet->mergeCells('A1:A2'); // No
                $sheet->mergeCells('B1:B2'); // NIM
                $sheet->mergeCells('C1:C2'); // Nama
                $sheet->mergeCells('D1:D2'); // Prodi
                $sheet->mergeCells('E1:E2'); // Fakultas
                $sheet->mergeCells('F1:F2'); // Alamat
                $sheet->mergeCells('G1:G2'); // Beasiswa
                $sheet->mergeCells('H1:H2'); // Tahun
                $sheet->mergeCells('I1:I2'); // Kategori
                $sheet->mergeCells('J1:J2'); // Surveyor

                // Merge untuk "Hasil Survei"
                $sheet->mergeCells('K1:AP1'); // Hasil Survei

                // Wrap text
                $sheet->getStyle('F3:F' . $totalRow)->getAlignment()->setWrapText(true); // Alamat
                $sheet->getStyle('AO3:AO' . $totalRow)->getAlignment()->setWrapText(true); // Catatan

                $sheet->getDelegate()->getColumnDimension('F')->setAutoSize(false);
                $sheet->getDelegate()->getColumnDimension('F')->setWidth(40);

                $sheet->getDelegate()->getColumnDimension('AO')->setAutoSize(false);
                $sheet->getDelegate()->getColumnDimension('AO')->setWidth(50);
            }
        ];
    }
}
