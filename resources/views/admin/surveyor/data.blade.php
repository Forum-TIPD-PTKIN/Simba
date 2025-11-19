@extends('admin.template.master-template')

@section('title', 'Data Surveyor')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Data
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Data Surveyor</span></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <h5 class="mb-3 mb-sm-0">Data Surveyor</h5>
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
                                        <option value="{{ $item->id }}" @selected($loop->first)>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-select form-select-sm" aria-label="Filter status" id="flt_status">
                                    <option value="" selected>-- SEMUA --</option>
                                    @foreach ($status_surveyor as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-auto g-2 gap-1 mb-3" role="group"
                                aria-label="Button Unduh Data Surveyor">
                                <button type="button" class="btn btn-sm btn-success" id="unduhDataSurveyor"><span
                                        class="far fa-file-excel"></span>
                                    Unduh Data</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatable-data-surveyor">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nama Surveyor</th>
                                            <th scope="col">Beasiswa</th>
                                            <th scope="col">No. HP</th>
                                            <th scope="col">Alamat</th>
                                            <th scope="col">Rekening Bank</th>
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


@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@push('script')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        $(function() {
            datatable = $('#datatable-data-surveyor').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.surveyor.show') }}",
                    type: 'GET',
                    data: function(d) {
                        d.tahun = $('#flt_tahun').val();
                        d.beasiswa = $('#flt_beasiswa').val();
                        d.status = $('#flt_status').val();
                    },
                },
                rowCallback: function(row, data, index) {
                    if (data.bersedia === 0) {
                        // Hapus kolom No. HP, Alamat, dan Rekening Bank
                        $('td:eq(3)', row).remove();
                        $('td:eq(3)', row).remove();
                        $('td:eq(3)', row).remove();

                        // Sisipkan satu kolom dengan colspan 3
                        $(`<td colspan="3" class="text-center text-muted"><span class="fw-bold fst-italic">Alasan: </span>${data.alasan}</td>`)
                            .insertAfter($('td:eq(2)', row));
                    }

                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'user.name',
                        name: 'users.name',
                    },
                    {
                        data: 'beasiswa',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'hp',
                    },
                    {
                        data: 'alamat',
                    },
                    {
                        data: 'rekening_bank',
                    },
                    {
                        data: 'status',
                        name: 'bersedia',
                        orderable: false,
                        searchable: false
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
                        "targets": [1, 4, 5],
                        "className": "text-start"
                    },
                    {
                        "targets": 2,
                        "width": "8%"
                    },
                    {
                        "targets": 5,
                        "width": "30%"
                    },
                    {
                        "targets": 6,
                        "width": "5%"
                    },
                ],
                "responsive": true,
                "autoWidth": true,
                "fixedColumns": true,
                "fixedHeader": true,
                "language": {
                    "url": 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json',
                },
            });
        });

        function reloadData() {
            datatable.ajax.reload();
        }
    </script>

    <script>
        $(document).on('click', '#unduhDataSurveyor', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val(),
                status = $('#flt_status').val();

            $.ajax({
                url: "{{ route('admin.surveyor.unduh') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa,
                    status: status
                },
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 2) { // Headers received
                            if (xhr.status === 200) {
                                xhr.responseType = 'blob';
                            } else {
                                xhr.responseType = 'text'; // For error messages
                            }
                        }
                    };
                    return xhr;
                },
                beforeSend: () => {
                    Swal.fire({
                        title: 'Memproses berkas...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });
                },
                success: function(response, status, xhr) {
                    var disposition = xhr.getResponseHeader(
                        'content-disposition');
                    var matches = /"([^""]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] :
                        'Data surveyor.xlsx');

                    var blob = new Blob([response], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });

                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                    link.remove();

                    Swal.close();
                },
                error: function(error) {
                    const msg = JSON.parse(error.responseText);
                    Swal.fire({
                        title: 'Gagal',
                        text: error && error.status !== 200 ?
                            (typeof msg === 'string' ? msg : msg.message) :
                            'Tidak dapat melakukan download file. Terjadi kesalahan atau data tidak tersedia',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        customClass: {
                            timerProgressBar: 'bg-danger'
                        }
                    });
                }
            });
        });
    </script>
@endpush
