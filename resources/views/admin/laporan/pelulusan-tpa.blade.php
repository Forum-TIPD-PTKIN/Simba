@extends('admin.template.master-template')

@section('title', 'Pelulusan TPA')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <style>
        .error {
            width: 100%;
            margin-top: .25rem;
            font-size: .875em;
            color: #dc3545;
        }

        .swal2-container {
            z-index: 2000 !important;
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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Pelulusan TPA</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Pelulusan TPA</h2>
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
                            <h5 class="mb-3 mb-sm-0">Data Peserta TPA</h5>
                            <div class="d-flex gap-1">
                                <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan"
                                    id="flt_tahun">
                                    @foreach ($tahun_kegiatan as $item)
                                        <option value="{{ $item->id }}" @selected($loop->first)>{{ $item->tahun }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-select form-select-sm" aria-label="Filter beasiswa" id="flt_beasiswa">
                                    @foreach ($beasiswa as $item)
                                        <option value="{{ $item->id }}" @selected($loop->first)>{{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-select form-select-sm" aria-label="Filter status" id="flt_status">
                                    <option value="" selected>-- Semua --</option>
                                    @foreach ($status as $item)
                                        <option value="{{ $item }}">{{ $item }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="btn-group mb-3" role="group" aria-label="Button Unduh Pelulusan TPA">
                                <button type="button" class="btn btn-sm btn-dark" data-bs-toggle="modal"
                                    data-bs-target="#modalImporPelulusanTPA"><span class="ti ti-file-import"></span>
                                    Impor Pelulusan Peserta TPA</button>
                            </div>
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
                                            <th scope="col">Aksi</th>
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

            <!-- Modal -->
            <div class="modal fade" id="modalImporPelulusanTPA" tabindex="-1" aria-labelledby="modalImporPelulusanTPALabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form class="needs-validation" enctype="multipart/form-data" id="formImporPelulusanTPA">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modalImporPelulusanTPALabel">Impor Pelulusan Peserta TPA
                                </h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="tahun_kegiatan" class="form-label">Tahun Kegiatan</label>
                                    <select class="form-select" aria-label="Tahun kegiatan" name="tahun_kegiatan"
                                        id="tahun_kegiatan" required>
                                        @foreach ($tahun_kegiatan as $item)
                                            <option value="{{ $item->id }}" @selected($loop->first)>
                                                {{ $item->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="beasiswa" class="form-label">Beasiswa</label>
                                    <select class="form-select" aria-label="Beasiswa" name="beasiswa" id="beasiswa"
                                        required>
                                        @foreach ($beasiswa as $item)
                                            <option value="{{ $item->id }}" @selected($loop->first)>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small id="file_imporHelp" class="form-text text-muted">Unduh file template <a
                                            class="fw-bold fst-italic" href="#"
                                            id="fileTemplateImporPelulusanTPA">disini!</a></small>
                                </div>
                                <div class="mb-3">
                                    <label for="file_impor" class="form-label">File Impor</label>
                                    <input type="file" name="file_impor" id="file_impor" class="form-control"
                                        required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </form>
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
        // Reload data
        function reloadData() {
            dataTable.ajax.reload(null, false);
        }

        // Datatable
        const dataTable = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.seleksi-tpa.pelulusan.data') }}",
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
                {
                    data: 'action',
                    nama: 'action',
                    orderable: false,
                    searchable: false
                }
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

    <script>
        $.validator.addMethod("maxfilesize", function(value, element, param) {
            if (element.files.length === 0) return true; // skip if no file
            return element.files[0].size <= param;
        }, "Ukuran file terlalu besar.");

        const validForm = $("form.needs-validation").validate({
            ignore: "",
            rules: {
                tahun_kegiatan: {
                    required: true
                },
                beasiswa: {
                    required: true
                },
                file_impor: {
                    required: true,
                    extension: "xls|xlsx",
                    maxfilesize: 2 * 1024 * 1024 // 2MB
                }
            },
            messages: {
                tahun_kegiatan: {
                    required: 'Tahun kegiatan belum dipilih'
                },
                beasiswa: {
                    required: 'Beasiswa belum dipilih'
                },
                file_impor: {
                    required: 'File impor belum dipilih',
                    extension: 'Ekstensi file harus .xls atau .xlsx',
                    maxfilesize: 'Ukuran file tidak boleh lebih dari 2MB'
                }
            },
            errorPlacement: function(error, element) {
                // Cek apakah elemen adalah TinyMCE (textarea yang diubah menjadi TinyMCE)
                if (element.siblings().hasClass('tox-tinymce')) {
                    const editorContainer = tinymce.activeEditor.getContainer();
                    // Tempatkan error di bawah editor TinyMCE
                    error.insertAfter(editorContainer).addClass('invalid-feedback');
                } else if (element.attr("type") === "radio") {
                    // Untuk radio buttons, tempatkan error setelah .form-check
                    error.insertAfter(element.closest('.form-check'));
                } else {
                    // Untuk input biasa, tempatkan error setelah input
                    error.addClass('invalid-feedback');
                    error.insertAfter(element);
                }
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            submitHandler: function(form, e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');

                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span role="status">Loading...</span>`;

                // Serialize form
                const formData = new FormData($('#formImporPelulusanTPA')[0]);
                formData.append('_token', "{{ csrf_token() }}");

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.seleksi-tpa.pelulusan.impor') }}",
                    data: formData,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        form.reset();
                        $('#modalImporPelulusanTPA').modal('hide');
                        dataTable.ajax.reload(null, false);

                        const msg = JSON.parse(JSON.stringify(data));
                        Swal.fire({
                            icon: msg.icon,
                            title: msg.title,
                            text: msg.message,
                            timer: 1500,
                            timerProgressBar: true,
                            customClass: {
                                timerProgressBar: 'bg-success bg-opacity-50'
                            }
                        });
                    },
                    error: function(error) {
                        const msg = JSON.parse(JSON.stringify(error));
                        Swal.fire({
                            icon: 'error',
                            title: "Gagal",
                            text: error && error.status !== 200 ?
                                (typeof msg === 'string' ? msg : msg.message) :
                                'Gagal impor pelulusan peserta TPA',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            customClass: {
                                timerProgressBar: 'bg-danger'
                            }
                        });
                    },
                    complete: function() {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Simpan';
                    }
                });
                return false;
            }
        });
    </script>

    <script>
        // Fungsi ubah status
        function ubahStatusKelulusanTpa(status_id, status) {
            let url = "{{ route('admin.seleksi-tpa.pelulusan.update') }}";

            Swal.fire({
                title: 'Anda Yakin?',
                html: `Anda akan mengubah status kelulusan TPA menjadi <strong>${status}</strong>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "PUT",
                        data: {
                            status_id: status_id,
                            status: status,
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: () => {
                            Swal.fire({
                                title: 'Memproses data...',
                                showCancelButton: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                                allowOutsideClick: false
                            });
                        },
                        success: (res) => {
                            reloadData();
                            Swal.fire({
                                icon: res.icon,
                                title: res.title,
                                text: res.message,
                                timer: 1500,
                                timerProgressBar: true
                            });
                        }
                    });
                }
            });
        }

        // Lolos
        function lolosData(id) {
            const status_id = id;
            const status = 'Lolos';

            ubahStatusKelulusanTpa(status_id, status);
        }

        // Gagal
        function gagalData(id) {
            const status_id = id;
            const status = 'Gagal';

            ubahStatusKelulusanTpa(status_id, status);
        }
    </script>

    <script>
        $(document).on('click', '#fileTemplateImporPelulusanTPA', function() {
            const tahun = $('#tahun_kegiatan').val(),
                beasiswa = $('#beasiswa').val();

            $.ajax({
                url: "{{ route('admin.seleksi-tpa.pelulusan.template') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa
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
                    var matches = /filename="?([^";]+)"?/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] :
                        'template_impor_pelulusan_peserta_tpa.xlsx');

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
