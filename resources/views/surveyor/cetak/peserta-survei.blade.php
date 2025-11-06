<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta Survei</title>
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            text-align: left;
            color: #000;
            padding: 20px 0 10px 0;
            margin: 20px 40px;
            border-bottom: 3px double #000;
        }

        header h4 {
            margin: 0;
            font-size: 20px;
            letter-spacing: 1px;
        }

        header h5 {
            margin: 3px 0;
            font-weight: 500;
            letter-spacing: 3px;
        }

        main {
            padding: 20px 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #1976d2;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e3f2fd;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            font-size: 15px;
        }

        .signature {
            margin-top: 30px;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <header>
        <h4>DAFTAR PESERTA SURVEI LAPANGAN</h4>
        <h4>CALON PENERIMA BEASISWA {{ strtoupper($beasiswa) }} TAHUN {{ $tahun }}</h4>
        <h5>UNIVERSITAS ISLAM NEGERI (UIN) MADURA</h5>
    </header>

    <main>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Program Studi</th>
                    <th>Alamat</th>
                    <th>Nomor HP</th>
                    <th>Nama Ayah</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($peserta as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $item->mahasiswa?->nim }}</td>
                        <td>{{ $item->mahasiswa?->nama }}</td>
                        <td class="text-center">{{ $item->mahasiswa?->prodi_name }}</td>
                        <td>{{ $item->biodata_pendaftar?->data?->biodata?->alamat_ktp?->value }}</td>
                        <td class="text-center">{{ $item->biodata_pendaftar?->data?->biodata?->no_hp?->value }}</td>
                        <td class="text-center">{{ $item->biodata_pendaftar?->data?->biodata?->nama_ayah?->value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <section class="footer">
            <p><em>Dokumentasi pelaksanaan survei terlampir. Demikian laporan pelaksanaan kegiatan.</em></p>

            <div class="signature">
                <p>Pamekasan, _____ Oktober 2024</p>
                <p><strong>Surveyor,</strong></p>
                <br><br>
                <p><strong>SUMARTONO, SH., MM.</strong></p>
                <p>NIP. 198201282005011005</p>
            </div>
        </section>
    </main>
</body>

</html>
