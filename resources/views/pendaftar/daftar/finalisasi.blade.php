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
                                        tanggal 12 Maret 2025 17:00 WIB.</strong>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3">Informasi Pendaftar</h5>
                            <table class="table table-borderless table-sm">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
