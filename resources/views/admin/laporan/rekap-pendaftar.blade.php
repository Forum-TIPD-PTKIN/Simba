@extends('admin.template.master-template')

@section('title', 'Rekapitulasi Pendaftar')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

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
                                <li class="breadcrumb-item" aria-current="page">Rekapitulasi Pendaftar</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Rekapitulasi Pendaftar</h2>
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
                            <h5 class="mb-3 mb-sm-0">Data Pendaftar</h5>
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
                                <select class="form-select form-select-sm" aria-label="Filter status" id="flt_status">
                                    <option value="" selected>-- SEMUA --</option>
                                    @foreach ($status_pendaftar as $item)
                                        <option value="{{ $item }}">{{ $item }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                            </div>
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
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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

@push('script')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        // Reload data
        function reloadData() {
            dataTable.ajax.reload(null, false);
        }

        // Datatable
        const dataTable = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.laporan.rekap.data') }}",
                type: "POST",
                data: (data) => {
                    data._token = "{{ csrf_token() }}";
                    data.flt_tahun = $('#flt_tahun').val();
                    data.flt_beasiswa = $('#flt_beasiswa').val();
                    data.flt_status = $('#flt_status').val();
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'mahasiswa.nim'
                },
                {
                    data: 'mahasiswa.nama'
                },
                {
                    data: 'mahasiswa.fakultas_prodi',
                    name: 'mahasiswas.prodi'
                },
                {
                    data: 'beasiswa',
                    searchable: false
                },
                {
                    data: 'status',
                    searchable: false
                },
            ],
            responsive: true,
            autoWidth: true,
            info: true,
            fixedColumns: true,
            fixedHeader: true,
            ordering: false,
            searching: true,
            language: {
                "url": 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json',
            },
            columnDefs: [{
                "targets": "_all",
                "className": "dt-head-center dt-body-center cell-border",
            }],
        });
    </script>
@endpush
