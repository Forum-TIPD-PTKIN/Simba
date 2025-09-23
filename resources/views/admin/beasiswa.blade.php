@extends('admin.template.master-template')

@section('title', 'Beasiswa')

@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <style>
        .swal2-container {
            z-index: 2000 !important;
        }
    </style>
@endsection

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
                                <li class="breadcrumb-item" aria-current="page">Beasiswa</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Beasiswa</h2>
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
                            <h5>Data Beasiswa</h5>
                        </div>
                        <div class="card-body">
                            <div class="btn-group mb-3" role="group" aria-label="Button Modal Beasiswa">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalBeasiswa"><span class="ti ti-circle-plus"></span>
                                    Tambah
                                    Data</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle text-center" id="tableBeasiswa">
                                    <thead class="bg-cyan-100">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nama Beasiswa</th>
                                            <th scope="col">Deskripsi</th>
                                            <th scope="col">Status</th>
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
            <div class="modal fade" id="modalBeasiswa" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="modalBeasiswaLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <form class="needs-validation">
                        @csrf
                        <input class="d-none" type="text" name="beasiswa_id" id="beasiswa_id">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modalBeasiswaLabel">Manajemen Data Beasiswa</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama Beasiswa</label>
                                    <input type="text" class="form-control" id="nama" name="nama"
                                        autocomplete="off" autofocus required>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi"></textarea>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check ps-0">
                                        <input type="radio" class="btn-check" name="status" id="status-aktif"
                                            autocomplete="off" value="on" checked>
                                        <label class="btn btn-sm btn-outline-success" for="status-aktif"><span
                                                class="small">Aktif</span></label>

                                        <input type="radio" class="btn-check" name="status" value="off"
                                            id="status-nonaktif" autocomplete="off">
                                        <label class="btn btn-sm btn-outline-danger" for="status-nonaktif"><span
                                                class="small">Tidak Aktif</span></label>
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

@section('script')
    <script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>

    <script>
        // Prevent Bootstrap dialog from blocking focusin
        document.addEventListener('focusin', (e) => {
            if (e.target.closest(".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
                e.stopImmediatePropagation();
            }
        });

        tinymce.init({
            selector: '#deskripsi',
            branding: false,
            menubar: 'edit insert view format table help',
            toolbar: "undo redo |link image | bold italic underline strikethrough | align | bullist numlist",
            toolbar_mode: 'sliding',
            plugins: [
                "advlist", "anchor", "autolink", "charmap", "code", "fullscreen",
                "help", "image", "insertdatetime", "link", "lists",
                "preview", "searchreplace", "table", "visualblocks", "accordion",
                "autoresize", "directionality", "emoticons", "nonbreaking", "pagebreak",
                "visualchars", "wordcount"
            ],
            /* enable title field in the Image dialog*/
            image_title: true,
            /* enable automatic uploads of images represented by blob or data URIs*/
            automatic_uploads: true,
            /*
              URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)
              images_upload_url: 'postAcceptor.php',
              here we add custom filepicker only to Image dialog
            */
            file_picker_types: 'image',
            /* and here's our custom image picker*/
            file_picker_callback: (cb, value, meta) => {
                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');

                input.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    const fileSize = ((file.size / 1024) / 1024).toFixed(4); // MB

                    if (parseFloat(fileSize) > 1) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "bottom-end",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                            didRender: function(x) {
                                $(".swal2-popup.swal2-modal").removeAttr("tabindex");
                            }
                        });
                        Toast.fire({
                            icon: "error",
                            title: "Ukuran file lebih dari 1 MB"
                        });
                        $(".swal2-container").css("z-index", "20000");
                        return false;
                    };

                    const reader = new FileReader();
                    reader.addEventListener('load', () => {
                        /*
                          Note: Now we need to register the blob in TinyMCEs image blob
                          registry. In the next release this part hopefully won't be
                          necessary, as we are looking to handle it internally.
                        */
                        const id = 'blobid' + (new Date()).getTime();
                        const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        const base64 = reader.result.split(',')[1];
                        const blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);

                        /* call the callback and populate the Title field with the file name */
                        cb(blobInfo.blobUri(), {
                            title: file.name
                        });
                    });
                    reader.readAsDataURL(file);
                });

                input.click();
            },
            setup: function(editor) {
                editor.on('blur', function() {
                    tinymce
                        .triggerSave(); // Sinkronkan TinyMCE ke textarea setiap kali editor kehilangan fokus
                });
            }
        });
    </script>

    <script>
        const validForm = $("form.needs-validation").validate({
            ignore: "",
            rules: {
                nama: {
                    required: true
                },
                deskripsi: {
                    required: true
                },
                status: {
                    required: true
                }
            },
            messages: {
                nama: {
                    required: 'Nama beasiswa harus diisi'
                },
                deskripsi: {
                    required: 'Deskripsi beasiswa harus diisi'
                },
                status: {
                    required: 'Status beasiswa harus dipilih'
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
                tinymce.triggerSave();
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');

                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                    <span role="status">Loading...</span>`;

                const id = $('#beasiswa_id').val();
                // Serialize form
                const formData = new URLSearchParams(new FormData(form)).toString();
                let url, type = "POST";
                url = "{{ route('admin.beasiswa.store') }}";

                if (id) {
                    url = "{{ route('admin.beasiswa.update', ':id') }}";
                    url = url.replace(':id', id);
                    type = "PUT";
                }

                $.ajax({
                    type: type,
                    url: url,
                    data: formData,
                    success: function(data) {
                        form.reset();
                        $('#modalBeasiswa').modal('hide');
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
        const dataTable = $("#tableBeasiswa").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.beasiswa.create') }}",
                type: "GET"
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'nama'
                },
                {
                    data: 'deskripsi'
                },
                {
                    data: 'status'
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
                    "width": "20%"
                },
                {
                    "targets": 3,
                    "width": "5%"
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
            let url = "{{ route('admin.beasiswa.edit', ':id') }}";
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

            $('#beasiswa_id').val(response.encrypted_id);
            $('#nama').val(response.nama);
            tinymce.activeEditor.setProgressState(true)
            tinymce.activeEditor.setProgressState(false, 1000)
            setTimeout(() => {
                tinymce.activeEditor.setContent(response.deskripsi);
                tinymce.triggerSave();
            }, 500);
            $(`input[name="status"][value="${response.status === 1 ? 'on' : 'off'}"]`).prop('checked', true);
            $('#modalBeasiswa').modal('show');
        }

        async function deleteData(id) {
            const response = await getData(id);

            Swal.fire({
                title: 'Apa Anda Yakin?',
                html: `Anda akan menghapus data beasiswa : <span class="fw-bold fst-italic">"${response.nama}"</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    url = "{{ route('admin.beasiswa.destroy', ':id') }}";
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
                                        text: res.message
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
@endsection
