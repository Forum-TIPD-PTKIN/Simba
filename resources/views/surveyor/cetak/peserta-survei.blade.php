<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peserta Survei</title>
    {{-- <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}"> --}}
    <link rel="stylesheet" href="file:///{{ $style }}">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background-color: #ffffff;
            color: #333;
            margin: 20px;
        }

        header {
            text-align: left;
            color: #000;
            margin: 0 40px;
            padding: 10px 0;
            border-bottom: 3px double #000;
            margin-bottom: 20px;
        }

        header h4 {
            margin: 0;
            font-size: 16px;
            letter-spacing: 1px;
        }

        header h5 {
            margin: 3px 0;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 2px;
        }

        main {
            padding: 20px 40px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            font-size: 12px;
            line-height: 1.3;
        }

        thead {
            border-bottom: 2px solid #636363;
            background-color: #e0e0e0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 40px;
            padding-right: 40px;
            float: right;
            font-size: 15px;
        }

        .signature {
            line-height: 1.6;
            text-align: left;
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
        <p>Dengan ini Saya melaporkan telah melaksanakan <span class="fw-bold">Survei Calon Penerima Beasiswa
                {{ $beasiswa }}
                UIN MADURA Tahun
                {{ $tahun }}</span>, dengan sasaran sebagai berikut:</p>
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
                        <td class="text-center">{{ $item->biodata_pendaftar?->data?->biodata?->nama_ayah?->value }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p class="mt-4 mb-1">Dokumentasi pelaksanaan survei terlampir. Demikian laporan pelaksanaan kegiatan.</p>
        <section class="footer mt-1">
            <div class="signature">
                <p class="mb-0">................................., ................................................
                </p>
                <p>Surveyor,</p>
                <br><br>
                <p class="mb-0"><strong>{{ $surveyor->user?->name }}</strong></p>
                <p>NIP. </p>
            </div>
        </section>
    </main>
</body>

</html>
