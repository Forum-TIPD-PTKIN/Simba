@extends('verifikator.template.master-template')

@section('title', 'Seleksi Administrasi')

@push('head')
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
                                <li class="breadcrumb-item" aria-current="page">Seleksi Administrasi</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Seleksi Administrasi</h2>
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
                            <div
                                class="alert {{ $jadwal_kegiatan ? 'alert-warning' : 'alert-danger' }} container-alert-jadwal">
                                <h5><i class="ti ti-calendar-event"></i> Jadwal Kegiatan</h5>
                                @if ($jadwal_kegiatan)
                                    Seleksi administrasi untuk beasiswa {{ $beasiswa[0]->nama }} tahun
                                    {{ $tahun_kegiatan[0]->tahun }} dimulai dari
                                    {{ $jadwal_kegiatan?->tanggal_mulai }} s.d. {{ $jadwal_kegiatan?->tanggal_selesai }}
                                @else
                                    Jadwal kegiatan belum dibuat oleh Administrator
                                @endif
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
            <div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-labelledby="modalVerifikasiLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form id="formVerifikasi" class="needs-validation">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modalVerifikasiLabel">Verifikasi Pendaftaran</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
    <script src="{{ asset('assets/admin/plugins/tinymce/tinymce.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        function reloadData() {
            $.ajax({
                url: "{{ route('verifikator.seleksi-administrasi.jadwal') }}",
                data: {
                    tahun: $('#flt_tahun').val(),
                    beasiswa: $('#flt_beasiswa').val()
                },
                success: (res) => {
                    const alert_jadwal = $('.container-alert-jadwal'),
                        text = res && Object.keys(res).length > 0 ?
                        `Seleksi administrasi untuk beasiswa ${res.beasiswa?.nama} tahun ${res.tahun_kegiatan?.tahun} dimulai dari ${res.tanggal_mulai} s.d. ${res.tanggal_selesai}` :
                        `Jadwal kegiatan belum dibuat oleh Administrator`;

                    alert_jadwal
                        .removeClass(function(index, className) {
                            return (className.match(/(^|\s)alert-\S+/g) || []).join(' ');
                        })
                        .addClass((res && Object.keys(res).length > 0) ? 'alert-warning' : 'alert-danger')
                        .html('')
                        .append(`
                        <h5><i class="ti ti-calendar-event"></i> Jadwal Kegiatan</h5>
                        ${text}
                    `);
                }
            });

            dataTable.ajax.reload(null, false);
        }

        const dataTable = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('verifikator.seleksi-administrasi.data') }}",
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
                    data: 'mahasiswa.nim'
                },
                {
                    data: 'mahasiswa.nama'
                },
                {
                    data: 'mahasiswa.fakultas_prodi'
                },
                {
                    data: 'beasiswa'
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
            responsive: true,
            autoWidth: true,
            info: true,
            fixedColumns: true,
            fixedHeader: true,
            ordering: false,
            searching: false,
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
        function verifikasiData(id) {
            let url = "{{ route('verifikator.seleksi-administrasi.edit', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
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
                success: (res) => {
                    $('#modalVerifikasi .modal-body').html(res);

                    $('#modalVerifikasi').modal('show');
                    Swal.close();

                    const oldEditor = tinymce.get('catatan');
                    if (oldEditor && typeof oldEditor.remove === 'function') {
                        oldEditor.remove();
                    }

                    // Prevent Bootstrap dialog from blocking focusin
                    document.addEventListener('focusin', (e) => {
                        if (e.target.closest(
                                ".tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
                            e.stopImmediatePropagation();
                        }
                    });


                    tinymce.init({
                        selector: '#catatan',
                        branding: false,
                        menubar: 'edit insert view format help',
                        toolbar: "undo redo |link | bold italic underline strikethrough | align | bullist numlist",
                        toolbar_mode: 'sliding',
                        plugins: [
                            "advlist", "anchor", "autolink", "charmap", "code", "fullscreen",
                            "help", "link", "lists", "preview", "searchreplace", "visualblocks",
                            "autoresize", "directionality", "emoticons", "visualchars", "wordcount"
                        ],
                        setup: function(editor) {
                            editor.on('blur', function() {
                                tinymce
                                    .triggerSave(); // Sinkronkan TinyMCE ke textarea setiap kali editor kehilangan fokus
                            });
                        }
                    });
                }
            });
        }

        function viewControl(e) {
            let container = document.getElementById('berkas-control');
            let links = container.querySelectorAll('.base-berkas');
            let urls = []
            links.forEach(link => {
                let url = link.getAttribute('data-url');
                let type = link.getAttribute('data-type');
                let extension = link.getAttribute('data-extension');
                urls.push({
                    url,
                    type,
                    extension
                })
            });
            const data = {
                active: {
                    url: e.getAttribute('data-url'),
                    type: e.getAttribute('data-type'),
                    extension: e.getAttribute('data-extension')
                },
                data: urls.sort((a, b) => a.type.localeCompare(b.type))
            }
            $.ajax({
                type: 'post',
                url: "{{ route('view.control') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    data: data
                },
                dataType: 'HTML',
                success: function(data) {
                    const winUrl = URL.createObjectURL(
                        new Blob([data], {
                            type: "text/html"
                        })
                    );

                    const margin = 100; // Jarak tepi agar tidak full full banget
                    const width = window.screen.availWidth - margin * 8;
                    const height = window.screen.availHeight - margin * 2;
                    const left = (window.screen.availWidth - width) / 2;
                    const top = (window.screen.availHeight - height) / 2;

                    const win = window.open(
                        winUrl,
                        "win",
                        `width=${width},height=${height},top=${top},left=${left}`
                    );
                }
            });
        }
    </script>

    <script>
        const validForm = $("form.needs-validation").validate({
            ignore: "",
            rules: {
                status_verval: {
                    required: true
                }
            },
            messages: {
                status_verval: {
                    required: 'Status verifikasi dan validasi harus dipilih'
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

                // Serialize form
                const formData = new URLSearchParams(new FormData(form)).toString();

                $.ajax({
                    type: "POST",
                    url: "{{ route('verifikator.seleksi-administrasi.store') }}",
                    data: formData,
                    success: function(data) {
                        form.reset();
                        $('#modalVerifikasi').modal('hide');
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
@endpush
