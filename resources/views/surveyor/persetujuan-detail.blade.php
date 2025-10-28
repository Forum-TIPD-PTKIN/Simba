@extends('surveyor.template.master-template')

@section('title', 'Persetujuan Surveyor')

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
                                <h2 class="mb-0">Persetujian Surveyor</h2>
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
                            <form @submit.prevent="submitForm">
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

                                <div v-if="status === '0'">
                                    <div class="form-group">
                                        <label for="alasan">Alasan (Opsional)</label>
                                        <textarea class="form-control" id="alasan" v-model="alasan" rows="3"></textarea>
                                    </div>
                                </div>

                                <div v-if="status === '1'">
                                    <div class="form-group">
                                        <label for="alamat">Alamat Lengkap</label>
                                        <textarea class="form-control" id="alamat" v-model="alamat" rows="3" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="no_wa">Nomor WhatsApp Aktif</label>
                                        <input type="text" class="form-control" id="no_wa" v-model="no_wa" required>
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
            },
            computed: {
                isFormValid() {
                    if (this.status === '1') {
                        return this.alamat.trim() !== '' && this.no_wa.trim() !== '';
                    }
                    if (this.status === '0') {
                        return true; // Alasan is optional
                    }
                    return false;
                }
            },
            methods: {
                submitForm() {
                    if (!this.isFormValid) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Harap lengkapi semua field yang wajib diisi.',
                        });
                        return;
                    }

                    // confirm
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
                            Swal.showLoading();
                            let formData = {
                                status: this.status,
                                _token: '{{ csrf_token() }}'
                            };

                            if (this.status === '1') {
                                formData.alamat = this.alamat;
                                formData.no_wa = this.no_wa;
                            } else {
                                formData.alasan = this.alasan;
                            }

                            const putUrl =
                                '{{ route('surveyor.persetujuan.update', $detailSurveyor->id) }}';

                            axios.put(putUrl, formData)
                                .then(response => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: 'Data berhasil disimpan.',
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                })
                                .catch(error => {
                                    // Handle error
                                    console.error(error.response.data);
                                    let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                                    if (error.response && error.response.data && error.response.data
                                        .message) {
                                        errorMessage = error.response.data.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: errorMessage,
                                    });
                                });

                        }
                    });
                }
            }
        });
    </script>
@endpush
