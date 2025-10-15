@extends('pendaftar.template.master-template')

@section('title', 'Jadwal Kegiatan')

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
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Jadwal Kegiatan</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Jadwal Kegiatan</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!--[ Main Content ] start-->
            <div class="row" id="app-vue">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <h5 class="mb-3 mb-sm-0">Data Jadwal Kegiatan</h5>
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
                            <div class="table-responsive">
                                <table class="table table-striped align-middle text-center" id="tableJadwal">
                                    <thead class="bg-cyan-100">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Beasiswa</th>
                                            <th scope="col">Jadwal</th>
                                            <th scope="col">Tanggal Mulai</th>
                                            <th scope="col">Tanggal Selesai</th>
                                            <th scope="col">Deskripsi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--[ Main Content ] end-->
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script>
        function reloadData() {
            dataTable.ajax.reload(null, false);
        }
    </script>

    <script>
        const dataTable = $("#tableJadwal").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pendaftar.jadwal-kegiatan') }}",
                type: "GET",
                data: (data) => {
                    data.flt_tahun = $('#flt_tahun').val();
                    data.flt_beasiswa = $('#flt_beasiswa').val();
                }
            },
            createdRow: function(row, data, dataIndex) {
                // Ambil bagian tanggal saja, buang jam
                const tanggalMulai = data.tanggal_mulai.split(' ')[0]; // "15-09-2025"
                const tanggalSelesai = data.tanggal_selesai.split(' ')[0]; // "15-09-2025"

                // Ambil tanggal hari ini dalam format DD-MM-YYYY
                const now = new Date();
                const today = String(now.getDate()).padStart(2, '0') + '-' +
                    String(now.getMonth() + 1).padStart(2, '0') + '-' +
                    now.getFullYear();

                // Bandingkan tanggal
                if (today >= tanggalMulai && today <= tanggalSelesai) {
                    $(row).css({
                        'background-color': '#c0e5d9',
                        'font-weight': 'bold'
                    });
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'beasiswa'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'tanggal_mulai'
                },
                {
                    data: 'tanggal_selesai'
                },
                {
                    data: 'deskripsi'
                }
            ],
            "columnDefs": [{
                    "targets": "_all",
                    "className": "dt-head-center dt-body-center cell-border",
                    "visible": true
                },
                {
                    "targets": 0,
                    "width": "5%"
                },
                {
                    "targets": 5,
                    "width": "15%"
                }
            ],
            "responsive": true,
            "autoWidth": true,
            "fixedColumns": true,
            "fixedHeader": true,
            "language": {
                "url": 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json',
            },
        });
    </script>
@endpush
