@php
    $status_seleksi_akhir = collect($pendaftar->pendaftar_status)
        ->filter(fn($item) => in_array($item->status, ['LOLOS PENERIMA', 'TIDAK LOLOS PENERIMA']))
        ->first();
@endphp
@if (
    $is_pengumuman_seleksi_akhir &&
        in_array($status_seleksi_akhir?->status, ['LOLOS PENERIMA', 'TIDAK LOLOS PENERIMA']))
    @if ($status_seleksi_akhir?->status === 'LOLOS PENERIMA')
        <div class="alert alert-success">
            <h4><i class="far fa-grin-stars"></i> Selamat!</h4>
            <p class="mb-0">Anda dinyatakan <span class="fw-bold fs-5">LOLOS sebagai PENERIMA</span> Beasiswa
                {{ $pendaftar->beasiswa?->nama }} Tahun {{ $pendaftar->tahun_kegiatan?->tahun }}.
            </p>
        </div>
        <div class="card">
            <div class="card-body">
                <p>Bagi mahasiswa yang dinyatakan LOLOS sebagai PENERIMA Beasiswa {{ $pendaftar->beasiswa?->nama }}
                    Tahun {{ $pendaftar->tahun_kegiatan?->tahun }} wajib hadir pada:</p>
                <dl class="dl-horizontal row">
                    <dt class="col-sm-3">Hari/Tanggal</dt>
                    <dd class="col-sm-9">Jum'at, 21 November 2025</dd>
                    <dt class="col-sm-3">Waktu</dt>
                    <dd class="col-sm-9">07.30 WIB</dd>
                    <dt class="col-sm-3">Tempat</dt>
                    <dd class="col-sm-9">Aula Lt. 4 Perpustakaan UIN Madura</dd>
                    <dt class="col-sm-3">Acara</dt>
                    <dd class="col-sm-9">Penjelasan Teknis tentang Beasiswa {{ $pendaftar->beasiswa?->nama }} Tahun
                        {{ $pendaftar->tahun_kegiatan?->tahun }} dan Pembuatan Rekening</dd>
                    <dt class="col-sm-3">Catatan</dt>
                    <dd class="col-sm-9">
                        yang harus dibawa :
                        <ol>
                            <li>KTP</li>
                            <li>Foto copy KTP (1 Lembar jangan dipotong)</li>
                            <li>Foto copy KK (1 Lembar jangan dipotong)</li>
                            <li>Smartphone dengan paket data</li>
                        </ol>
                    </dd>
                </dl>
            </div>
        </div>
    @else
        <div class="alert alert-danger">
            <h4><i class="far fa-sad-tear"></i> Mohon Maaf!</h4>
            <p class="mb-0">Anda
                dinyatakan <span class="fw-bold fs-5">TIDAK LOLOS sebagai PENERIMA</span> Beasiswa
                {{ $pendaftar->beasiswa?->nama }} Tahun {{ $pendaftar->tahun_kegiatan?->tahun }}.
            </p>
        </div>
    @endif
@else
    <div class="alert alert-warning">
        <h4><i class="ti ti-alert-circle"></i> Perhatian!</h4>
        <p class="mb-0">
            Menunggu Jadwal Pengumuman Seleksi Akhir
        </p>
    </div>
@endif
