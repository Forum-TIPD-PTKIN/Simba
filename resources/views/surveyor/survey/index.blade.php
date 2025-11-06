@extends('surveyor.template.master-template')

@section('title', 'Persetujuan Surveyor')

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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Surveyor</a></li>
                                <li class="breadcrumb-item" aria-current="page">Survey</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Survey</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Daftar Survey</h5>
                        </div>

                        @if ($selectMasterDefault)
                            <div class="card-body">

                                <div class="d-flex gap-1 flex-wrap justify-content-start">
                                    <input type="hidden" id="surveyorId" value="{{ $selectMasterDefault->id }}">
                                    @foreach ($master as $item)
                                        <button data-id="{{ $item->id }}"
                                            class="btn {{ $selectMasterDefault->id === $item->id ? 'btn-primary text-white' : 'btn-outline-primary' }} h3">{{ $item->beasiswa }}
                                            {{ $item->tahun }}</button>
                                    @endforeach
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped" id="datatable-survey">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>NIM</th>
                                                <th>Nama</th>
                                                <th>Fak/Prodi</th>
                                                <th>HP</th>
                                                <th>Alamat</th>
                                                <th>Progress</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    Data tidak ditemukan
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush


@if ($selectMasterDefault)
    @push('script')
        <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
        <script>
            let datatable = null;
            $(function() {
                datatable = $('#datatable-survey').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('surveyor.survey') }}',
                        data: function(d) {
                            d.surveyor_id = $('#surveyorId').val();
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'pendaftar.mahasiswa.nim',
                        },
                        {
                            data: 'pendaftar.mahasiswa.nama',
                        }, {
                            data: 'fakultas',
                            name: 'pendaftar.mahasiswa.fakultas'
                        }, {
                            data: 'pendaftar.biodata_pendaftar.data.biodata.no_hp.value',
                            name: 'pendaftar.biodata_pendaftar.data',
                            orderable: false,
                        }, {
                            data: 'pendaftar.biodata_pendaftar.data.biodata.alamat_ktp.value',
                            name: 'pendaftar.biodata_pendaftar.data',
                            orderable: false,
                        },
                        {
                            data: 'progress',
                            name: 'progress',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });
        </script>
    @endpush
@endif
