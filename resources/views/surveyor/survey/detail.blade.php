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

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-circle me-2"></i> <strong>Perhatian! </strong><br>
                Pastikan untuk menyimpan setiap perubahan sebelum keluar dari halaman.
            </div>

            <div class="row">

                <div class="col-md-5 col-lg-4">
                    <section class="card shadow-sm border-0">
                        <header class="card-header ext-center py-3">
                            <h5 class="mb-0">Profil Mahasiswa</h5>
                        </header>

                        <article class="card-body text-center">
                            <!-- Foto Profil -->
                            <figure class="mb-3">
                                <img src="https://be.iainmadura.ac.id/api/v1/external/mahasiswa/foto?nim={{ $pendaftar->pendaftar->mahasiswa->nim }}&key=6321afccabf95b9ec00ac8d193479f4f6a849d46ffbe50fc7e14a74011554fc1"
                                    alt="Foto Mahasiswa Affan One" class="rounded-circle img-thumbnail shadow-sm"
                                    style="width: 120px; height: 120px; object-fit: cover;">
                                <figcaption class="mt-2">
                                    <h5 class="fw-bold mb-0">{{ $pendaftar->pendaftar->mahasiswa->nama }}</h5>
                                    <p class="text-muted mb-1">NIM: {{ $pendaftar->pendaftar->mahasiswa->nim }}</p>
                                </figcaption>
                            </figure>

                            <!-- Info Akademik -->
                            <dl class="row justify-content-center small mb-3">
                                <dt class="col-12 mb-1"><i class="bi bi-mortarboard text-primary me-1"></i>Fakultas / Prodi
                                </dt>
                                <dd class="col-12 mb-2">{{ $pendaftar->pendaftar->mahasiswa->fakultas_prodi }}</dd>
                            </dl>

                            <hr>

                            <section class="text-start small px-2">
                                <table class="table table-borderless align-middle mb-0">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="text-secondary fw-semibold">Alamat</th>
                                            <td>{{ $pendaftar->pendaftar->biodata_pendaftar->data->biodata->alamat_ktp->value }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row" class="text-secondary fw-semibold">No. HP</th>
                                            <td><a
                                                    href="tel:{{ $pendaftar->pendaftar->biodata_pendaftar->data->biodata->no_hp->value }}">{{ $pendaftar->pendaftar->biodata_pendaftar->data->biodata->no_hp->value }}</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </section>

                        </article>
                    </section>
                </div>

                <div class="col-md-7 col-lg-8" id="body-survey">
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
                                    <label for="kesehatanAyah" class="form-label">Kesehatan Ayah <span class="badge"
                                            :class="kesehatanAyahBadgeClass"
                                            v-html="kesehatanAyahSufficiancyText"></span></label>
                                    <div class="d-flex flex-column">
                                        <input type="range" class="form-range" min="1" max="10"
                                            id="kesehatanAyah" step="0.5" v-model.number="survey.ayah.kesehatan">
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
                                    <label for="kondisiIbu" class="form-label">Kondisi Ibu <span class="badge"
                                            :class="kondisiIbuBadgeClass" v-html="kondisiIbuSufficiancyText"></span></label>
                                    <div class="d-flex flex-column">
                                        <input type="range" class="form-range" min="1" max="10"
                                            id="kondisiIbu" step="0.5" v-model.number="survey.ibu.kondisi">
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
                                <div class="mb-5">
                                    <label for="penghasilanAyah" class="form-label">Penghasilan Ayah</label>
                                    <select class="form-select" id="penghasilanAyah"
                                        v-model="survey.pekerjaan.ayah.penghasilan">
                                        <option value="" disabled selected>-- Pilih Penghasilan --</option>
                                        <option v-for="item in master.penghasilan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
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
                                <div class="mb-5">
                                    <label for="penghasilanIbu" class="form-label">Penghasilan Ibu</label>
                                    <select class="form-select" id="penghasilanIbu"
                                        v-model="survey.pekerjaan.ibu.penghasilan">
                                        <option value="" disabled selected>-- Pilih Penghasilan --</option>
                                        <option v-for="item in master.penghasilan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jumlahTanggungan" class="form-label">Jumlah Tanggungan</label>
                                    <input type="number" style="max-width: 170px; width:100%;"
                                        class="form-control text-center" min="1" max="99"
                                        id="jumlahTanggungan" v-model="survey.tanggungan">
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>3. TEMPAT TINGGAL</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="kepemilikanRumah" class="form-label">Status Kepemilikan Rumah</label>
                                    <select class="form-select" id="kepemilikanRumah" v-model="survey.kepemilikanRUmah">
                                        <option value="" disabled selected>-- Pilih Status --</option>
                                        <option v-for="item in master.kepemilikanRumah" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="bangunanRumah" class="form-label">Jenis Bangunan Rumah</label>
                                    <select class="form-select" id="bangunanRumah" v-model="survey.bangunanRumah">
                                        <option value="" disabled selected>-- Pilih Jenis --</option>
                                        <option v-for="item in master.bangunanRumah" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="kepemilikanListrik" class="form-label">Status Kepemilikan Listrik</label>
                                    <select class="form-select" id="kepemilikanListrik"
                                        v-model="survey.kepemilikanListrik">
                                        <option value="" disabled selected>-- Pilih Status --</option>
                                        <option v-for="item in master.kepemilikanListrik" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-5">
                                    <label for="lantaiRumah" class="form-label">Jenis Lantai Rumah</label>
                                    <select class="form-select" id="lantaiRumah" v-model="survey.lantaiRumah">
                                        <option value="" disabled selected>-- Pilih Jenis --</option>
                                        <option v-for="item in master.lantaiRumah" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-title">Kondisi Rumah</div>
                                            <div class="row">
                                                <div class="col-12 col-md-6 col-lg-7 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Tampak Depan</label>
                                                    <img src="{{ $pendaftar->pendaftar->pemberkasan->data->pemberkasan->file_rumah_depan->value->url }}"
                                                        class="rounded w-100" alt="">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-5 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Tampak Samping</label>
                                                    <img src="{{ $pendaftar->pendaftar->pemberkasan->data->pemberkasan->file_rumah_samping->value->url }}"
                                                        class="rounded w-100 mb-3" alt="">
                                                    <div class="checked-kecocokan">
                                                        <div class="radio-widget-mini">
                                                            <form class="radio-mini-form">
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-model="survey.kondisiRumah.rumah"
                                                                        name="kondisiRumahRumah" value="sesuai">
                                                                    <span>Sesuai</span>
                                                                </label>
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-model="survey.kondisiRumah.rumah"
                                                                        name="kondisiRumahRumah" value="tidak">
                                                                    <span>Tidak Sesuai</span>
                                                                </label>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-title">Kondisi Dapur</div>
                                            <div class="row">
                                                <div class="col-12 col-md-6 col-lg-7 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Bukti Dukung</label>
                                                    <img src="{{ $pendaftar->pendaftar->pemberkasan->data->pemberkasan->file_dapur->value->url }}"
                                                        class="rounded w-100" alt="">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-5 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Penilaian</label>
                                                    <div class="checked-kecocokan">
                                                        <div class="radio-widget-mini mb-3">
                                                            <form class="radio-mini-form">
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-model="survey.kondisiRumah.dapur.status"
                                                                        name="kondisiRumahDapur" value="sesuai">
                                                                    <span>Sesuai</span>
                                                                </label>
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-model="survey.kondisiRumah.dapur.status"
                                                                        name="kondisiRumahDapur" value="tidak">
                                                                    <span>Tidak Sesuai</span>
                                                                </label>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <span v-if="survey.kondisiRumah.dapur.status" class="badge"
                                                        :class="kondisiDapurBadgeClass"
                                                        v-html="kondisiDapurSufficiancyText"></span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex flex-column">
                                                    <input type="range" class="form-range" min="1"
                                                        max="10" id="kondisiDapur" step="0.5"
                                                        :disabled="!survey.kondisiRumah.dapur.status"
                                                        v-model.number="survey.kondisiRumah.dapur.kondisi">
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <div class="fw-bold text-danger"><i
                                                                class="fas fa-chevron-left"></i>
                                                            Tidak layak menerima</div>
                                                        <div class="fw-bold text-success">Layak menerima <i
                                                                class="fas fa-chevron-right"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-title">Kondisi Kamar Mandi</div>
                                            <div class="row">
                                                <div class="col-12 col-md-6 col-lg-7 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Bukti Dukung</label>
                                                    <img src="{{ $pendaftar->pendaftar->pemberkasan->data->pemberkasan->file_kamar_mandi->value->url }}"
                                                        class="rounded w-100" alt="">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-5 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Penilaian</label>
                                                    <div class="checked-kecocokan">
                                                        <div class="radio-widget-mini mb-3">
                                                            <form class="radio-mini-form">
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-model="survey.kondisiRumah.kamarMandi.status"
                                                                        name="kondisiKamarMandi" value="sesuai">
                                                                    <span>Sesuai</span>
                                                                </label>
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-model="survey.kondisiRumah.kamarMandi.status"
                                                                        name="kondisiKamarMandi" value="tidak">
                                                                    <span>Tidak Sesuai</span>
                                                                </label>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <span v-if="survey.kondisiRumah.kamarMandi.status" class="badge"
                                                        :class="kondisiKamarMandiBadgeClass"
                                                        v-html="kondisiKamarMandiSufficiancyText"></span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex flex-column">
                                                    <input type="range" class="form-range" min="1"
                                                        max="10" id="kondisiKamarMandi" step="0.5"
                                                        :disabled="!survey.kondisiRumah.kamarMandi.status"
                                                        v-model.number="survey.kondisiRumah.kamarMandi.kondisi">
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <div class="fw-bold text-danger"><i
                                                                class="fas fa-chevron-left"></i>
                                                            Tidak layak menerima</div>
                                                        <div class="fw-bold text-success">Layak menerima <i
                                                                class="fas fa-chevron-right"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="card-title">Kondisi WC</div>
                                            <div class="row">
                                                <div class="col-12 col-md-6 col-lg-7 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Bukti Dukung</label>
                                                    <img src="{{ $pendaftar->pendaftar->pemberkasan->data->pemberkasan->file_wc->value->url }}"
                                                        class="rounded w-100" alt="">
                                                </div>
                                                <div class="col-12 col-md-6 col-lg-5 mb-3 mb-md-0">
                                                    <label class="form-label text-muted small">Penilaian</label>
                                                    <div class="checked-kecocokan">
                                                        <div class="radio-widget-mini mb-3">
                                                            <form class="radio-mini-form">
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-model="survey.kondisiRumah.wc.status"
                                                                        name="kondisiWC" value="sesuai">
                                                                    <span>Sesuai</span>
                                                                </label>
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-model="survey.kondisiRumah.wc.status"
                                                                        name="kondisiWC" value="tidak">
                                                                    <span>Tidak Sesuai</span>
                                                                </label>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <span v-if="survey.kondisiRumah.wc.status" class="badge"
                                                        :class="kondisiWcMandiBadgeClass"
                                                        v-html="kondisiWciSufficiancyText"></span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex flex-column">
                                                    <input type="range" class="form-range" min="1"
                                                        max="10" id="kondisiWC" step="0.5"
                                                        :disabled="!survey.kondisiRumah.wc.status"
                                                        v-model.number="survey.kondisiRumah.wc.kondisi">
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <div class="fw-bold text-danger"><i
                                                                class="fas fa-chevron-left"></i>
                                                            Tidak layak menerima</div>
                                                        <div class="fw-bold text-success">Layak menerima <i
                                                                class="fas fa-chevron-right"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div v-if="surveyErrors">
                                    <div class="fw-bold text-danger">Kesalahan Validasi</div>
                                    <ol class="text-danger">
                                        <li v-for="error in surveyErrors">@{{ error }}</li>
                                    </ol>
                                </div>
                                <div class="d-flex gap-3">
                                    <textarea class="form-control" v-model="survey.catatan" placeholder="Catatan (opsional)" name=""
                                        id=""></textarea>
                                    <button class="btn btn-lg btn-primary" type="submit"
                                        :disabled="surveyErrors !== null">
                                        <i class="fas fa-save me-1"></i> Simpan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection

@push('head')
    <style>
        /* --- Radio Widget Mini (isolated styles) --- */
        .radio-widget-mini {
            font-family: "Inter", sans-serif;
            font-size: 14px;
            color: #1e1e1e;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
        }

        .radio-mini-form {
            display: flex;
            gap: 8px;
        }

        .radio-mini-label {
            color: #8a8a8a;
            display: flex;
            align-items: center;
            cursor: pointer;
            position: relative;
            font-weight: 500;
            border-radius: 20px;
            padding: 4px 10px 4px 6px;
            background-color: #f4f5ff;
            transition: background-color 0.2s ease;
        }

        .radio-mini-label:hover {
            background-color: #e7e9ff;
        }

        .radio-mini-label input {
            position: absolute;
            left: -9999px;
        }

        .radio-mini-label span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* custom circle */
        .radio-mini-label span::before {
            content: "";
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #fff;
            box-shadow: inset 0 0 0 2px #9c9cfb;
            transition: all 0.25s ease;
        }

        /* checked state */
        .radio-mini-label input:checked+span::before {
            box-shadow: inset 0 0 0 6px #f4f5ff;
        }

        .radio-mini-label input:checked+span {
            color: #f4f5ff;
        }

        .radio-mini-label:has(input:checked) {
            background-color: #00005c;
        }
    </style>
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script>
        new Vue({
            el: '#app',
            data: {
                master: {
                    pekerjaan: @json($masterPekerjaan),
                    penghasilan: @json($masterPenghasilan),
                    kepemilikanRumah: @json($masterKepemilikanRumah),
                    bangunanRumah: @json($masterBangunanRumah),
                    lantaiRumah: @json($masterLantaiRumah),
                    kepemilikanListrik: @json($masterKepemilikanListrik),
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
                            pekerjaan: 4,
                            penghasilan: 9
                        },
                        ibu: {
                            pekerjaanLainnya: '',
                            pekerjaan: 8,
                            penghasilan: 9
                        }
                    },
                    tanggungan: 1,
                    kepemilikanRUmah: 1,
                    bangunanRumah: 1,
                    lantaiRumah: 4,
                    kepemilikanListrik: 1,
                    kondisiRumah: {
                        rumah: '',
                        dapur: {
                            status: '',
                            kondisi: 4
                        },
                        kamarMandi: {
                            status: '',
                            kondisi: 4
                        },
                        wc: {
                            status: '',
                            kondisi: 4
                        },
                    },
                    catatan: ''
                }
            },
            computed: {
                surveyErrors() {
                    const s = this.survey;
                    const errors = [];

                    // === AYAH ===
                    if (!s.ayah.nama) errors.push("Nama ayah wajib diisi");
                    if (s.ayah.kesehatan == null || s.ayah.kesehatan === '')
                        errors.push("Kesehatan ayah wajib diisi (1–10)");

                    // === IBU ===
                    if (!s.ibu.nama) errors.push("Nama ibu wajib diisi");
                    if (s.ibu.kondisi == null || s.ibu.kondisi === '')
                        errors.push("Kondisi ibu wajib diisi (1–10)");

                    // === PEKERJAAN AYAH ===
                    if (!s.pekerjaan.ayah.pekerjaan)
                        errors.push("Pekerjaan ayah wajib diisi");

                    // Jika pekerjaannya “LAINNYA”, wajib isi pekerjaanLainnya
                    if (s.pekerjaan.ayah.pekerjaan === 'LAINNYA' && !s.pekerjaan.ayah.pekerjaanLainnya)
                        errors.push("Pekerjaan ayah (lainnya) wajib diisi");

                    if (s.pekerjaan.ayah.penghasilan == null || s.pekerjaan.ayah.penghasilan === '')
                        errors.push("Penghasilan ayah wajib diisi");

                    // === PEKERJAAN IBU ===
                    if (!s.pekerjaan.ibu.pekerjaan)
                        errors.push("Pekerjaan ibu wajib diisi");

                    if (s.pekerjaan.ibu.pekerjaan === 'LAINNYA' && !s.pekerjaan.ibu.pekerjaanLainnya)
                        errors.push("Pekerjaan ibu (lainnya) wajib diisi");

                    if (s.pekerjaan.ibu.penghasilan == null || s.pekerjaan.ibu.penghasilan === '')
                        errors.push("Penghasilan ibu wajib diisi");

                    // === DATA UMUM ===
                    if (!s.tanggungan) errors.push("Jumlah tanggungan wajib diisi");
                    if (!s.kepemilikanRUmah) errors.push("Kepemilikan rumah wajib diisi");
                    if (!s.bangunanRumah) errors.push("Jenis bangunan rumah wajib diisi");
                    if (!s.lantaiRumah) errors.push("Jenis lantai rumah wajib diisi");
                    if (!s.kepemilikanListrik) errors.push("Kepemilikan listrik wajib diisi");

                    // === KONDISI RUMAH ===
                    if (!s.kondisiRumah.rumah) errors.push("Kondisi rumah wajib diisi");

                    // Dapur
                    if (!s.kondisiRumah.dapur.status) errors.push("Status dapur wajib diisi");
                    if (s.kondisiRumah.dapur.kondisi == null || s.kondisiRumah.dapur.kondisi === '')
                        errors.push("Kondisi dapur wajib diisi (1–10)");

                    // Kamar mandi
                    if (!s.kondisiRumah.kamarMandi.status) errors.push("Status kamar mandi wajib diisi");
                    if (s.kondisiRumah.kamarMandi.kondisi == null || s.kondisiRumah.kamarMandi.kondisi === '')
                        errors.push("Kondisi kamar mandi wajib diisi (1–10)");

                    // WC
                    if (!s.kondisiRumah.wc.status) errors.push("Status WC wajib diisi");
                    if (s.kondisiRumah.wc.kondisi == null || s.kondisiRumah.wc.kondisi === '')
                        errors.push("Kondisi WC wajib diisi (1–10)");

                    // === HASIL ===
                    return errors.length === 0 ? null : errors;
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
                },
                kondisiDapurSufficiancyText() {
                    return `Tingkat kelayakan ${this.survey.kondisiRumah.dapur.kondisi}`;
                },
                kondisiDapurBadgeClass() {
                    const value = this.survey.kondisiRumah.dapur.kondisi;
                    if (value <= 2.5) return 'bg-danger';
                    if (value <= 5) return 'bg-warning';
                    if (value <= 7.5) return 'bg-info';
                    return 'bg-success';
                },
                kondisiKamarMandiSufficiancyText() {
                    return `Tingkat kelayakan ${this.survey.kondisiRumah.kamarMandi.kondisi}`;
                },
                kondisiKamarMandiBadgeClass() {
                    const value = this.survey.kondisiRumah.kamarMandi.kondisi;
                    if (value <= 2.5) return 'bg-danger';
                    if (value <= 5) return 'bg-warning';
                    if (value <= 7.5) return 'bg-info';
                    return 'bg-success';
                },
                kondisiWciSufficiancyText() {
                    return `Tingkat kelayakan ${this.survey.kondisiRumah.wc.kondisi}`;
                },
                kondisiWcMandiBadgeClass() {
                    const value = this.survey.kondisiRumah.wc.kondisi;
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

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/viewerjs@1.11.6/dist/viewer.min.css">
@endpush
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/viewerjs@1.11.6/dist/viewer.min.js"></script>
    <script>
        $(document).ready(function() {
            new Viewer(document.getElementById('body-survey'), {
                toolbar: true,
                navbar: true,
                zoomRatio: 0.6
            });
        })
    </script>
@endpush
