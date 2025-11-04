@php
    $status_seleksi_tpa = collect($pendaftar->pendaftar_status)
        ->filter(fn($item) => in_array($item->status, ['LOLOS TPA', 'GAGAL TPA']))
        ->first();
@endphp
@if ($is_pengumuman_seleksi_tpa && in_array($status_seleksi_tpa?->status, ['GAGAL TPA', 'LOLOS TPA']))
    @if ($status_seleksi_tpa?->status === 'LOLOS TPA')
        <div class="alert alert-success">
            <h4><i class="ti ti-mood-smile"></i> Selamat!</h4>
            <p class="mb-0">Anda dinyatakan <span class="fw-bold fs-5">LAYAK</span> mengikuti proses seleksi berikutnya.
            </p>
        </div>
    @else
        <div class="alert alert-danger">
            <h4><i class="ti ti-mood-sad"></i> Sayang Sekali!</h4>
            <p class="mb-0">Anda
                dinyatakan <span class="fw-bold fs-5">TIDAK LAYAK</span> mengikuti proses seleksi berikutnya.
            </p>
        </div>
    @endif
@else
    <div class="alert alert-warning">
        <h4><i class="ti ti-alert-circle"></i> Perhatian!</h4>
        <p class="mb-0">
            Menunggu Jadwal Pengumuman Seleksi Tes Potensi Akademik
        </p>
    </div>
@endif
