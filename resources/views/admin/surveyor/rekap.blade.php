@extends('admin.template.master-template')

@section('title', 'Rekap Surveyor')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Surveyor
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Rekap Surveyor</span></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="{{ route('admin.surveyor', ['assign' => 1]) }}" class="btn btn-primary btn-sm">
                        <i class="ti ti-user-plus"></i> Assign Surveyors
                    </a>
                </div>
                <div class="d-flex gap-1">
                    <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan" id="flt_tahun">
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
                        @foreach ($status_surveyor as $item)
                            <option value="{{ $item }}">{{ $item }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="alert alert-info">
                                Surveyor yang berstatus <span class="fw-bold">Draft</span> tidak akan menampilkan data
                                peserta survei di akun surveyor
                            </div>
                            <div class="d-flex justify-content-end mb-2">
                                <div class="d-flex align-items-center gap-1">
                                    <div class="text-muted">Klik untuk mengubah semua status surveyor <i
                                            class="fas fa-arrow-right"></i>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" onclick="publishSurveyorAll(0)" type="button"
                                            class="btn btn-sm btn-warning">Draft Semua</button>
                                        <button type="button" onclick="publishSurveyorAll(1)"
                                            class="btn btn-sm btn-success">Publish Semua</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatable-rekap-surveyor">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Nama Surveyor</th>
                                            <th>Beasiswa</th>
                                            <th class="text-center">Jml. Mhs</th>
                                            <th class="text-center"> <i class="fas fa-check text-success"></i> Selesai
                                            </th>
                                            <th class="text-center"> <i class="fas fa-times text-danger"></i> Belum
                                            </th>
                                            <th class="text-center">Status <span class="text-muted small">(Klik)</span>
                                            </th>
                                            <th class="text-center">Aksi</th>
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

    <div class="modal fade" id="modalDetail" aria-labelledby="modalAssignSurveyorLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAssignSurveyorLabel">Detail Surveyor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id=modalDetailContent>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        let modalDetail = null;
        let datatable = null;
        const masterTahun = @json($master_tahun);
        const masterBeasiswa = @json($master_beasiswa);
        const statusSurveyor = @json($status_surveyor);

        $(function() {
            modalDetail = $('#modalDetail').modal({
                backdrop: 'static',
                keyboard: false
            });

            datatable = $('#datatable-rekap-surveyor').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.surveyor.rekap') }}',
                    data: function(d) {
                        d.tahun = $('#flt_tahun').val();
                        d.beasiswa = $('#flt_beasiswa').val();
                        d.status = $('#flt_status').val();
                    },
                },
                columnDefs: [{
                    targets: 5,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('text-success');
                    }
                }, {
                    targets: 4,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('text-danger');
                    }
                }, {
                    targets: 6,
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).addClass('text-center');
                    }
                }],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        name: 'user.name',
                        data: 'name',
                    },
                    {
                        data: 'beasiswa',
                        name: 'beasiswa',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'details_count',
                        searchable: false
                    },
                    {
                        data: 'selesai_count',
                        searchable: false
                    },
                    {
                        data: 'belum_selesai_count',
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
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

        function publishSurveyor(evt, status) {
            const id = evt.currentTarget.dataset.id;
            Swal.fire({
                title: status ? 'Mempublish surveyor...' : 'Menonaktifkan surveyor...',
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false
            });
            let url = '{{ route('admin.surveyor.rekap.publish', ['id' => ':id']) }}';
            url = url.replace(':id', evt.currentTarget.dataset.id);
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status
                },
                success: (response) => {
                    Swal.fire({
                        icon: response.icon,
                        title: response.title,
                        text: response.message,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                    datatable.ajax.reload();
                },
                complete: () => {
                    Swal.close();
                }
            });
        }

        function publishSurveyorAll(status) {
            Swal.fire({
                title: 'Konfirmasi',
                text: status ? 'Anda yakin ingin mempublish surveyor ini?' :
                    'Anda yakin ingin menonaktifkan surveyor ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: `Ya, ${status ? 'Publish' : 'Nonaktifkan'}!`,
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: status ? 'Mempublish surveyor...' : 'Menonaktifkan surveyor...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });
                    let url = '{{ route('admin.surveyor.rekap.publish-all') }}';
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: status,
                            tahun: $('#flt_tahun').val(),
                            beasiswa: $('#flt_beasiswa').val(),
                        },
                        success: (response) => {
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.message,
                                timer: 2000,
                                timerProgressBar: true,
                            });
                            datatable.ajax.reload();
                        },
                        complete: () => {
                            Swal.close();
                        }

                    });
                }
            });
        }

        function reloadData() {
            datatable.ajax.reload();
            gantiNama();
        }

        function openDetal(evt) {
            Swal.fire({
                title: 'Menyimpan data...',
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false
            });
            let url = '{{ route('admin.surveyor.detail', ['id' => ':id']) }}';
            url = url.replace(':id', evt.currentTarget.dataset.id);
            $.ajax({
                url: url,
                type: 'GET',
                success: (response) => {
                    $('#modalDetailContent').html(response);
                    modalDetail.modal('show');
                },
                complete: () => {
                    Swal.close();
                }

            });
        }

        function gantiNama() {
            const tahun = $('#flt_tahun').val();
            const beasiswa = $('#flt_beasiswa').val();
            const itemTahun = masterTahun.find(item => item.id == tahun);
            const itemBeasiswa = masterBeasiswa.find(item => item.id == beasiswa);
            $('.kip-nama').text(`${itemBeasiswa.nama} ${itemTahun.tahun}`);
        }
    </script>
@endpush
