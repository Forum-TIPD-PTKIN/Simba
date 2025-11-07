<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrumen Survei Lapangan KIP Kuliah 2024</title>
    <style>
        body {
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.5;
        }

        .instrumen-container {
            margin: 20px auto;
            padding: 40px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            width: 21.5cm;
            height: 33cm;
            overflow: hidden;
        }

        .judul-utama {
            text-align: center;
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #111;
            text-transform: uppercase;
        }

        .sub-judul {
            text-align: center;
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .instrumen-tabel {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 8px;
        }

        .instrumen-tabel th,
        .instrumen-tabel td {
            border: 1px solid #000000;
            padding: 6px 12px;
            vertical-align: top;
            text-align: left;
        }

        .instrumen-tabel td.tabel-kategori-vertikal {
            text-align: center;
        }

        .instrumen-tabel th {
            text-align: center;
            font-weight: 600;
            background-color: #f7f7f7;
            padding-top: 10px;
        }

        .tabel-kategori-vertikal {
            text-align: center;
            vertical-align: middle !important;
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            font-weight: 600;
            width: 30px;
            text-transform: uppercase;
        }

        .lembar-deskripsi {
            font-size: 14px;
            margin-top: 20px;
        }

        .lembar-deskripsi .judul-deskripsi {
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            border-bottom: 1px solid #000000;
            margin: 0;
        }

        .deskripsi-item {
            margin-top: 15px;
        }

        .deskripsi-item label {
            font-weight: 600;
            display: block;
            margin-bottom: -4px;
        }

        .kotak-isian {
            border: 1px solid #000000;
            min-height: 50px;
            margin-bottom: 15px;
            padding: 8px;
        }

        .area-tanda-tangan {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            font-size: 14px;
            line-height: 1.6;
        }

        .ttd-kiri,
        .ttd-kanan {
            width: 45%;
        }

        .ttd-kanan {
            text-align: left;
        }

        a {
            color: #0066cc;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        td.table-striped {
            background: #f1f0f0;
        }

        @media print {
            @page {
                size: 215mm 330mm;
                margin: 1.5cm;
            }

            body {
                font-family: "Times New Roman", serif;
                background-color: #fff !important;
                color: #000 !important;
                margin: 0;
                padding: 0;
                font-size: 10pt;
                line-height: 1.3;
            }

            .instrumen-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                zoom: 0.95;
                break-inside: avoid;
                overflow: hidden;
            }

            h2.judul-utama,
            h3.sub-judul {
                color: #000 !important;
                margin-bottom: 15px;
            }

            .instrumen-tabel,
            .instrumen-tabel th,
            .instrumen-tabel td {
                border: 1px solid #000 !important;
                font-size: 9pt;
                padding: 4px 6px;
            }

            .tabel-kategori-vertikal,
            .instrumen-tabel th {
                /* -webkit-print-color-adjust: exact;
                color-adjust: exact; */
            }

            .kotak-isian {
                border: 1px solid #000 !important;
                min-height: 42px;
                margin-bottom: 10px;
            }

            .deskripsi-item {
                margin-top: 10px;
            }

            a,
            a:visited {
                color: #000 !important;
                text-decoration: underline !important;
            }

            .area-tanda-tangan {
                margin-top: 20px;
                line-height: 1.4;
            }

            .deskripsi-item,
            .area-tanda-tangan,
            .ttd-kiri,
            .ttd-kanan {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="instrumen-container">
        <h2 class="judul-utama">INSTRUMEN SURVEI LAPANGAN CALON PENERIMA KIP KULIAH 2024</h2>
        <h3 class="sub-judul">IAIN MADURA</h3>

        <table class="instrumen-tabel">
            <thead>
                <tr>
                    <th>#10</th>
                    <th>NO</th>
                    <th>BUTIR SURVEI</th>
                    <th>URAIAN</th>
                    <th>CEK</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $stripedTable = false;
                @endphp
                @foreach ($butiran as $key => $item)
                    <tr>
                        @foreach ($grouper as $group)
                            @if ($key == $group->range[0])
                                @php
                                    $stripedTable = !$stripedTable;
                                @endphp
                                <td rowspan="{{ $group->rowspan }}"
                                    class="tabel-kategori-vertikal {{ $stripedTable ? 'table-striped' : '' }}">
                                    {{ $group->text }}
                                </td>
                            @endif
                        @endforeach
                        <td class="{{ $stripedTable ? 'table-striped' : '' }}">{{ $key + 1 }}</td>
                        <td class="{{ $stripedTable ? 'table-striped' : '' }}">{{ $item[0] }}</td>
                        <td class="{{ $stripedTable ? 'table-striped' : '' }}">{{ $item[1] }}</td>
                        <td class="{{ $stripedTable ? 'table-striped' : '' }}"></td>
                    </tr>
                @endforeach
                <tr>
                    <td></td>
                    <td>{{ count($butiran) + 1 }}</td>
                    <td>Berkas Dan Lain-lain</td>
                    <td colspan="2">
                        <a _target="_blank" href="https://simba.uinmadura.ac.id">https://simba.uinmadura.ac.id</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="lembar-deskripsi">
            <p class="judul-deskripsi">LEMBAR DESKRIPSI</p>
            <p style="margin-top:0; font-weight: normal;">Deskripsikan kondisi faktual kategori survei di bagian ini!
            </p>

            <div class="deskripsi-item">
                <label>A. KESESUAIAN DOKUMEN DAN KONDISI FAKTUAL</label>
                <div class="kotak-isian"></div>
            </div>
            <div class="deskripsi-item">
                <label>B. KONDISI ORANG TUA/WALI</label>
                <div class="kotak-isian"></div>
            </div>
            <div class="deskripsi-item">
                <label>C. KONDISI TEMPAT TINGGAL</label>
                <div class="kotak-isian"></div>
            </div>
            <div class="deskripsi-item">
                <label>D. KONDISI EKONOMI</label>
                <div class="kotak-isian"></div>
            </div>

            <div class="area-tanda-tangan">
                <div class="ttd-kiri">
                    Pamekasan, ______ Oktober 2024<br><br>
                    Orang tua/Wali,<br><br><br> <strong>MOH.SAYURI</strong>/____________
                </div>
                <div class="ttd-kanan">
                    Surveyor,<br><br><br><br> <strong>SUMARTONO, SH., MM.</strong><br>
                    NIP. 198201282005011005
                </div>
            </div>
        </div>
    </div>
</body>

</html>
