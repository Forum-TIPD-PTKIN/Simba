@extends('surveyor.template.master-template')

@section('title', 'Detail Survey Kondisi Mahasiswa')

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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Surveyor</a></li>
                                <li class="breadcrumb-item" aria-current="page">Detail Survey</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Detail Survey Kondisi Mahasiswa</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-md-5 col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Profil Mahasiswa</h5>
                        </div>
                        <div class="card-body"></div>
                    </div>
                </div>
                <div class="col-md-7 col-lg-8">
                    <div class="card" id="app">
                        <div class="card-header">
                            <h5>Form Survey untuk Mahasiswa: [Nama Mahasiswa]</h5>
                        </div>
                        <div class="card-body">
                            <form @submit.prevent="submitSurvey">
                                <!-- Section 1: Academic Condition -->
                                <h4>1. Kondisi Akademik</h4>
                                <hr>
                                <div class="mb-3">
                                    <label for="statusAkademik" class="form-label">Status Akademik</label>
                                    <select class="form-select" id="statusAkademik" v-model="survey.akademik.status">
                                        <option value="">Pilih Status</option>
                                        <option value="aktif">Aktif</option>
                                        <option value="cuti">Cuti</option>
                                        <option value="non-aktif">Non-Aktif</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kehadiran di Kelas</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kehadiran" id="kehadiran1"
                                                value=">80%" v-model="survey.akademik.kehadiran">
                                            <label class="form-check-label" for="kehadiran1"> > 80% </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kehadiran" id="kehadiran2"
                                                value="50-80%" v-model="survey.akademik.kehadiran">
                                            <label class="form-check-label" for="kehadiran2"> 50% - 80% </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="kehadiran" id="kehadiran3"
                                                value="<50%" v-model="survey.akademik.kehadiran">
                                            <label class="form-check-label" for="kehadiran3">
                                                < 50% </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 2: Financial Condition -->
                                <h4 class="mt-4">2. Kondisi Finansial</h4>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">Sumber Pendanaan Utama (bisa lebih dari satu)</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sumber1" value="orang_tua"
                                                v-model="survey.finansial.sumber">
                                            <label class="form-check-label" for="sumber1">Orang Tua</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sumber2" value="beasiswa"
                                                v-model="survey.finansial.sumber">
                                            <label class="form-check-label" for="sumber2">Beasiswa</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sumber3" value="bekerja"
                                                v-model="survey.finansial.sumber">
                                            <label class="form-check-label" for="sumber3">Bekerja Paruh Waktu</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="kecukupanFinansial" class="form-label">Tingkat Kecukupan Finansial: <span
                                            class="badge"
                                            :class="financialBadgeClass">@{{ financialSufficiencyText }}</span></label>
                                    <input type="range" class="form-range" min="0" max="100"
                                        id="kecukupanFinansial" v-model.number="survey.finansial.kecukupan">
                                </div>

                                <!-- Section 3: Living Condition -->
                                <h4 class="mt-4">3. Kondisi Tempat Tinggal</h4>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Tempat Tinggal</label>
                                    <div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tempatTinggal"
                                                id="tinggal1" value="rumah_ortu" v-model="survey.tempatTinggal.jenis">
                                            <label class="form-check-label" for="tinggal1">Rumah Orang Tua</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tempatTinggal"
                                                id="tinggal2" value="kos" v-model="survey.tempatTinggal.jenis">
                                            <label class="form-check-label" for="tinggal2">Kamar Kost / Kontrakan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tempatTinggal"
                                                id="tinggal3" value="apartemen" v-model="survey.tempatTinggal.jenis">
                                            <label class="form-check-label" for="tinggal3">Apartemen</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="tempatTinggal"
                                                id="tinggal4" value="lainnya" v-model="survey.tempatTinggal.jenis">
                                            <label class="form-check-label" for="tinggal4">Lainnya</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Section 4: General Assessment -->
                                <h4 class="mt-4">4. Catatan Tambahan Surveyor</h4>
                                <hr>
                                <div class="mb-3">
                                    <label for="catatan" class="form-label">Catatan atau Observasi Penting
                                        Lainnya</label>
                                    <textarea class="form-control" id="catatan" rows="4" v-model="survey.catatan"></textarea>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    <button type="submit" class="btn btn-primary">Simpan Hasil Survey</button>
                                </div>
                            </form>

                            <hr>
                            <h5>Data Survey (Live Preview):</h5>
                            <pre>@{{ survey }}</pre>
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
    <script>
        new Vue({
            el: '#app',
            data: {
                survey: {
                    akademik: {
                        status: '',
                        kehadiran: ''
                    },
                    finansial: {
                        sumber: [],
                        kecukupan: 50 // default value for slider
                    },
                    tempatTinggal: {
                        jenis: ''
                    },
                    catatan: ''
                }
            },
            computed: {
                financialSufficiencyText() {
                    const value = this.survey.finansial.kecukupan;
                    if (value <= 25) return 'Sangat Kurang';
                    if (value <= 50) return 'Kurang';
                    if (value <= 75) return 'Cukup';
                    return 'Sangat Cukup';
                },
                financialBadgeClass() {
                    const value = this.survey.finansial.kecukupan;
                    if (value <= 25) return 'bg-danger';
                    if (value <= 50) return 'bg-warning';
                    if (value <= 75) return 'bg-info';
                    return 'bg-success';
                }
            },
            methods: {
                submitSurvey() {
                    // In a real application, you would send this.survey data to the server
                    // using an AJAX call (e.g., with axios).
                    console.log('Submitting survey data:', JSON.stringify(this.survey, null, 2));
                    alert('Data survey (pura-pura) berhasil disimpan! Cek console log untuk melihat datanya.');
                }
            }
        });
    </script>
@endpush
