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
    <title>Kartu Peserta Ujian</title>
    <link rel="stylesheet" href="file:///{{ $style }}">
    <link rel="stylesheet" href="file:///{{ $style_kartu }}">
</head>

<body>
    <table class="header-box" cellspacing="0" cellpadding="0" style="width: 100%;">
        <tr>
            <td>
                <table style="width: 600px; border-collapse: collapse;" align="center">
                    <tr>
                        <td style="width: 100px; vertical-align: middle; padding: 4px;">
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(env('API_URL') . '/assets/imgs/logo.png', false, stream_context_create($arrContextOptions))) }}"
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

    <table class="kartu-wrapper" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2" class="judul">
                KARTU PESERTA TES POTENSI AKADEMIK
                <p class="m-0 p-0">BEASISWA {{ $pendaftar->beasiswa?->nama }}</p>
            </td>
        </tr>
        <tr>
            <td class="foto-cell">
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents('https://be.iainmadura.ac.id/api/v1/external/mahasiswa/foto?nim=' . $pendaftar->mahasiswa?->nim . '&key=6321afccabf95b9ec00ac8d193479f4f6a849d46ffbe50fc7e14a74011554fc1', false, stream_context_create($arrContextOptions))) }}"
                    class="foto" alt="Foto Peserta">
            </td>
            <td class="identitas-cell">
                <table class="identitas">
                    <tr>
                        <td>NIM</td>
                        <td class="fw-bold">: {{ $pendaftar->mahasiswa?->nim }}</td>
                    </tr>
                    <tr>
                        <td>Nama Lengkap</td>
                        <td class="fw-bold">: {{ $pendaftar->mahasiswa?->nama }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td class="fw-bold">: {{ $pendaftar->user?->email }}</td>
                    </tr>
                    <tr>
                        <td>Program Studi/Fakultas</td>
                        <td class="fw-bold">:
                            {{ $pendaftar->mahasiswa?->prodi_name }}/{{ $pendaftar->mahasiswa?->fakultas_name }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Registrasi</td>
                        <td class="fw-bold">:
                            {{ \Carbon\Carbon::parse($pendaftar->created_at)->translatedFormat('d-m-Y H:i:s') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="ujian">
                    <tr>
                        <th colspan="2">Informasi Tes</th>
                    </tr>
                    <tr>
                        <td>Tanggal Ujian</td>
                        <td class="fw-bold">:
                            {{ \Carbon\Carbon::parse($map_ujian->tanggal_mulai)->locale('id')->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Waktu Ujian</td>
                        <td class="fw-bold">:
                            {{ \Carbon\Carbon::parse($map_ujian->tanggal_mulai)->locale('id')->translatedFormat('H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($map_ujian->tanggal_selesai)->locale('id')->translatedFormat('H:i') }}
                        </td>
                    </tr>
                    <tr>
                        <td>Sesi</td>
                        <td class="fw-bold">: {{ $map_ujian->sesi }}</td>
                    </tr>
                    <tr>
                        <td>Ruang</td>
                        <td class="fw-bold">: {{ $map_ujian->ruang }}</td>
                    </tr>
                    <tr>
                        <td>Tempat Ujian</td>
                        <td class="fw-bold">: Laboratorium Komputer</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p class="fst-italic mt-5">*) Harap membawa tanda pengenal (KTP/KTM) dan hadir 30 menit sebelum waktu ujian dimulai
        untuk
        melakukan verifikasi peserta tes.</p>
</body>

</html>
