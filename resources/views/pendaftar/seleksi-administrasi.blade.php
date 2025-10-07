@extends('pendaftar.template.master-template')

@section('title', 'Seleksi Administrasi')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Seleksi Administrasi</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Seleksi Administrasi</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <h5 class="mb-3 mb-sm-0">Hasil Seleksi Administrasi</h5>
                            <div class="d-flex gap-1">
                                <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan"
                                    id="flt_tahun">
                                    @foreach ($master_tahun as $item)
                                        <option value="{{ $item->id }}" @selected($loop->first)>{{ $item->tahun }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-select form-select-sm" aria-label="Filter beasiswa" id="flt_beasiswa">
                                    @foreach ($master_beasiswa as $item)
                                        <option value="{{ $item->id }}" @selected($loop->first)>{{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (
                                $is_pengumuman_seleksi_administrasi &&
                                    in_array($pendaftar->latest_status?->status, ['GAGAL ADMINISTRASI', 'LOLOS ADMINISTRASI']))
                                @if ($pendaftar->latest_status?->status === 'LOLOS ADMINISTRASI')
                                    <div class="alert alert-success">
                                        <h4><i class="ti ti-mood-smile"></i> Selamat!</h4>
                                        <p class="mb-0">Anda dinyatakan <span class="fw-bold fs-5">LOLOS SELEKSI
                                                ADMINISTRASI</span>, silakan lihat jadwal kegiatan selanjutnya.</p>
                                    </div>
                                @else
                                    <div class="alert alert-danger">
                                        <h4><i class="ti ti-mood-sad"></i> Sayang Sekali!</h4>
                                        <p class="mb-0">Setelah dilakukan verifikasi dan validasi
                                            berkas pendaftaran, Anda
                                            dinyatakan <span class="fw-bold fs-5">TIDAK LOLOS SELEKSI ADMINISTRASI</span>.
                                        </p>
                                        <p>Jika ada pertanyaan lebih lanjut, hubungi panitia seleksi.</p>
                                    </div>
                                @endif
                            @else
                                {{ $jadwal_pengumuman_seleksi_administrasi?->formatTanggal('tanggal_mulai', 'l, d F Y H:i') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
@endpush

@push('script')
    <script>
        // Reload data
        function reloadData() {
            $.ajax({
                url: "{{ route('pendaftar.seleksi-administrasi') }}",
                data: {
                    flt_tahun: $('#flt_tahun').val(),
                    flt_beasiswa: $('#flt_beasiswa').val()
                },
                success: (res) => {
                    const alert_jadwal = $('.container-alert-jadwal'),
                        text = res && Object.keys(res).length > 0 ?
                        `Seleksi administrasi untuk beasiswa ${res.beasiswa?.nama} tahun ${res.tahun_kegiatan?.tahun} dimulai dari ${res.tanggal_mulai} s.d. ${res.tanggal_selesai}` :
                        `Jadwal kegiatan belum dibuat oleh Administrator`;

                    alert_jadwal
                        .removeClass(function(index, className) {
                            return (className.match(/(^|\s)alert-\S+/g) || []).join(' ');
                        })
                        .addClass((res && Object.keys(res).length > 0) ? 'alert-warning' : 'alert-danger')
                        .html('')
                        .append(`
                        <h5><i class="ti ti-calendar-event"></i> Jadwal Kegiatan</h5>
                        ${text}
                    `);
                }
            });
        }
    </script>
@endpush
