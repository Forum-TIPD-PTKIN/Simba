@php
    $deskripsi_verifikasi = json_decode($pendaftar->latest_status?->deskripsi);
@endphp
@if (
    $is_pengumuman_seleksi_administrasi &&
        in_array($pendaftar->latest_status?->status, ['GAGAL ADMINISTRASI', 'LOLOS ADMINISTRASI']))
    @if ($pendaftar->latest_status?->status === 'LOLOS ADMINISTRASI')
        <div class="alert alert-success">
            <h4><i class="ti ti-mood-smile"></i> Selamat!</h4>
            <p class="mb-0">Anda dinyatakan <span class="fw-bold fs-5">LOLOS SELEKSI
                    ADMINISTRASI</span>, silakan lihat jadwal kegiatan selanjutnya.</p>
        </div>
        <button class="btn btn-success" id="unduhKartuPesertaTPA"><i class="ti ti-file-text"></i> Download Kartu Peserta
            Tes Potensi
            Akademik</button>
    @else
        <div class="alert alert-danger">
            <h4><i class="ti ti-mood-sad"></i> Sayang Sekali!</h4>
            <p class="mb-0">Setelah dilakukan verifikasi dan validasi
                berkas pendaftaran, Anda
                dinyatakan <span class="fw-bold fs-5">TIDAK LOLOS SELEKSI ADMINISTRASI</span>.
            </p>
            <p class="fw-bold mt-3 mb-0">Catatan Verifikator :</p>
            {!! $deskripsi_verifikasi->catatan !!}
            <p>Jika ada pertanyaan lebih lanjut, hubungi panitia seleksi.</p>
        </div>
    @endif
@else
    <div class="alert alert-warning">
        <h4><i class="ti ti-alert-circle"></i> Perhatian!</h4>
        <p class="mb-0">
            {{ $jadwal_pengumuman_seleksi_administrasi ? 'Jadwal pengumuman seleksi administrasi dimulai ' . $jadwal_pengumuman_seleksi_administrasi?->formatTanggal('tanggal_mulai', 'l, d F Y H:i') : 'Jadwal pengumuman seleksi administrasi tidak ditemukan' }}.
        </p>
    </div>
@endif
