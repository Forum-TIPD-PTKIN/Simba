@extends('pendaftar.template.master-template')

@section('title', 'Pendaftaran')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('pendaftar.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Pendaftaran</li>
                                <li class="breadcrumb-item" aria-current="page">{{ $pendaftar?->beasiswa->nama }}</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Pendaftaran Beasiswa {{ $pendaftar?->beasiswa->nama }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- konten --}}

            <!-- [ Main Content ] end -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-success">
                                <h3 class="text-success"><i class="fas fa-check-circle"></i> Pendaftaran Berhasil
                                    Difinalisasi</h3>
                                <p class="mb-2">
                                    Pendaftaran Anda untuk <strong>Beasiswa
                                        {{ $pendaftar?->beasiswa->nama }}</strong> telah berhasil diselesaikan dan sedang
                                    dalam proses peninjauan. Anda tidak dapat mengubah data lagi.
                                </p>
                                <p class="mb-0">
                                    <strong class="text-danger">Pengumuman kelulusan (Administrasi) akan diumumkan pada
                                        hari {{ $pengumuman_seleksi }}.</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <h5 class="mb-3">Informasi Pendaftar</h5>
                            <table class="table table-borderless table-sm infodata">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold" style="width: 150px;">NIM</td>
                                        <td>: {{ $pendaftar?->mahasiswa->nim }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Nama</td>
                                        <td>: {{ $pendaftar?->mahasiswa->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Fakultas/Prodi</td>
                                        <td>: {{ $pendaftar?->mahasiswa->fakultas_prodi }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Beasiswa</td>
                                        <td>: {{ $pendaftar?->beasiswa->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Tahun Kegiatan</td>
                                        <td>: {{ $pendaftar?->tahun_kegiatan->tahun }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <h5 class="mb-3">Informasi PMB</h5>
                            <table class="table table-borderless table-sm infodata">
                                <tbody>
                                    <tr>
                                        <td class="fw-bold" style="width: 150px;">Kode Akun PMB</td>
                                        <td>: {{ $akunpmb ?? 'NOT FOUND' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold" style="width: 150px;">Jalur Masuk</td>
                                        <td>: {{ $jalur->nama ?? 'NOT FOUND' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold" style="width: 150px;">Tahun Masuk</td>
                                        <td>: {{ $jalur->tahun_masuk ?? 'NOT FOUND' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Sekolah Asal</td>
                                        <td>: {{ $jalur->sekolah_asal->master_sekolah->nama ?? 'NOT FOUND' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Tahun Lulus Sekolah</td>
                                        <td>: {{ $jalur->sekolah_asal->tahun_lulus ?? 'NOT FOUND' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Jurusan (Saat Sekolah)</td>
                                        <td>: {{ $jalur->sekolah_asal->master_jurusan_sekolah->nama ?? 'NOT FOUND' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <h5 class="mb-3">Informasi Berkas</h5>
                            <div class="base-file" id="form-berkas">
                                @foreach ($berkas as $item)
                                    @if ($item->url)
                                        <div class="file">
                                            <div class="icon">
                                                <img src="{{ asset('assets/icons/' . iconFiles($item->extension)) }}"
                                                    alt="">
                                            </div>
                                            <div class="name">
                                                <a data-extension="{{ $item->extension }}" data-url="{{ $item->url }}"
                                                    data-type="{{ $item->text }}" href="javascript:void(0)"
                                                    class="base-berkas btn btn-link p-0"
                                                    onclick="viewControl(this)">{{ $item->text }}</a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="nonfile">
                                            <div class="label text-muted">{{ $item->text }}</div>
                                            <div class="name fw-bold">{{ $item->value }}</div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('head')
    <style>
        .infodata tr {
            white-space: nowrap !important;
        }

        .base-file {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
        }

        .base-file .file,
        .nonfile {
            display: flex;
            justify-content: start;
            align-items: center;
            margin-bottom: 4px;
            border-bottom: 1px solid #d8d8d8;
            padding-bottom: 4px;
            flex: 1 1 100%;
        }

        .nonfile {
            flex-direction: column;
            justify-content: start !important;
            align-items: start !important
        }

        .base-file .file .icon {
            overflow: hidden;
            width: 18px;
            height: 18px;
            border-radius: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 2px;
        }

        .base-file .file .icon img {
            width: 100%;
        }
    </style>
@endpush

@push('script')
    <script>
        function viewControl(e) {
            let container = document.getElementById('form-berkas');
            let links = container.querySelectorAll('.base-berkas');
            let urls = []
            links.forEach(link => {
                let url = link.getAttribute('data-url');
                let type = link.getAttribute('data-type');
                let extension = link.getAttribute('data-extension');
                urls.push({
                    url,
                    type,
                    extension
                })
            });
            const data = {
                active: {
                    url: e.getAttribute('data-url'),
                    type: e.getAttribute('data-type'),
                    extension: e.getAttribute('data-extension')
                },
                data: urls
            }
            $.ajax({
                type: 'post',
                url: "{{ route('view.control') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    data: data
                },
                dataType: 'HTML',
                success: function(data) {
                    console.log(data)
                    const winUrl = URL.createObjectURL(
                        new Blob([data], {
                            type: "text/html"
                        })
                    );

                    const margin = 100; // Jarak tepi agar tidak full full banget
                    const width = window.screen.availWidth - margin * 8;
                    const height = window.screen.availHeight - margin * 2;
                    const left = (window.screen.availWidth - width) / 2;
                    const top = (window.screen.availHeight - height) / 2;

                    const win = window.open(
                        winUrl,
                        "win",
                        `width=${width},height=${height},top=${top},left=${left}`
                    );
                }
            });
        }
    </script>
@endpush
