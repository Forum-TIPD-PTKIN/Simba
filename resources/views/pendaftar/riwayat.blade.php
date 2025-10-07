@extends('pendaftar.template.master-template')

@section('title', 'Riwayat Pendaftaran')

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
                                <li class="breadcrumb-item" aria-current="page">Riwayat Pendaftaran</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Riwayat Pendaftaran</h2>
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
                        <div class="card-header">
                            <h5 class="mb-3 mb-sm-0">Data Riwayat Pendaftaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover text-center align-middle"
                                    id="dataTable">
                                    <thead class="bg-cyan-100">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">NIM</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Fakultas/Prodi</th>
                                            <th scope="col">Beasiswa</th>
                                            <th scope="col">Tahun Kegiatan</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($riwayat as $key => $item)
                                            <tr>
                                                <th scope="row">{{ $key + 1 }}</th>
                                                <td>{{ $item->mahasiswa->nim }}</td>
                                                <td>{{ $item->mahasiswa->nama }}</td>
                                                <td>{{ $item->mahasiswa->fakultas_prodi }}</td>
                                                <td>{{ $item->beasiswa->nama }}</td>
                                                <td>{{ $item->tahun_kegiatan->tahun }}</td>
                                                <td>
                                                    @switch($item->latest_status?->status)
                                                        @case(in_array($item->latest_status?->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']))
                                                        @case(cek_jadwal($item->tahun_kegiatan?->id, $item->beasiswa?->id, false, true))
                                                            -
                                                        @break

                                                        @default
                                                            {{ $item->latest_status?->status }}
                                                    @endswitch
                                                </td>
                                                <td>
                                                    <a href="{{ route('pendaftar.daftar', ['id' => $item->beasiswa->id, 'step' => 2]) }}"
                                                        class="btn btn-sm btn-info">Detail</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@push('script')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        $("#dataTable").DataTable({
            responsive: true,
            autoWidth: true,
            info: true,
            fixedColumns: true,
            fixedHeader: true,
            ordering: false,
            searching: false,
            language: {
                "url": 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json',
            },
            columnDefs: [{
                "targets": [2, 3],
                "className": "dt-head-center dt-body-left",
            }, {
                "targets": "_all",
                "className": "dt-head-center",
            }],
        });
    </script>
@endpush
