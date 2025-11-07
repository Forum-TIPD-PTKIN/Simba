<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrumen Survei</title>
    {{-- <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}"> --}}
    <link rel="stylesheet" href="file:///{{ $style }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            color: #000;
            background: #ffffff;
        }

        header h2,
        header h3 {
            text-align: center;
            text-transform: uppercase;
            margin: 0;
            padding: 2px;
        }

        header h2 {
            font-size: 15px;
        }

        header h3 {
            font-size: 13px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
            font-size: 13px;
            line-height: 1.3;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
            word-wrap: break-word;
            word-break: break-word;
            white-space: normal;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }

        .vertical {
            text-align: center;
            vertical-align: middle;
        }

        .rotate {
            transform: rotate(-90deg);
            white-space: nowrap;
            font-weight: bold;
            display: inline-block;
        }


        .section {
            margin-top: 20px;
        }

        .section h4 {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .section p {
            margin: 5px 0;
            font-size: 12px;
        }

        .box {
            border: 1px solid #000;
            height: 60px;
            margin-bottom: 10px;
        }

        .signature {
            margin-top: 10px;
            font-size: 12px;
        }

        .signature p {
            margin: 0;
        }

        .signature table p {
            font-size: 12px;
        }

        .names {
            margin-top: 60px;
        }

        .space {
            display: inline-block;
            width: 200px;
        }
    </style>
</head>

<body>
    <header>
        <h2>INSTRUMEN SURVEI LAPANGAN </h2>
        <h2>CALON PENERIMA BEASISWA {{ $beasiswa }} TAHUN {{ $tahun }}</h2>
        <h3>UIN MADURA</h3>
    </header>

    <table>
        <thead>
            <tr>
                <th width="5%">&nbsp;</th>
                <th width="5%">NO</th>
                <th width="15%">BUTIR SURVEI</th>
                <th>URAIAN</th>
                <th width="7%">CEK</th>
            </tr>
        </thead>
        <tbody>
            <!-- Bagian BIODATA -->
            <tr>
                <td rowspan="11" class="vertical">
                    <div class="rotate">BIODATA</div>
                </td>
                <td class="text-center">1</td>
                <td>NIM</td>
                <td>{{ $peserta->mahasiswa?->nim }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Nama</td>
                <td>{{ $peserta->mahasiswa?->nama }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Prodi</td>
                <td>{{ $peserta->mahasiswa?->prodi_name }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Alamat</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->alamat_ktp?->value }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Nomor KTP</td>
                <td>{{ $data_siakad?->nik }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Nomor Ijazah SMA (Sederajat)</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->nomor_ijazah?->value }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">7</td>
                <td>Nilai Transkrip SMA (Sederajat)</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->nilai_akhir?->value }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">8</td>
                <td>Nomor Ponsel</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->no_hp?->value }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">9</td>
                <td>Nomor Kartu Keluarga</td>
                <td>-</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">10</td>
                <td>Nama Ayah</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->nama_ayah?->value }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">11</td>
                <td>Nama Ibu</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->nama_ibu?->value }}</td>
                <td></td>
            </tr>

            <!-- Bagian EKONOMI -->
            <tr>
                <td rowspan="7" class="vertical">
                    <div class="rotate">
                        EKONOMI
                    </div>
                </td>
                <td class="text-center">12</td>
                <td>Kondisi Ayah</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->kondisi_ayah?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">13</td>
                <td>Pekerjaan Ayah</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->pekerjaan_ayah?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">14</td>
                <td>Penghasilan Ayah</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->penghasilan_ayah?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">15</td>
                <td>Kondisi Ibu</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->kondisi_ibu?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">16</td>
                <td>Pekerjaan Ibu</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->pekerjaan_ibu?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">17</td>
                <td>Penghasilan Ibu</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->penghasilan_ibu?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">18</td>
                <td>Tanggungan Keluarga</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->tanggungan_keluarga?->valOption }}</td>
                <td></td>
            </tr>

            <!-- Bagian TEMPAT TINGGAL -->
            <tr>
                <td rowspan="7" class="vertical">
                    <div class="rotate">
                        TEMPAT TINGGAL
                    </div>
                </td>
                <td class="text-center">19</td>
                <td>Status Kepemilikan Rumah</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->kepemilikan_rumah?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">20</td>
                <td>Bangunan Rumah</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->bangunan_rumah?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">21</td>
                <td>Lantai Rumah</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->lantai_rumah?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">22</td>
                <td>Kepemilikan Listrik</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->kepemilikan_listrik?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">23</td>
                <td>Kondisi Dapur</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->kondisi_dapur?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">24</td>
                <td>Kondisi Kamar Mandi & WC</td>
                <td>{{ $peserta->biodata_pendaftar?->data?->biodata?->kamar_mandi_wc?->valOption }}</td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">25</td>
                <td>Berkas persyaratan</td>
                <td><a href="https://simba.iainmadura.ac.id/surveyor/survey/{{ $peserta->id }}"
                        target="_blank">Lihat</a></td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <div class="section">
        <h4>LEMBAR DESKRIPSI</h4>
        <p>Deskripsikan kondisi faktual kategori survei di bagian ini!</p>

        <p><b>A. KESESUAIAN DOKUMEN DAN KONDISI FAKTUAL</b></p>
        <div class="box"></div>

        <p><b>B. KONDISI ORANG TUA/WALI</b></p>
        <div class="box"></div>

        <p><b>C. KONDISI TEMPAT TINGGAL</b></p>
        <div class="box"></div>

        <p><b>D. KONDISI EKONOMI</b></p>
        <div class="box"></div>
    </div>

    <div class="signature">
        <table class="table table-borderless">
            <tr>
                <td>&nbsp;</td>
                <td class="text-center">
                    <p class="mb-2">.................................,
                        ................................................</p>
                </td>
            </tr>
            <tr>
                <td class="text-center">
                    <p>Orang Tua/Wali,</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p class="mb-0">
                        <strong>{{ $peserta->biodata_pendaftar?->data?->biodata?->nama_ayah?->value }}</strong>
                    </p>
                    <p>&nbsp;</p>
                </td>
                <td class="text-center">
                    <p>Surveyor,</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p class="mb-1"><strong>{{ $surveyor->user?->name }}</strong></p>
                    <p>NIP. .......................................</p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
