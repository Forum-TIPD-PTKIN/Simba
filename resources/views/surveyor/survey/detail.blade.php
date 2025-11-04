@extends('surveyor.template.master-template')

@section('title', 'Detail Survey Kondisi Mahasiswa')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="pc-container" id="app">
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
                    <form @submit.prevent="submitSurvey">
                        <div class="card">
                            <div class="card-header">
                                <h4>1. ORANG TUA / WALI</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="namaAyah" class="form-label">Nama Ayah</label>
                                    <input type="text" class="form-control" id="namaAyah" v-model="survey.ayah.nama">
                                </div>
                                <div class="mb-3">
                                    <label for="kecukupanFinansial" class="form-label">Kesehatan Ayah <span class="badge"
                                            :class="kesehatanAyahBadgeClass"
                                            v-html="kesehatanAyahSufficiancyText"></span></label>
                                    <div class="d-flex flex-column">
                                        <input type="range" class="form-range" min="1" max="10"
                                            id="kecukupanFinansial" step="0.5" v-model.number="survey.ayah.kesehatan">
                                        <div class="d-flex justify-content-between mt-1">
                                            <div class="fw-bold text-danger">
                                                <i class="fas fa-chevron-left"></i> Tidak layak menerima
                                            </div>
                                            <div class="fw-bold text-success">Layak menerima <i
                                                    class="fas fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 mt-5">
                                    <label for="namaIbu" class="form-label">Nama Ibu</label>
                                    <input type="text" class="form-control" id="namaIbu" v-model="survey.ibu.nama">
                                </div>
                                <div class="mb-3">
                                    <label for="kecukupanFinansial" class="form-label">Kondisi Ibu <span class="badge"
                                            :class="kondisiIbuBadgeClass" v-html="kondisiIbuSufficiancyText"></span></label>
                                    <div class="d-flex flex-column">
                                        <input type="range" class="form-range" min="1" max="10"
                                            id="kecukupanFinansial" step="0.5" v-model.number="survey.ibu.kondisi">
                                        <div class="d-flex justify-content-between mt-1">
                                            <div class="fw-bold text-danger"><i class="fas fa-chevron-left"></i>
                                                Tidak layak menerima</div>
                                            <div class="fw-bold text-success">Layak menerima <i
                                                    class="fas fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="card">
                            <div class="card-header">
                                <h4>2. EKONOMI</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="pekerjaanAyah" class="form-label">Pekerjaan Ayah</label>
                                    <select class="form-select" id="pekerjaanAyah"
                                        v-model="survey.pekerjaan.ayah.pekerjaan">
                                        <option value="" disabled selected>-- Pilih Pekerjaan --</option>
                                        <option v-for="item in master.pekerjaan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3" v-if="survey.pekerjaan.ayah.pekerjaan === 'LAINNYA'">
                                    <label for="pekerjaanAyahLainnya" class="form-label">Tulis Pekerjaan Lainnya</label>
                                    <input type="text" class="form-control" id="pekerjaanAyahLainnya"
                                        v-model="survey.pekerjaan.ayah.pekerjaanLainnya">
                                </div>
                                <div class="mb-3">
                                    <label for="penghasilanAyah" class="form-label">Penghasilan Ayah</label>
                                    <select class="form-select" id="penghasilanAyah"
                                        v-model="survey.pekerjaan.ayah.penghasilan">
                                        <option value="" disabled selected>-- Pilih Penghasilan --</option>
                                        <option v-for="item in master.penghasilan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>

                                <div class="mb-3 mt-4">
                                    <label for="pekerjaanIbu" class="form-label">Pekerjaan Ibu</label>
                                    <select class="form-select" id="pekerjaanIbu"
                                        v-model="survey.pekerjaan.ibu.pekerjaan">
                                        <option value="" disabled selected>-- Pilih Pekerjaan --</option>
                                        <option v-for="item in master.pekerjaan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3" v-if="survey.pekerjaan.ibu.pekerjaan === 'LAINNYA'">
                                    <label for="pekerjaanIbuLainnya" class="form-label">Tulis Pekerjaan Lainnya</label>
                                    <input type="text" class="form-control" id="pekerjaanIbuLainnya"
                                        v-model="survey.pekerjaan.ibu.pekerjaanLainnya">
                                </div>
                                <div class="mb-3">
                                    <label for="penghasilanAyah" class="form-label">Penghasilan Ibu</label>
                                    <select class="form-select" id="penghasilanAyah"
                                        v-model="survey.pekerjaan.ayah.penghasilan">
                                        <option value="" disabled selected>-- Pilih Penghasilan --</option>
                                        <option v-for="item in master.penghasilan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>3. TEMPAT TINGGAL</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Sumber Pendanaan Utama (bisa lebih dari satu)</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sumber1"
                                                value="orang_tua" v-model="survey.finansial.sumber">
                                            <label class="form-check-label" for="sumber1">Orang Tua</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sumber2"
                                                value="beasiswa" v-model="survey.finansial.sumber">
                                            <label class="form-check-label" for="sumber2">Beasiswa</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="sumber3"
                                                value="bekerja" v-model="survey.finansial.sumber">
                                            <label class="form-check-label" for="sumber3">Bekerja Paruh Waktu</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="kecukupanFinansial" class="form-label">Tingkat Kecukupan Finansial:
                                        <span class="badge"
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
                                            <label class="form-check-label" for="tinggal2">Kamar Kost /
                                                Kontrakan</label>
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

                                <hr>
                                <h5>Data Survey (Live Preview):</h5>
                                <pre>@{{ survey }}</pre>
                            </div>
                        </div>
                    </form>
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
                master: {
                    pekerjaan: [{
                            value: 1,
                            label: 'PNS/TNI/POLRI'
                        },
                        {
                            value: 2,
                            label: 'Pensiunan'
                        },
                        {
                            value: 3,
                            label: 'Pedagang'
                        },
                        {
                            value: 4,
                            label: 'Petani/Nelayan'
                        },
                        {
                            value: 5,
                            label: 'Wirausaha'
                        },
                        {
                            value: 6,
                            label: 'Buruh Tetap'
                        },
                        {
                            value: 7,
                            label: 'Buruh Tidak Tetap'
                        },
                        {
                            value: 8,
                            label: 'Tidak Bekerja'
                        },
                        {
                            value: 'LAINNYA',
                            label: 'Lainnya'
                        },
                    ],
                    penghasilan: [{
                            value: 10,
                            label: '< Rp 1.000.000'
                        },
                        {
                            value: 9,
                            label: 'Rp 1.000.000 - Rp 2.000.000'
                        },
                        {
                            value: 8,
                            label: 'Rp 2.000.001 - Rp 3.000.000'
                        },
                        {
                            value: 7,
                            label: 'Rp 3.000.001 - Rp 4.000.000'
                        },
                        {
                            value: 6,
                            label: 'Rp 4.000.001 - Rp 5.000.000'
                        },
                        {
                            value: 5,
                            label: 'Rp 5.000.001 - Rp 6.000.000'
                        },
                        {
                            value: 4,
                            label: 'Rp 6.000.001 - Rp 7.000.000'
                        },
                        {
                            value: 3,
                            label: 'Rp 7.000.001 - Rp 8.000.000'
                        },
                        {
                            value: 2,
                            label: 'Rp 8.000.001 - Rp 9.999.999'
                        },
                        {
                            value: 1,
                            label: '>= Rp 10.000.000'
                        },
                    ]
                },
                survey: {
                    ayah: {
                        nama: '',
                        kesehatan: 7
                    },
                    ibu: {
                        nama: '',
                        kondisi: 6
                    },
                    pekerjaan: {
                        ayah: {
                            pekerjaanLainnya: '',
                            pekerjaan: '',
                            penghasilan: 9
                        },
                        ibu: {
                            pekerjaanLainnya: '',
                            pekerjaan: '',
                            penghasilan: 9
                        }
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
                },
                kesehatanAyahSufficiancyText() {
                    return `Tingkat kelayakan ${this.survey.ayah.kesehatan}`;
                },
                kesehatanAyahBadgeClass() {
                    const value = this.survey.ayah.kesehatan;
                    if (value <= 2.5) return 'bg-danger';
                    if (value <= 5) return 'bg-warning';
                    if (value <= 7.5) return 'bg-info';
                    return 'bg-success';
                },
                kondisiIbuSufficiancyText() {
                    return `Tingkat kelayakan ${this.survey.ibu.kondisi}`;
                },
                kondisiIbuBadgeClass() {
                    const value = this.survey.ibu.kondisi;
                    if (value <= 2.5) return 'bg-danger';
                    if (value <= 5) return 'bg-warning';
                    if (value <= 7.5) return 'bg-info';
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
