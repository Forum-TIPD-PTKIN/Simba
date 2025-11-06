@extends('surveyor.template.master-template')

@section('title', 'Persetujuan Surveyor')

@push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
                                <li class="breadcrumb-item" aria-current="page">Persetujuan</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Persetujuan Surveyor</h2>
                                <div class="fs-4">Kegiatan Beasiswa {{ $titleKegiatan }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Form Persetujuan</h5>
                        </div>
                        <div class="card-body" id="app">
                            <div class="alert alert-info" role="alert">
                                <strong>Perhatian!</strong> Pilihan yang Anda buat bersifat final dan tidak dapat diubah
                                kembali.
                            </div>
                            <form @submit.prevent="submitForm" id="formPersetujuanSurveyor" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label class="form-label">Apakah Anda bersedia menjadi surveyor?</label>
                                    <div class="form-check">
                                        <input class="form-check-input" name="bersedia" type="radio" id="bersedia"
                                            value="1" v-model="status">
                                        <label class="form-check-label" for="bersedia">
                                            Bersedia
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" name="bersedia" type="radio" id="tidak_bersedia"
                                            value="0" v-model="status">
                                        <label class="form-check-label" for="tidak_bersedia">
                                            Tidak Bersedia
                                        </label>
                                    </div>
                                </div>

                                <div v-show="status === '0'">
                                    <div class="form-group">
                                        <label for="alasan">Alasan (Opsional)</label>
                                        <textarea class="form-control" id="alasan" name="alasan" v-model="alasan" rows="3"></textarea>
                                    </div>
                                </div>

                                <div v-show="status === '1'">
                                    <div class="form-group">
                                        <label for="alamat">Alamat Saat Ini <span class="small text-muted">(Jalan, Dusun,
                                                Desa/Kelurahan, Kecamatan, Kabupaten/Kota)</span></label>
                                        <textarea class="form-control" id="alamat" name="alamat" v-model="alamat" rows="3"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_wa">Nomor WhatsApp Aktif</label>
                                        <input type="text" class="form-control" id="no_wa" name="no_wa"
                                            v-model="no_wa">
                                    </div>
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
                                </div>

                                <button type="submit" class="btn btn-primary" :disabled="!isFormValid">Simpan</button>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        new Vue({
            el: '#app',
            data: {
                status: null,
                alasan: '',
                alamat: '',
                no_wa: '',
                no_rekening: '',
                nama_rekening: '',
                nama_bank: '',
                file_rekening: null,
                isFormValid: false
            },
            methods: {
                handleFileRekening(e) {
                    this.file_rekening = e.target.files[0];
                    this.checkFormValidity();
                },
                initValidator() {
                    const vm = this;

                    $.validator.addMethod("maxfilesize", function(value, element, param) {
                        if (element.files.length === 0) return true;
                        return element.files[0].size <= param;
                    }, "Ukuran file terlalu besar.");

                    $("#formPersetujuanSurveyor").validate({
                        ignore: "",
                        rules: {
                            bersedia: {
                                required: true
                            },
                            alamat: {
                                required: function(element) {
                                    return vm.status === '1';
                                }
                            },
                            no_wa: {
                                required: function(element) {
                                    return vm.status === '1';
                                },
                                digits: true,
                                minlength: 10,
                                maxlength: 12
                            },
                            no_rekening: {
                                required: function(element) {
                                    return vm.status === '1';
                                },
                                digits: true
                            },
                            nama_rekening: {
                                required: function(element) {
                                    return vm.status === '1';
                                }
                            },
                            nama_bank: {
                                required: function(element) {
                                    return vm.status === '1';
                                }
                            },
                            file_rekening: {
                                required: function(element) {
                                    return vm.status === '1';
                                },
                                extension: "pdf",
                                maxfilesize: 1 * 1024 * 1024
                            }
                        },
                        messages: {
                            bersedia: {
                                required: "Harus pilih salah satu"
                            },
                            alamat: {
                                required: "Alamat wajib diisi"
                            },
                            no_wa: {
                                required: "Nomor WhatsApp wajib diisi",
                                digits: "Harus angka",
                                minlength: "Minimal 10 digit",
                                maxlength: "Maksimal 12 digit"
                            },
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
                        onkeyup: function() {
                            vm.checkFormValidity();
                        },
                        onfocusout: function() {
                            vm.checkFormValidity();
                        },
                        onclick: function() {
                            vm.checkFormValidity();
                        },
                        submitHandler: function(form, e) {
                            e.preventDefault();
                            vm.submitAjax(form);
                        }
                    });

                    // Recheck saat ada input berubah
                    $("#formPersetujuanSurveyor").on("change keyup", "input, textarea", function() {
                        vm.checkFormValidity();
                    });
                },

                checkFormValidity() {
                    if (this.status === '1') {
                        this.isFormValid = $("#formPersetujuanSurveyor").valid();
                    } else if (this.status === '0') {
                        this.isFormValid = $("#formPersetujuanSurveyor input[name='bersedia']:checked").length > 0;
                    } else {
                        this.isFormValid = false;
                    }
                },

                submitForm() {
                    this.checkFormValidity();
                    if (this.isFormValid) {
                        $("#formPersetujuanSurveyor").submit();
                    }
                },

                submitAjax(form) {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = `
                        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                        <span role="status">Menyimpan...</span>
                    `;

                    const formData = new FormData(form);
                    formData.append('_method', 'PUT'); // spoof PUT
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin dengan pilihan ini? Keputusan ini tidak dapat diubah kembali.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, lanjut",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('surveyor.persetujuan.update', $detailSurveyor->id) }}",
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
                                    // form.reset();
                                    // this.isFormValid = false;
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
                        }
                    });

                    // Swal.fire({
                    //     title: "Konfirmasi",
                    //     text: "Apakah Anda yakin dengan pilihan ini? Keputusan ini tidak dapat diubah kembali.",
                    //     icon: "warning",
                    //     showCancelButton: true,
                    //     confirmButtonColor: "#3085d6",
                    //     cancelButtonColor: "#d33",
                    //     confirmButtonText: "Ya, lanjut",
                    // }).then((result) => {
                    //     if (result.isConfirmed) {
                    //         Swal.showLoading();
                    //         let formData = {
                    //             status: this.status,
                    //             _token: '{{ csrf_token() }}'
                    //         };

                    //         if (this.status === '1') {
                    //             formData.alamat = this.alamat;
                    //             formData.no_wa = this.no_wa;
                    //         } else {
                    //             formData.alasan = this.alasan;
                    //         }

                    //         const putUrl =
                    //             '{{ route('surveyor.persetujuan.update', $detailSurveyor->id) }}';

                    //         axios.put(putUrl, formData)
                    //             .then(response => {
                    //                 Swal.fire({
                    //                     icon: 'success',
                    //                     title: 'Berhasil!',
                    //                     text: 'Data berhasil disimpan.',
                    //                 }).then(() => {
                    //                     window.location.reload();
                    //                 });
                    //             })
                    //             .catch(error => {
                    //                 // Handle error
                    //                 console.error(error.response.data);
                    //                 let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                    //                 if (error.response && error.response.data && error.response.data
                    //                     .message) {
                    //                     errorMessage = error.response.data.message;
                    //                 }
                    //                 Swal.fire({
                    //                     icon: 'error',
                    //                     title: 'Gagal!',
                    //                     text: errorMessage,
                    //                 });
                    //             });

                    //     }
                    // });
                }
            },
            mounted() {
                this.$nextTick(() => {
                    this.initValidator();
                });
            },
            watch: {
                status() {
                    this.$nextTick(() => {
                        $("#formPersetujuanSurveyor").validate().resetForm();
                        $(".is-invalid, .is-valid").removeClass("is-invalid is-valid");
                        this.checkFormValidity();
                    });
                }
            }
        });
    </script>
@endpush
