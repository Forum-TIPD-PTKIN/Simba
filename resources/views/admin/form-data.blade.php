@extends('admin.template.master-template')

@section('title', 'Form Data')

@section('head')
    <style>
        .swal2-container {
            z-index: 2000 !important;
        }
    </style>
@endsection

@section('content')
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content" id="app-vue">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Form Data</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Form Data</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!--[ Main Content ] start-->
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <h5 class="mb-3 mb-sm-0">Data Form Data</h5>
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
                            <div class="form-group mb-3">
                                <div class="d-flex" style="gap:0.4rem">
                                    <button class="btn btn-primary" title="Tambah Form" data-bs-toggle="modal"
                                        data-bs-target="#modalTambahFormData" v-on:click="onReset()"><i
                                            class="ti ti-plus"></i></button>
                                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#copyModal"
                                        v-on:click="onReset()" title="Salin Form"><i class="ti ti-copy"></i></button>
                                    <button class="btn btn-danger" v-on:click="onDeleteMaster()"
                                        v-if="master_jenis.length>1" title="Hapus Form"><i class="ti ti-trash"></i></button>

                                    <select class="form-select" id="jenisform" v-model="form.jenis"
                                        v-on:change="onFilter()">
                                        <option value="FORM_BARU">
                                            Data Form Baru
                                        </option>
                                        <option v-for="item in master_jenis" :value="item">
                                            Filter Form : @{{ item }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle text-center" id="tableFormData">
                                    <thead class="bg-cyan-100">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th>Data/Tipe</th>
                                            <th>Name</th>
                                            <th>Validator</th>
                                            <th>Deskripsi</th>
                                            <th>Opsi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <tr v-for="(item,index) in data">
                                            <td class="text-center">@{{ item.indexed + 1 }}</td>
                                            <td>
                                                @{{ item.judul }}
                                                <div class="text-muted small">
                                                    @{{ item.config.type }}
                                                </div>
                                            </td>
                                            <td>
                                                @{{ item.config.name }}
                                                <div class="small text-muted"
                                                    v-if="item.config.type=='select' || item.config.type=='radio' || item.config.type=='checkbox'">
                                                    @{{ item.config.option.length }} Option
                                                </div>
                                            </td>
                                            <td>
                                                <ul>
                                                    <li v-for="v in formatValidator(item.config.validator)">
                                                        @{{ v.validator }} : @{{ v.message }}</li>
                                                </ul>
                                            </td>
                                            <td>
                                                @{{ item.deskripsi }}
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group mb-3 btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-primary"
                                                        v-on:click="onCopyData(item)" data-bs-toggle="modal"
                                                        data-bs-target="#modalTambahFormData">
                                                        <i class="ti ti-copy"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-info" v-on:click="onEdit(item)"
                                                        data-bs-toggle="modal" data-bs-target="#modalTambahFormData">
                                                        <i class="ti ti-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger"
                                                        v-on:click="onDelete(item)">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--[ Main Content ] end-->

            <!-- Modal Tambah Form Data -->
            <div class="modal fade" id="modalTambahFormData" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" aria-labelledby="modalTambahFormDataLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form class="needs-validation" id="formData">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modalTambahFormDataLabel">Manajemen Form Data</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="col-12 mb-3" v-if="mode === 'NEW_FORM'">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <select class="form-select" id="tahun" name="tahun" v-model="form.tahun"
                                        data-msg="Harus diisi" required>
                                        <option value="" disabled> - pilih - </option>
                                        @foreach ($tahun_kegiatan as $key => $item)
                                            <option value="{{ $item->id }}">{{ $item->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mb-3" v-if="mode === 'NEW_FORM'">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <select class="form-select" id="tahun" name="tahun" v-model="form.tahun"
                                        data-msg="Harus diisi" required>
                                        <option value="" disabled> - pilih - </option>
                                        @foreach ($tahun_kegiatan as $key => $item)
                                            <option value="{{ $item->id }}">{{ $item->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mb-3" v-if="mode === 'NEW_FORM'">
                                    <label for="namajenis" class="form-label">Nama Jenis Form</label>
                                    <input type="text" id="namajenis" class="form-control" name="namajenis"
                                        autocomplete="off" autofocus placeholder="ex. Form Biodata" v-model="namajenis"
                                        data-msg="Harus diisi" required />
                                </div>

                                <div class="col-12 mb-3" v-if="data.length && mode === null">
                                    <label for="insertTo" class="form-label">Sisipkan</label>
                                    <select class="form-select" id="insertTo" v-model="form.indexed"
                                        data-msg="Harus diisi" required>
                                        <option value="0">Paling awal</option>
                                        <option :value="data.length">Paling akhir</option>
                                        <template v-for="(item,index) in data">
                                            <option v-if="form.id!==item.id && index<data.length-1"
                                                :value="(+item.indexed) + 1">Setelah
                                                @{{ item.judul }}</option>
                                        </template>
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="namaform" class="form-label">Title</label>
                                    <input type="text" id="namaform" class="form-control" name="namaform"
                                        autocomplete="off" v-model="form.nama" placeholder="ex. Nama Lengkap"
                                        data-msg="Harus diisi" required />
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="nameform" class="form-label">Name Form</label>
                                    <div class="small text-muted">Definisikan form dengan sebuah
                                        <strong>name</strong>,
                                        namun Anda dapat mengosongkan bagian ini dengan begitu sistem akan
                                        membuatkan
                                        name secara otomatis
                                    </div>
                                    <input type="text" id="nameform" class="form-control" name="name"
                                        v-model="form.name" autocomplete="off" placeholder="ex. nama_lengkap" />
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="typeform" class="form-label">Tipe Form</label>
                                    <select class="form-select" id="typeform" name="typeform" v-model="form.type"
                                        data-msg="Harus diisi" required>
                                        <option value="" disabled> - pilih - </option>
                                        @foreach ($master_type as $key => $item)
                                            <option value="{{ $key }}">{{ $item }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="deksripsiform" class="form-label">Deskripsi Form</label>
                                    <div class="small text-muted">Anda bisa memberikan penjelasan mengenai
                                        form ini, atau dapat dikosongkan</div>
                                    <textarea type="text" id="deksripsiform" class="form-control" name="deskripsi" autocomplete="off"
                                        v-model="form.deskripsi"></textarea>
                                </div>

                                <div class="col-12 mb-3">
                                    <label for="optionform" class="form-label">Option Form</label>
                                    <div class="table-responsive" id="optionform">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Value</th>
                                                    <th>Teks</th>
                                                    <th>Del</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in form.options">
                                                    <td>@{{ index + 1 }}</td>
                                                    <td>
                                                        <input type="text" v-model="item.value" class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="text" v-model="item.text" class="form-control">
                                                    </td>
                                                    <th>
                                                        <button class="btn btn-sm btn-danger" type="button"
                                                            v-on:click="onDeleteOption(index)">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <button class="btn btn-sm btn-primary" type="button"
                                                            v-on:click="onAddOption()">
                                                            <i class="ti ti-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12 mb-3">
                                    <div class="alert alert-info">
                                        <strong>Tips</strong> untuk validator gunakan validator string, atau
                                        silahkan cek <a class="text-warning"
                                            href="https://laravel.com/docs/12.x/validation" target="_blank">disini</a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Validasi</th>
                                                    <th>Pesan Error</th>
                                                    <th>Del</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in form.validators">
                                                    <td>@{{ index + 1 }}</td>
                                                    <td>
                                                        <input type="text" v-model="item.validator"
                                                            class="form-control">
                                                    </td>
                                                    <td>
                                                        <input type="text" v-model="item.message"
                                                            class="form-control">
                                                    </td>
                                                    <th>
                                                        <button class="btn btn-sm btn-danger" type="button"
                                                            v-on:click="onDeleteValidator(index)">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-center">
                                                        <button class="btn btn-sm btn-primary" type="button"
                                                            v-on:click="onAddValidator()">
                                                            <i class="ti ti-plus"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" v-on:click="onSave()">Simpan</button>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.14/vue.min.js"></script>

    <script>
        var app;

        $(document).ready(function() {
            $('#formData').validate({
                // debug: true,
                // invalidHandler: function(event, validator) {
                //     console.log('Form tidak valid');
                //     console.log(validator.errorList);
                // }
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
                }
            });
        })

        function getDetail() {
            const url = "{{ route('admin.form-data.detail') }}";
            return $.ajax({
                type: 'post',
                url: url,
                dataType: "JSON",
                data: {
                    _token: '{{ csrf_token() }}',
                    jenis: app.form.jenis
                },
                complete: (data) => {
                    app.data = data.responseJSON;
                    app.form.indexed = app.data.length
                }
            });
        }
    </script>

    <script>
        app = new Vue({
            el: '#app-vue',
            data: {
                form: {
                    id: '',
                    jenis: '{{ $jenis }}',
                    old_jenis: '{{ $jenis }}',
                    nama: '',
                    name: '',
                    type: 'text',
                    deskripsi: '',
                    indexed: 0,
                    validators: [],
                    options: [],
                },
                data: [],
                master_jenis: @json($master_jenis),
                mode: null,
                namajenis: '',
            },
            methods: {
                onAdd: () => {
                    app.onReset();
                },
                onDeleteMaster: () => {
                    Swal.fire({
                        title: 'Konfirmasi',
                        html: `Anda akan menghapus Master Form : <span class="fw-bold fst-italic text-danger"> ${app.form.jenis}</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let url = "{{ route('admin.form-data.destroy.master') }}";
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    jenis: app.form.jenis
                                },
                                success: function(res) {
                                    const i = app.master_jenis.indexOf(app.form.jenis);
                                    app.master_jenis.splice(i, 1);
                                    app.form.jenis = app.master_jenis[app.master_jenis
                                        .length -
                                        1];
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: res,
                                        icon: 'success'
                                    });
                                    getDetail();
                                },
                                error: function(res) {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: 'Kesalahan pada saat menghapus Form Data',
                                        icon: 'error'
                                    });
                                },
                            });
                        }
                    })
                },
                onDelete: (item) => {
                    Swal.fire({
                        title: 'Konfirmasi',
                        html: `Anda akan menghapus form Form Data : <span class="fw-bold fst-italic text-danger"> ${item.judul}</span>`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            let url =
                                "{{ route('admin.form-data.destroy', ['ID_ITEM_PATCH']) }}";
                            url = url.replaceAll('ID_ITEM_PATCH', item.id);
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    jenis: item.jenis
                                },
                                success: function(res) {
                                    app.data = res;
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: 'Item Form Data Berhasil Dihapus',
                                        icon: 'success'
                                    });
                                },
                                error: function(res) {
                                    Swal.fire({
                                        title: 'Gagal',
                                        text: 'Kesalahan pada saat menghapus Form Data',
                                        icon: 'error'
                                    });
                                },
                            });
                        }
                    })
                },
                onReset: () => {
                    app.form.id = '';
                    app.form.nama = '';
                    app.form.name = '';
                    app.form.type = 'text';
                    app.form.deskripsi = '';
                    app.form.validators = [];
                    app.form.options = [];
                    app.form.indexed = app.data.length;
                    app.mode = null;
                    app.namajenis = ''
                },
                onCopyData: item => {
                    app.form.nama = item.judul;
                    app.form.name = item.config.name;
                    app.form.type = item.config.type;
                    app.form.deskripsi = item.deskripsi;
                    app.form.validators = app.formatValidator(item.config.validator);
                    app.form.options = item.config.option;
                    app.form.indexed = item.indexed;
                },
                onCopy: () => {
                    let url = "{{ route('admin.form-data.copy') }}";

                    $('#formCopy').parsley().validate();

                    if ($('#formCopy').parsley().isValid()) {
                        $('#formCopy')
                            .find('button[type="submit"]')
                            .prop('disabled', true)
                            .html(
                                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
                            );

                        $.ajax({
                            url: url,
                            type: "POST",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                jenis: app.form.jenis,
                                nama: app.form.nama
                            },
                            success: function(res) {
                                app.form.jenis = app.form.nama
                                $('.modal').modal('hide');
                                app.master_jenis.push(app.form.nama);
                                Swal.fire({
                                    title: 'Berhasil',
                                    text: 'Master Form Data Berhasil Disalin',
                                    icon: 'success'
                                });
                                getDetail();
                            },
                            error: function(res) {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: res.responseJSON.message,
                                    icon: 'error'
                                });
                            },
                            complete: function(res) {
                                app.form.nama = '';
                                $('#formCopy')
                                    .find('button[type="submit"]')
                                    .prop('disabled', false)
                                    .html(
                                        `Submit`
                                    );
                            }
                        });
                    }
                },
                onDeleteValidator: (i) => {
                    app.form.validators.splice(i, 1)
                },
                onAddValidator: (i) => {
                    app.form.validators.push({
                        validator: '',
                        message: ''
                    })
                },
                onDeleteOption: (i) => {
                    app.form.options.splice(i, 1)
                },
                onAddOption: (i) => {
                    app.form.options.push({
                        value: '',
                        text: ''
                    })
                },
                formatValidator: validator => {
                    let v = validator
                    return Object.keys(validator).map(n => {
                        return {
                            validator: n,
                            message: v[n]
                        }
                    })
                },
                onEdit: (item) => {
                    app.form.id = item.id;
                    app.form.nama = item.judul;
                    app.form.name = item.config.name;
                    app.form.type = item.config.type;
                    app.form.deskripsi = item.deskripsi;
                    app.form.validators = app.formatValidator(item.config.validator);
                    app.form.options = item.config.option;
                    app.form.indexed = item.indexed;
                },
                onSave: () => {
                    if (app.mode === 'NEW_FORM' && !app.namajenis) {
                        Swal.fire({
                            title: 'Berhasil',
                            text: '?',
                            icon: 'success'
                        });
                        return;
                    }

                    if ($('#formData').valid()) {
                        $('#formData')
                            .find('button[type="submit"]')
                            .prop('disabled', true)
                            .html(
                                `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`
                            );
                        let url_update =
                            "{{ route('admin.form-data.update', ['ID_ITEM_PATCH']) }}";
                        url_update = url_update.replaceAll('ID_ITEM_PATCH', app.form.id);
                        if (app.mode === 'NEW_FORM' && app.form.jenis == '') {
                            app.form.jenis = app.namajenis;
                        }

                        $.ajax({
                            url: app.form.id ? url_update : "{{ route('admin.form-data.store') }}",
                            type: app.form.id ? "PUT" : "POST",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                newform: app.mode === 'NEW_FORM' ? app.namajenis : null,
                                ...app.form
                            },
                            success: function(res) {
                                $('.modal').modal('hide');
                                if (app.mode === 'NEW_FORM') {
                                    app.master_jenis.push(app.namajenis);
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: 'Item Form Data Berhasil Disimpan',
                                        icon: 'success'
                                    });
                                    app.form.jenis = app.namajenis;
                                    getDetail();
                                } else {
                                    app.data = res;
                                    app.form.indexed++;
                                    Swal.fire({
                                        title: 'Berhasil',
                                        text: 'Item Form Data Berhasil Disimpan',
                                        icon: 'success'
                                    });
                                }
                            },
                            error: function(res) {
                                Swal.fire({
                                    title: 'Gagal',
                                    text: res.responseJSON.message,
                                    icon: 'error'
                                });
                            },
                            complete: function(res) {
                                $('#formData')
                                    .find('button[type="submit"]')
                                    .prop('disabled', false)
                                    .html(
                                        `Simpan`
                                    );
                            }
                        });
                    }
                },
                onFilter: () => {
                    if (app.form.jenis === 'FORM_BARU') {
                        app.form.jenis = app.form.old_jenis
                        app.mode = 'NEW_FORM';
                        $('#modalTambahFormData').modal('show');
                    } else {
                        app.form.old_jenis = app.form.jenis
                        app.mode = null;
                        getDetail();
                    }
                }
            }
        })
        getDetail();
    </script>

    <script>
        function reloadData() {
            dataTable.ajax.reload(null, false);
        }
    </script>
@endsection
