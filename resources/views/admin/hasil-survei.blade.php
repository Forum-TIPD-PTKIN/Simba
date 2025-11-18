@extends('admin.template.master-template')

@section('title', 'Hasil Survei')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <style>
        .swal2-container {
            z-index: 2000 !important;
        }

        .select2-container--bootstrap-5 .select2-results__option {
            font-size: 0.87rem !important;
            padding: 0.25rem 0.5rem;
        }

        .select2-container--bootstrap-5 .select2-selection--single {
            font-size: 0.87rem !important;
            padding: 0.25rem 0.5rem;
        }

        .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
            font-size: 0.87rem !important;
        }
    </style>
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
                                <li class="breadcrumb-item" aria-current="page">Hasil Survei</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Hasil Survei</h2>
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
                            <h5 class="mb-3 mb-sm-0">Data Peserta Survei</h5>
                            <div class="d-flex gap-1 align-items-start flex-wrap">
                                <div>
                                    <label for="flt_tahun" class="form-label small">Tahun</label>
                                    <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan"
                                        id="flt_tahun">
                                        @foreach ($tahun_kegiatan as $item)
                                            <option value="{{ $item->id }}" @selected($loop->first)>
                                                {{ $item->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="flt_beasiswa" class="form-label small">Beasiswa</label>
                                    <select class="form-select form-select-sm" aria-label="Filter beasiswa"
                                        id="flt_beasiswa">
                                        @foreach ($beasiswa as $item)
                                            <option value="{{ $item->id }}" @selected($loop->first)>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="flt_surveyor" class="form-label small">Surveyor</label>
                                    <select class="form-select form-select-sm" aria-label="Filter surveyor"
                                        id="flt_surveyor">
                                        <option value="" selected>-- All --</option>
                                        @foreach ($surveyor as $item)
                                            <option value="{{ $item->id }}">
                                                {{ Str::words(strip_tags($item->name), 3, '...') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="align-self-end">
                                    <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-auto g-2 gap-1 mb-3" role="group"
                                aria-label="Button Generate Peserta Tes">
                                <button type="button" class="btn btn-sm btn-success" id="unduhDataPeserta"><span
                                        class="far fa-file-excel"></span>
                                    Unduh Data</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle text-center" id="tablePeserta">
                                    <thead class="bg-cyan-100">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">NIM</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Prodi</th>
                                            <th scope="col">Beasiswa</th>
                                            <th scope="col">Surveyor</th>
                                            <th scope="col">Nilai Akhir</th>
                                            <th scope="col">Aksi</th>
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

            <!-- Modal -->
            <div class="modal fade" id="modalViewNilai" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="modalViewNilaiLabel">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalViewNilaiLabel">Detail Nilai Survei</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function reloadData() {
            dataTable.ajax.reload(null, false);
        }

        $('#flt_surveyor').select2({
            theme: 'bootstrap-5',
        });
    </script>

    <script>
        const dataTable = $("#tablePeserta").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.seleksi-akhir.data-hasil-survei') }}",
                type: "POST",
                data: (data) => {
                    data._token = "{{ csrf_token() }}";
                    data.flt_tahun = $('#flt_tahun').val();
                    data.flt_beasiswa = $('#flt_beasiswa').val();
                    data.flt_surveyor = $('#flt_surveyor').val();
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'mahasiswa.nim',
                    name: 'mahasiswas.nim'
                },
                {
                    data: 'mahasiswa.nama',
                    name: 'mahasiswas.nama'
                },
                {
                    data: 'prodi',
                    name: 'mahasiswas.prodi'
                },
                {
                    data: 'beasiswa',
                    searchable: false,
                    orderable: false
                },
                {
                    data: 'surveyor_detail.surveyor.user.name',
                    name: 'users.name',
                },
                {
                    data: 'nilai_akhir',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'action',
                    name: 'action',
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
                    "targets": 2,
                    "width": "20%",
                    "className": "text-start"
                },
                {
                    "targets": 3,
                    "width": "15%",
                },
                {
                    "targets": 5,
                    "width": "20%",
                    "className": "text-start"
                },
                {
                    "targets": 6,
                    "width": "8%",
                },
            ],
            "order": [
                [1, 'asc']
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

    <script>
        $('#flt_tahun, #flt_beasiswa').on('change', function() {
            $.ajax({
                url: "{{ route('admin.seleksi-akhir') }}",
                type: 'GET',
                data: {
                    flt_tahun: $('#flt_tahun').val(),
                    flt_beasiswa: $('#flt_beasiswa').val()
                },
                beforeSend: () => {
                    Swal.fire({
                        title: 'Mengambil data surveyor...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });
                },
                success: (res) => {
                    $('#flt_surveyor').empty().append($('<option>', {
                        value: '',
                        text: '-- All --'
                    }));
                    res.forEach(opt => {
                        $('#flt_surveyor').append($('<option>', {
                            value: opt.id,
                            text: opt.name.split(" ").slice(0, 3).join(" ") + '...'
                        }));
                    });

                    Swal.close();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON.message ??
                            'Terdapat kesalahan server, periksa lebih lanjut!',
                    });
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '#unduhDataPeserta', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val()
            surveyor = $('#flt_surveyor').val();

            $.ajax({
                url: "{{ route('admin.seleksi-akhir.unduh-hasil-survei') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa,
                    surveyor: surveyor
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
                        'Hasil survei.xlsx');

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

    <script>
        function getData(id) {
            let url = "{{ route('admin.seleksi-akhir.data-hasil-survei.show', ':id') }}";
            url = url.replace(':id', id);

            return $.ajax({
                url: url,
                beforeSend: () => {
                    Swal.fire({
                        title: 'Mengambil data...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });
                },
                complete: () => {
                    Swal.close();
                }
            });
        }

        async function viewData(id) {
            const response = await getData(id);

            $('#modalViewNilai .modal-body').html(response);
            $('#modalViewNilai').modal('show');
        }
    </script>
@endpush
