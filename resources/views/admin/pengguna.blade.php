@extends('admin.template.master-template')

@section('title', 'Pengguna')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <style>
        .swal2-container {
            z-index: 2000 !important;
        }

        .error {
            width: 100%;
            margin-top: .25rem;
            font-size: .875em;
            color: #dc3545;
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
                                <li class="breadcrumb-item" aria-current="page">Pengguna</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Pengguna</h2>
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
                        <div class="card-header">
                            <h5>Data Pengguna</h5>
                        </div>
                        <div class="card-body">
                            <div class="btn-group mb-3" role="group" aria-label="Button Modal Pengguna">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalPengguna"><span class="ti ti-circle-plus"></span>
                                    Tambah
                                    Data</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle text-center" id="tablePengguna">
                                    <thead class="bg-cyan-100">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nama Pengguna</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Akses</th>
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
            <div class="modal fade" id="modalPengguna" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="modalPenggunaLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <form class="needs-validation">
                        @csrf
                        <input class="d-none" type="text" name="pengguna_id" id="pengguna_id">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modalPenggunaLabel">Manajemen Data Pengguna</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="pegawai" class="form-label">Pegawai</label>
                                    <select class="form-select select2" aria-label="Pegawai" id="pegawai" name="pegawai"
                                        required>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check ps-0">
                                        @foreach ($access as $item)
                                            <input type="checkbox" class="btn-check" name="akses[]"
                                                id="akses-{{ strtolower($item['access']) }}" autocomplete="off"
                                                value="{{ $item['code'] }}">
                                            <label class="btn btn-sm btn-outline-success"
                                                for="akses-{{ strtolower($item['access']) }}"><span
                                                    class="small">{{ $item['access'] }}</span></label>
                                        @endforeach
                                    </div>
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
    <!-- [ Main Content ] end -->
@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#pegawai').select2({
                dropdownParent: $('#modalPengguna'),
                placeholder: 'Pilih pegawai',
                allowClear: true,
                theme: 'bootstrap-5',
                minimumInputLength: 1,
                ajax: {
                    url: "{{ route('get.data.api') }}", // route di Laravel
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            link: '/api/pegawai',
                            qi: params
                                .term, // kirim search term ke API, karena select2 memakai parameter term=? Kalau di API tidak menerima parameter itu, maka pencarian tidak bisa dilakukan
                            limit: 50 // jumlah maksimum response
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data?.data.map(item => ({
                                id: item.kode,
                                text: item.nama
                            }))
                        };
                    },
                    cache: true
                },
            });
        });
    </script>

    <script>
        const validForm = $("form.needs-validation").validate({
            ignore: "",
            rules: {
                pegawai: {
                    required: true
                },
                "akses[]": {
                    required: true
                }
            },
            messages: {
                pegawai: {
                    required: 'Pegawai belum dipilih'
                },
                "akses[]": {
                    required: 'Pilih minimal 1 akses'
                }
            },
            errorPlacement: function(error, element) {
                // Cek apakah elemen adalah TinyMCE (textarea yang diubah menjadi TinyMCE)
                if (element.siblings().hasClass('tox-tinymce')) {
                    const editorContainer = tinymce.activeEditor.getContainer();
                    // Tempatkan error di bawah editor TinyMCE
                    error.insertAfter(editorContainer).addClass('invalid-feedback');
                } else if (element.hasClass('select2-hidden-accessible')) {
                    // Taruh pesan error DI BAWAH tampilan Select2
                    error.insertAfter(element.next('.select2'));
                } else if (element.attr("type") === "radio" || element.attr("type") === "checkbox") {
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

                const id = $('#pengguna_id').val();
                // Serialize form
                const formData = new URLSearchParams(new FormData(form)).toString();
                let url, type = "POST";
                url = "{{ route('admin.pengguna.store') }}";

                if (id) {
                    url = "{{ route('admin.pengguna.update', ':id') }}";
                    url = url.replace(':id', id);
                    type = "PUT";
                }

                $.ajax({
                    type: type,
                    url: url,
                    data: formData,
                    success: function(data) {
                        form.reset();
                        $('#modalPengguna').modal('hide');
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
                    error: function(data) {
                        const msg = JSON.parse(JSON.stringify(data));
                        Swal.fire({
                            icon: 'error',
                            title: "Gagal",
                            text: msg.responseJSON.message
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
        const dataTable = $("#tablePengguna").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.pengguna.create') }}",
                type: "GET"
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'access'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            "columnDefs": [{
                    "targets": 0,
                    "width": "5%"
                },
                {
                    "targets": 1,
                    "width": "15%"
                },
                {
                    "targets": 2,
                    "width": "15%"
                },
                {
                    "targets": 3,
                    "width": "10%"
                },
                {
                    "targets": 4,
                    "width": "10%"
                },
                {
                    "targets": "_all",
                    "className": "dt-head-center dt-body-center cell-border",
                    "visible": true
                },
            ],
            "order": [1, 'asc'],
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
        function getData(id) {
            let url = "{{ route('admin.pengguna.edit', ':id') }}";
            url = url.replace(':id', id);

            return $.ajax({
                url: url,
                dataType: "JSON",
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

        async function editData(id) {
            const response = await getData(id);

            $('#pengguna_id').val(response?.id);
            // Buat option baru jika belum ada
            let option = new Option(response?.name, response?.username, true, true);
            $('#pegawai').append(option).trigger('change'); // set value
            // Uncheck semua dulu
            $('input[name="akses[]"]').prop('checked', false);
            response?.access.forEach(item => $(`input[name="akses[]"][value="${item}"]`)
                .prop('checked', true));
            $('#modalPengguna').modal('show');
        }

        async function deleteData(id) {
            const response = await getData(id);

            Swal.fire({
                title: 'Apa Anda Yakin?',
                html: `Anda akan menghapus data pengguna : <span class="fw-bold fst-italic">"${response.name}"</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    url = "{{ route('admin.pengguna.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    Swal.fire({
                        title: 'Sedang memproses...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function(res) {
                                    dataTable.ajax.reload(null, false);

                                    Swal.fire({
                                        title: res.title,
                                        text: res.message,
                                        icon: res.icon,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                },
                                error: function(res) {
                                    Swal.fire({
                                        title: 'Gagal',
                                        icon: 'error',
                                        text: res.responseJSON.message ??
                                            'Ada kesalahan'
                                    });
                                },
                                complete: () => {
                                    $("form.needs-validation").trigger('reset');
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                }
            });
        }
    </script>
@endpush
