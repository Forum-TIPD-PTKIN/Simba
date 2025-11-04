@php
    $arrContextOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ];
@endphp
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir Peserta TPA</title>
    <link rel="stylesheet" href="file:///{{ $style }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 30px;
            color: #333;
        }

        .header-box {
            width: 100%;
            border-bottom: 3px groove #aaa;
            margin: auto;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 80px;
            height: auto;
            display: block;
        }

        .logo {
            width: 70px;
            height: auto;
        }

        .instansi-cell {
            padding: 6px;
            font-size: 12px;
            color: #333;
            text-align: center;
            vertical-align: middle;
            line-height: 1.5;
        }

        .instansi-cell>* {
            margin-bottom: 8px;
        }

        .instansi-cell>*:last-child {
            margin-bottom: 0;
        }

        .instansi-cell h2,
        .instansi-cell h3 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
        }

        .instansi-cell p {
            margin: 2px 0;
            font-size: 11px;
        }

        .page-break {
            page-break-before: always;
        }

        .spacer {
            height: 40px;
        }

        .footer {
            margin-top: 30px;
            position: absolute;
            right: 0;
            width: 300px;
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="header-box" cellspacing="0" cellpadding="0" style="width: 100%;">
        <tr>
            <td>
                <table style="width: 600px; border-collapse: collapse;" align="center">
                    <tr>
                        <td style="width: 100px; vertical-align: middle; padding: 4px;">
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://api.iainmadura.ac.id/assets/imgs/logo.png', false, stream_context_create($arrContextOptions))) }}"
                                alt="Logo UIN Madura" class="logo-cell">
                        </td>

                        <td class="instansi-cell">
                            <h2>Kementerian Agama Republik Indonesia</h2>
                            <h3>Universitas Islam Negeri Madura</h3>
                            <p>Jl. Raya Panglegur Km. 4 Pamekasan 69371 Jawa Timur</p>
                            <p>Telp. (0324) 327248 Fax. (0324) 322551</p>
                            <p>Website: <a href="https://uinmadura.ac.id" target="_blank">www.uinmadura.ac.id</a> |
                                Email: <a href="mailto:info@uinmadura.ac.id" target="_blank">info@uinmadura.ac.id</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="label text-center fw-bold mb-5">
        <h3 class="mt-3 mb-0 p-0">DAFTAR HADIR PESERTA</h3>
        <h3 class="m-0 p-0">TES POTENSI AKADEMIK</h3>
        <h3 class="m-0 p-0 text-uppercase">BEASISWA {{ $beasiswa }} TAHUN {{ $tahun }}</h3>
    </div>
    <table class="info-ujian text-start fw-bold mb-3" cellspacing="0" cellpadding="0" style="width: 50%;">
        <tr width="10%">
            <td>Hari/Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($tanggal_ujian)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td>Ruang</td>
            <td> : {{ $ruang }}</td>
        </tr>
        <tr>
            <td>Sesi </td>
            <td>: {{ $sesi }}</td>
        </tr>
    </table>

    @if (env('APP_ENV') === 'production')
        @php
            $globalIndex = 1;
        @endphp
        @foreach ($data->chunk(20) as $chunk)
            <table class="table data-peserta" cellspacing="0" cellpadding="0" style="width: 100%">
                <thead class="bg-secondary">
                    <tr>
                        <th scope="col" class="text-center text-white">No</th>
                        <th scope="col" class="text-center text-white">NIM</th>
                        <th scope="col" class="text-center text-white">Nama</th>
                        <th scope="col" class="text-center text-white">Program Studi</th>
                        <th scope="col" class="text-center text-white">Tanda Tangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chunk as $item)
                        <tr>
                            <td class="text-center">{{ $globalIndex }}</td>
                            <td class="text-center">{{ $item->pendaftar?->mahasiswa?->nim }}</td>
                            <td class="text-center">{{ $item->pendaftar?->mahasiswa?->nama }}</td>
                            <td class="text-center">{{ $item->pendaftar?->mahasiswa?->prodi_name }}</td>
                            <td></td>
                        </tr>
                        @php $globalIndex++; @endphp
                    @endforeach
                </tbody>
            </table>
            <div class="page-break"></div>
            <div class="spacer"></div>
        @endforeach
    @else
        <table class="table data-peserta" cellspacing="0" cellpadding="0" style="width: 100%">
            <thead class="bg-secondary">
                <tr>
                    <th scope="col" class="text-center text-white">No</th>
                    <th scope="col" class="text-center text-white">NIM</th>
                    <th scope="col" class="text-center text-white">Nama</th>
                    <th scope="col" class="text-center text-white">Program Studi</th>
                    <th scope="col" class="text-center text-white">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $item->pendaftar?->mahasiswa?->nim }}</td>
                        <td class="text-center">{{ $item->pendaftar?->mahasiswa?->nama }}</td>
                        <td class="text-center">{{ $item->pendaftar?->mahasiswa?->prodi_name }}</td>
                        <td></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>............, ........................</p>
        <p>Pengawas ruang,</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>&nbsp;</p>
        <p>(..........................................)</p>
    </div>

</body>

</html>
