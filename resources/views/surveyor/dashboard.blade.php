@extends('surveyor.template.master-template')

@section('title', 'Dashboard')

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
                                <li class="breadcrumb-item" aria-current="page">Home</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Home</h2>
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
                            <h5 class="mb-3 mb-sm-0">Rekapitulasi Responden</h5>
                            <div class="d-flex gap-1 form-filter">
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
                                <button class="btn btn-sm btn-primary btnFilter">Filter</button>
                            </div>
                        </div>

                        <div class="card-body" id="daftar-responden">
                            {!! $view_daftar_responden !!}
                        </div>
                    </div>
                </div>
            </div>

            @if ($cek_rekening)
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Data Rekening Bank</h5>
                            </div>
                            <div class="card-body" id="app">
                                <div class="alert alert-danger" role="alert">
                                    <strong>Perhatian!</strong> Anda belum menentukan data rekening bank, silakan lengkapi
                                    form
                                    di bawah!
                                </div>
                                <form id="formUpdateRekeningBank" enctype="multipart/form-data" method="POST"
                                    action="#">
                                    <div class="form-group">
                                        <label for="no_rekening">Nomor Rekening</label>
                                        <input type="text" class="form-control" id="no_rekening" name="no_rekening"
                                            v-model="no_rekening">
                                    </div>
                                    <div class="form-group">
                                        <label for="nama_rekening">Nama di Rekening</label>
                                        <input type="text" class="form-control" id="nama_rekening" name="nama_rekening"
                                            v-model="nama_rekening">
                                    </div>
                                    <div class="form-group">
                                        <label for="nama_bank">Nama Bank</label>
                                        <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                            v-model="nama_bank">
                                    </div>
                                    <div class="form-group">
                                        <label for="file_rekening">File Buku Rekening <span class="small text-muted">(PDF
                                                maks 1MB)</span></label>
                                        <input type="file" name="file_rekening" id="file_rekening"
                                            @change="handleFileRekening" class="form-control">
                                    </div>

                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('click', '.btnFilter', function() {
            const container = $(this).closest('div.form-filter'),
                tahun = container.find('#flt_tahun').val(),
                beasiswa = container.find('#flt_beasiswa').val();

            let url = "{{ route('surveyor.dashboard.show', ['tahun' => ':tahun', 'beasiswa' => ':beasiswa']) }}"
                .replace(':tahun', tahun)
                .replace(':beasiswa', beasiswa);

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
                    let target;
                    target = $('#daftar-responden');
                    target.children().remove();
                    target.html(res);

                    Swal.close();
                }
            });
        });
    </script>

    <script>
        $.validator.addMethod("maxfilesize", function(value, element, param) {
            if (element.files.length === 0) return true;
            return element.files[0].size <= param;
        }, "Ukuran file terlalu besar.");

        $("#formUpdateRekeningBank").validate({
            ignore: "",
            rules: {
                no_rekening: {
                    required: true,
                    digits: true
                },
                nama_rekening: {
                    required: true
                },
                nama_bank: {
                    required: true
                },
                file_rekening: {
                    required: true,
                    extension: "pdf",
                    maxfilesize: 1 * 1024 * 1024
                }
            },
            messages: {
                no_rekening: {
                    required: "Nomor rekening wajib diisi",
                    digits: "Harus angka"
                },
                nama_rekening: {
                    required: "Nama rekening wajib diisi"
                },
                nama_bank: {
                    required: "Nama bank wajib diisi"
                },
                file_rekening: {
                    required: "File wajib diunggah",
                    extension: "Harus format PDF",
                    maxfilesize: "Ukuran maksimal 1 MB"
                }
            },
            errorPlacement: function(error, element) {
                if (element.attr("type") === "radio") {
                    error.insertAfter(element.closest(".form-check:last")).addClass(
                        "invalid-feedback d-block");
                } else {
                    error.addClass("invalid-feedback");
                    error.insertAfter(element);
                }
            },
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            submitHandler: function(form, e) {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                        <span role="status">Menyimpan...</span>
                    `;

                const formData = new FormData(form);
                formData.append('_method', 'PUT'); // spoof PUT
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('bersedia', 1);
                formData.append('update_rekening', true);
                formData.append('alamat', "{{ $cek_rekening?->alamat }}");
                formData.append('no_wa', "{{ $cek_rekening?->hp }}");

                $.ajax({
                    url: "{{ route('surveyor.persetujuan.update', $cek_rekening?->id ?? 0) }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: (res) => {
                        Swal.fire({
                            icon: res.icon ?? 'success',
                            title: res.title ?? 'Berhasil',
                            text: res.message ?? 'Data berhasil disimpan',
                            timer: 1500,
                            timerProgressBar: true
                        });
                        window.location.reload();
                    },
                    error: (err) => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: err.responseJSON?.message ??
                                'Terjadi kesalahan'
                        });
                    },
                    complete: () => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = 'Simpan';
                    }
                });

                return false;
            }
        });
    </script>
@endpush
