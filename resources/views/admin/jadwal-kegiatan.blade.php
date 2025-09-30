@extends('admin.template.master-template')

@section('title', 'Jadwal Kegiatan')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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
                                <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="btn-group mb-3" role="group" aria-label="Button Modal Jadwal Kegiatan">
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalJadwal"><span class="ti ti-circle-plus"></span>
                                    Tambah
                                    Data</button>
                            </div>
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
            <div class="modal fade" id="modalJadwal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="modalJadwalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form class="needs-validation">
                        @csrf
                        <input class="d-none" type="text" name="jadwal_id" id="jadwal_id">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modalJadwalLabel">Manajemen Data Jadwal</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row mb-3">
                                    <div class="col-12 col-sm-6 mb-3 mb-sm-0">
                                        <label for="tahun" class="form-label">Tahun Kegiatan</label>
                                        <input type="number" class="form-control" id="tahun" name="tahun"
                                            value="{{ $tahun_kegiatan->filter(function ($item) {
                                                    return $item->status === 1;
                                                })->first()?->tahun }}"
                                            readonly required>
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <label for="beasiswa" class="form-label">Beasiswa</label>
                                        <select class="form-select" aria-label="Beasiswa" name="beasiswa" id="beasiswa"
                                            required>
                                            <option value="" selected>-- Pilih salah satu --</option>
                                            @foreach ($beasiswa as $item)
                                                <option value="{{ $item->id }}">{{ $item->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-sm-6 mb-3 mb-sm-0">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select" aria-label="Role Kegiatan" name="role"
                                            id="role" required>
                                            <option value="" selected>-- Pilih salah satu --</option>
                                            @foreach ($role_kegiatan as $item)
                                                <option value="{{ $item }}">{{ str_ireplace('_', ' ', $item) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <label for="nama" class="form-label">Nama Kegiatan</label>
                                        <input type="text" class="form-control" id="nama" name="nama"
                                            autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-12 col-sm-6 mb-3 mb-sm-0">
                                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                        <input type="text" class="form-control datetimepicker" id="tanggal_mulai"
                                            name="tanggal_mulai" autocomplete="off" required>
                                    </div>

                                    <div class="col-12 col-sm-6">
                                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                                        <input type="text" class="form-control datetimepicker" id="tanggal_selesai"
                                            name="tanggal_selesai" autocomplete="off" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label">Deskripsi</label>
                                    <textarea class="form-control" id="deskripsi" name="deskripsi"></textarea>
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
    <script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(function() {
            $('#role').on('change', (e) => {
                // $(this) tidak bisa untuk arrow function, gunakan e.currentTarget
                $('#nama').val($(e.currentTarget).val().replaceAll('_', ' '));
            });

            // Range picker
            $('.datetimepicker').daterangepicker({
                singleDatePicker: true,
                timePicker: true,
                timePicker24Hour: true,
                drops: "auto",
                showDropdowns: true,
                autoUpdateInput: false,
                applyButtonClasses: "btn-success",
                cancelClass: "btn-danger",
                locale: {
                    format: 'D/M/YYYY HH:mm',
                    applyLabel: 'Terapkan',
                    cancelLabel: 'Batal',
                    "daysOfWeek": [
                        "Min",
                        "Sen",
                        "Sel",
                        "Rab",
                        "Kam",
                        "Jum",
                        "Sab"
                    ],
                    "monthNames": [
                        "Januari",
                        "Februari",
                        "Maret",
                        "April",
                        "Mei",
                        "Juni",
                        "Juli",
                        "Agustus",
                        "September",
                        "Oktober",
                        "November",
                        "Desember"
                    ],
                }
            });

            $('.datetimepicker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('D/M/YYYY HH:mm'));
            });

            $('.datetimepicker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        });

        function reloadData() {
            dataTable.ajax.reload(null, false);
        }
    </script>

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
                tahun: {
                    required: true
                },
                beasiswa: {
                    required: true
                },
                role: {
                    required: true
                },
                nama: {
                    required: true
                },
                tanggal_mulai: {
                    required: true
                },
                tanggal_selesai: {
                    required: true
                }
            },
            messages: {
                tahun: {
                    required: 'Tahun kegiatan harus diisi'
                },
                beasiswa: {
                    required: 'Beasiswa harus dipilih'
                },
                role: {
                    required: 'Role kegiatan harus dipilih'
                },
                nama: {
                    required: 'Nama kegiatan harus diisi'
                },
                tanggal_mulai: {
                    required: 'Tanggal mulai harus diisi'
                },
                tanggal_selesai: {
                    required: 'Tanggal selesai harus diisi'
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

                const id = $('#jadwal_id').val();
                let url, type = "POST";
                url = "{{ route('admin.jadwal-kegiatan.store') }}";

                // Serialize form
                const formData = new URLSearchParams(new FormData(form)).toString();

                if (id) {
                    url = "{{ route('admin.jadwal-kegiatan.update', ':id') }}";
                    url = url.replace(':id', id);
                    type = "PUT";
                }

                $.ajax({
                    type: type,
                    url: url,
                    data: formData,
                    success: function(data) {
                        form.reset();
                        $('#modalJadwal').modal('hide');
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
        const dataTable = $("#tableJadwal").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.jadwal-kegiatan.create') }}",
                type: "GET",
                data: (data) => {
                    data._token = "{{ csrf_token() }}";
                    data.flt_tahun = $('#flt_tahun').val();
                    data.flt_beasiswa = $('#flt_beasiswa').val();
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
                    "targets": 5,
                    "width": "15%"
                },
                {
                    "targets": 6,
                    "width": "10%"
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
    </script>

    <script>
        function getData(id) {
            let url = "{{ route('admin.jadwal-kegiatan.edit', ':id') }}";
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
            const response = await getData(id),
                tanggal_mulai = new Date(response.tanggal_mulai),
                tanggal_selesai = new Date(response.tanggal_selesai);

            $('#jadwal_id').val(response.encrypted_id);
            $(`#beasiswa option[value="${response.beasiswa_id}"]`).prop('selected', true);
            $(`#role option[value="${response.role}"]`).prop('selected', true);
            $('#nama').val(response.nama);
            $('#tanggal_mulai').data('daterangepicker').setStartDate(tanggal_mulai);
            $('#tanggal_mulai').data('daterangepicker').setEndDate(tanggal_mulai);
            $('#tanggal_selesai').data('daterangepicker').setStartDate(tanggal_selesai);
            $('#tanggal_selesai').data('daterangepicker').setEndDate(tanggal_selesai);
            $('#tanggal_mulai').val(moment(tanggal_mulai).format('D/M/YYYY HH:mm'));
            $('#tanggal_selesai').val(moment(tanggal_selesai).format('D/M/YYYY HH:mm'));
            if (response.deskripsi) {
                tinymce.activeEditor.setProgressState(true)
                tinymce.activeEditor.setProgressState(false, 1000)
                setTimeout(() => {
                    tinymce.activeEditor.setContent(response.deskripsi);
                    tinymce.triggerSave();
                }, 500);
            }
            $('#modalJadwal').modal('show');
        }

        async function deleteData(id) {
            const response = await getData(id);

            Swal.fire({
                title: 'Apa Anda Yakin?',
                html: `Anda akan menghapus data jadwal : <span class="fw-bold fst-italic">"${response.nama} (${response.beasiswa?.nama} - ${response.tahun_kegiatan?.tahun})"</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    url = "{{ route('admin.jadwal-kegiatan.destroy', ':id') }}";
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
