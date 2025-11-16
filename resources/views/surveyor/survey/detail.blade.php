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
                                <li class="breadcrumb-item"><a href="{{ route('surveyor.survey') }}">Survei</a></li>
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
                Pastikan status sudah tesimpan (Ter-update).
            </div>


            <div class="card">
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-center ">
                        <div style="white-space: nowrap;">Saat ini :</div>
                        <select onchange="reloadPage(this)" class="form-select" aria-label="Default select example">
                            <option value="" selected disabled> - Pilih Pendaftar - </option>
                            @foreach ($masterPendaftar as $item)
                                <option value="{{ $item->id }}" @selected($item->id == $id)>
                                    {{ $item->pendaftar->mahasiswa->nama }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('surveyor.hasil-survei', ['id' => $pendaftar->pendaftar->id]) }}"
                            class="btn btn-sm btn-dark"><i class="far fa-eye"></i> Pratinjau</a>
                    </div>
                </div>
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
                                <img src="{{ $pendaftar->pendaftar->mahasiswa->foto }}" alt="Foto Mahasiswa Affan One"
                                    class="rounded-circle img-thumbnail shadow-sm"
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
                                            <th scope="row" class="text-secondary fw-semibold">Kategori</th>
                                            <td>{{ $pendaftar->pendaftar->pemberkasan->data->pemberkasan->kategori->valOption }}
                                            </td>
                                        </tr>
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
                                    <label for="namaAyah" class="form-label fw-bold mb-0">Nama Ayah</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elAyahNama">
                                                @if ($nilaiSurvey->ayahNamaUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->ayahNamaUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.nama_ayah.value }}</div>
                                        </div>
                                    </div>
                                    <input type="text"
                                        v-on:input="liveChage('ayahNama', survey.ayah.nama, '#elAyahNama', 'tidak')"
                                        class="form-control" id="namaAyah" v-model="survey.ayah.nama">
                                    <div class="radio-mini-form mt-2">
                                        <label class="radio-mini-label">
                                            <input type="radio" v-on:change="changeStatusSesuai('ayahNamaStatus')"
                                                v-model="survey.ayah.status" name="namaAyah" value="sesuai">
                                            <span>Sesuai</span>
                                        </label>
                                        <label class="radio-mini-label">
                                            <input type="radio" v-on:change="changeStatusSesuai('ayahNamaStatus')"
                                                v-model="survey.ayah.status" name="namaAyah" value="tidak">
                                            <span>Tidak Sesuai</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-3">
                                    <label for="kesehatanAyah" class="form-label fw-bold mb-0">Kesehatan Ayah</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elAyahKesehatan">
                                                @if ($nilaiSurvey->ayahKesehatanUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->ayahKesehatanUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Point kelayakan menerima:
                                            <span class="badge"
                                                :class="kesehatanAyahBadgeClass">@{{ kesehatanAyahSufficiancyText }}</span>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.kondisi_ayah.valOption }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <input type="range"
                                            v-on:change="change('ayahKesehatan', survey.ayah.kesehatan, '#elAyahKesehatan')"
                                            class="form-range" min="1" max="10" id="kesehatanAyah"
                                            step="0.5" v-model.number="survey.ayah.kesehatan">
                                        <div class="d-flex justify-content-between mt-1">
                                            <div class="fw-bold text-danger">
                                                <i class="fas fa-chevron-left"></i> Point 1
                                            </div>
                                            <div class="fw-bold text-success">Point 10 <i
                                                    class="fas fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-3 mt-5">
                                    <label for="namaIbu" class="form-label mb-0 fw-bold">Nama Ibu</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elIbuNama">
                                                @if ($nilaiSurvey->ibuNamaUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->ibuNamaUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.nama_ibu.value }}</div>
                                        </div>
                                    </div>
                                    <input type="text"
                                        v-on:input="liveChage('ibuNama', survey.ibu.nama, '#elIbuNama', 'tidak')"
                                        class="form-control" id="namaIbu" v-model="survey.ibu.nama">
                                    <div class="radio-mini-form mt-2">
                                        <label class="radio-mini-label">
                                            <input type="radio" v-on:change="changeStatusSesuai('ibuNamaStatus')"
                                                v-model="survey.ibu.status" name="namaIbu" value="sesuai">
                                            <span>Sesuai</span>
                                        </label>
                                        <label class="radio-mini-label">
                                            <input type="radio" v-on:change="changeStatusSesuai('ibuNamaStatus')"
                                                v-model="survey.ibu.status" name="namaIbu" value="tidak">
                                            <span>Tidak Sesuai</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-3">
                                    <label for="kondisiIbu" class="form-label fw-bold mb-0">Kondisi Ibu</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elIbuKondisi">
                                                @if ($nilaiSurvey->ibuKondisiUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->ibuKondisiUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Point kelayakan menerima:
                                            <span class="badge"
                                                :class="kondisiIbuBadgeClass">@{{ kondisiIbuSufficiancyText }}</span>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.kondisi_ibu.valOption }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <input type="range"
                                            v-on:change="change('ibuKondisi', survey.ibu.kondisi, '#elIbuKondisi')"
                                            class="form-range" min="1" max="10" id="kondisiIbu"
                                            step="0.5" v-model.number="survey.ibu.kondisi">
                                        <div class="d-flex justify-content-between mt-1">
                                            <div class="fw-bold text-danger"><i class="fas fa-chevron-left"></i>
                                                Point 1</div>
                                            <div class="fw-bold text-success">Point 10 <i
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
                                    <label for="pekerjaanAyah" class="form-label fw-bold mb-0">Pekerjaan Ayah</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elAyahPekerjaan">
                                                @if ($nilaiSurvey->ayahPekerjaanUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->ayahPekerjaanUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.pekerjaan_ayah.valOption }}</div>
                                        </div>
                                    </div>
                                    <select class="form-select" id="pekerjaanAyah"
                                        v-on:change="change('ayahPekerjaan', survey.pekerjaan.ayah.pekerjaan, '#elAyahPekerjaan')"
                                        v-model="survey.pekerjaan.ayah.pekerjaan">
                                        <option value="" disabled selected>-- Pilih Pekerjaan --</option>
                                        <option v-for="item in master.pekerjaan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3" v-if="survey.pekerjaan.ayah.pekerjaan === 'LAINNYA'">
                                    <div class="alert alert-warning">
                                        <strong>Penting!</strong> Kategori “Pekerjaan lainnya” mencakup pekerjaan dengan
                                        tingkat di bawah
                                        "Buruh Tidak Tetap" dan di atas "Tidak Bekerja".
                                    </div>
                                    <label for="pekerjaanAyahLainnya" class="form-label">Tulis Pekerjaan Lainnya</label>
                                    <input
                                        v-on:input="liveChage('ayahPekerjaanLainnya', survey.pekerjaan.ayah.pekerjaanLainnya, '#elAyahPekerjaan', 'tidak')"
                                        type="text" class="form-control" id="pekerjaanAyahLainnya"
                                        v-model="survey.pekerjaan.ayah.pekerjaanLainnya">
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-3">
                                    <label for="penghasilanAyah" class="form-label mb-0 fw-bold">Penghasilan Ayah</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elAyahPenghasilan">
                                                @if ($nilaiSurvey->ayahPenghasilanUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->ayahPenghasilanUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.penghasilan_ayah.valOption }}</div>
                                        </div>
                                    </div>
                                    <select class="form-select" id="penghasilanAyah"
                                        v-on:change="change('ayahPenghasilan', survey.pekerjaan.ayah.penghasilan, '#elAyahPenghasilan')"
                                        v-model="survey.pekerjaan.ayah.penghasilan">
                                        <option value="" disabled selected>-- Pilih Penghasilan --</option>
                                        <option v-for="item in master.penghasilan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-3">
                                    <label for="pekerjaanIbu" class="form-label mb-0 fw-bold">Pekerjaan Ibu</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elIbuPekerjaan">
                                                @if ($nilaiSurvey->ibuPekerjaanUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->ibuPekerjaanUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.pekerjaan_ibu.valOption }}</div>
                                        </div>
                                    </div>
                                    <select class="form-select" id="pekerjaanIbu"
                                        v-on:change="change('ibuPekerjaan', survey.pekerjaan.ibu.pekerjaan, '#elIbuPekerjaan')"
                                        v-model="survey.pekerjaan.ibu.pekerjaan">
                                        <option value="" disabled selected>-- Pilih Pekerjaan --</option>
                                        <option v-for="item in master.pekerjaan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="mb-3" v-if="survey.pekerjaan.ibu.pekerjaan === 'LAINNYA'">
                                    <div class="alert alert-warning">
                                        <strong>Penting!</strong> Kategori “Pekerjaan lainnya” mencakup pekerjaan dengan
                                        tingkat di bawah
                                        "Buruh Tidak Tetap" dan di atas "Tidak Bekerja".
                                    </div>
                                    <label for="pekerjaanIbuLainnya" class="form-label">Tulis Pekerjaan Lainnya</label>
                                    <input type="text"
                                        v-on:input="liveChage('ibuPekerjaanLainnya', survey.pekerjaan.ibu.pekerjaanLainnya, '#elIbuPekerjaan', 'tidak')"
                                        class="form-control" id="pekerjaanIbuLainnya"
                                        v-model="survey.pekerjaan.ibu.pekerjaanLainnya">
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-5">
                                    <label for="penghasilanIbu" class="form-label mb-0 fw-bold">Penghasilan Ibu</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elIbuPenghasilan">
                                                @if ($nilaiSurvey->ibuPenghasilanUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->ibuPenghasilanUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.penghasilan_ibu.valOption }}</div>
                                        </div>
                                    </div>
                                    <select class="form-select" id="penghasilanIbu"
                                        v-model="survey.pekerjaan.ibu.penghasilan"
                                        v-on:change="change('ibuPenghasilan', survey.pekerjaan.ibu.penghasilan, '#elIbuPenghasilan')">
                                        <option value="" disabled selected>-- Pilih Penghasilan --</option>
                                        <option v-for="item in master.penghasilan" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-3">
                                    <label for="jumlahTanggunganKeluarga" class="form-label mb-0 fw-bold">Jumlah
                                        Tanggungan Keluarga</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elTanggunganKeluarga">
                                                @if ($nilaiSurvey->tanggunganKeluargaUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->tanggunganKeluargaUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.tanggungan_keluarga.valOption }}</div>
                                        </div>
                                    </div>
                                    <input type="number" style="max-width: 170px; width:100%;"
                                        v-on:input="liveChage('tanggunganKeluarga', survey.tanggunganKeluarga, '#elTanggunganKeluarga', 'tidak')"
                                        class="form-control text-center" min="1" max="99"
                                        id="jumlahTanggunganKeluarga" v-model="survey.tanggunganKeluarga">
                                    <div class="radio-mini-form mt-2">
                                        <label class="radio-mini-label">
                                            <input type="radio"
                                                v-on:change="changeStatusSesuai('tanggunganKeluargaStatus')"
                                                v-model="survey.tanggunganKeluargaStatus" name="tanggunganKeluarga"
                                                value="sesuai">
                                            <span>Sesuai</span>
                                        </label>
                                        <label class="radio-mini-label">
                                            <input type="radio"
                                                v-on:change="changeStatusSesuai('tanggunganKeluargaStatus')"
                                                v-model="survey.tanggunganKeluargaStatus" name="tanggunganKeluarga"
                                                value="tidak">
                                            <span>Tidak Sesuai</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4>3. TEMPAT TINGGAL</h4>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="kepemilikanRumah" class="form-label mb-0 fw-bold">Status Kepemilikan
                                        Rumah</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elKepemilikanRumah">
                                                @if ($nilaiSurvey->kepemilikanRumahUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->kepemilikanRumahUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.kepemilikan_rumah.valOption }}</div>
                                        </div>
                                    </div>
                                    <select class="form-select"
                                        v-on:change="change('kepemilikanRumah', survey.kepemilikanRumah, '#elKepemilikanRumah')"
                                        id="kepemilikanRumah" v-model="survey.kepemilikanRumah">
                                        <option value="" disabled selected>-- Pilih Status --</option>
                                        <option v-for="item in master.kepemilikanRumah" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                    <div class="radio-mini-form mt-2">
                                        <label class="radio-mini-label">
                                            <input type="radio"
                                                v-on:change="changeStatusSesuai('kepemilikanRumahStatus')"
                                                v-model="survey.kepemilikanRumahStatus" name="kepemilikanRumah"
                                                value="sesuai">
                                            <span>Sesuai</span>
                                        </label>
                                        <label class="radio-mini-label">
                                            <input type="radio"
                                                v-on:change="changeStatusSesuai('kepemilikanRumahStatus')"
                                                v-model="survey.kepemilikanRumahStatus" name="kepemilikanRumah"
                                                value="tidak">
                                            <span>Tidak Sesuai</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-3">
                                    <label for="bangunanRumah" class="form-label fw-bold mb-0">Jenis Bangunan
                                        Rumah</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elBangunanRumah">
                                                @if ($nilaiSurvey->bangunanRumahUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->bangunanRumahUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.bangunan_rumah.valOption }}</div>
                                        </div>
                                    </div>
                                    <select class="form-select"
                                        v-on:change="change('bangunanRumah', survey.bangunanRumah, '#elBangunanRumah')"
                                        id="bangunanRumah" v-model="survey.bangunanRumah">
                                        <option value="" disabled selected>-- Pilih Jenis --</option>
                                        <option v-for="item in master.bangunanRumah" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                    <div class="radio-mini-form mt-2">
                                        <label class="radio-mini-label">
                                            <input type="radio" v-on:change="changeStatusSesuai('bangunanRumahStatus')"
                                                v-model="survey.bangunanRumahStatus" name="bangunanRumah" value="sesuai">
                                            <span>Sesuai</span>
                                        </label>
                                        <label class="radio-mini-label">
                                            <input type="radio" v-on:change="changeStatusSesuai('bangunanRumahStatus')"
                                                v-model="survey.bangunanRumahStatus" name="bangunanRumah" value="tidak">
                                            <span>Tidak Sesuai</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-3">
                                    <label for="kepemilikanListrik" class="form-label fw-bold mb-0">Status Kepemilikan
                                        Listrik</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elKepemilikanListrik">
                                                @if ($nilaiSurvey->kepemilikanListrikUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->kepemilikanListrikUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.kepemilikan_listrik.valOption }}</div>
                                        </div>
                                    </div>
                                    <select class="form-select" id="kepemilikanListrik"
                                        v-on:change="change('kepemilikanListrik', survey.kepemilikanListrik, '#elKepemilikanListrik')"
                                        v-model="survey.kepemilikanListrik">
                                        <option value="" disabled selected>-- Pilih Status --</option>
                                        <option v-for="item in master.kepemilikanListrik" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                    <div class="radio-mini-form mt-2">
                                        <label class="radio-mini-label">
                                            <input type="radio"
                                                v-on:change="changeStatusSesuai('kepemilikanListrikStatus')"
                                                v-model="survey.kepemilikanListrikStatus" name="kepemilikanListrik"
                                                value="sesuai">
                                            <span>Sesuai</span>
                                        </label>
                                        <label class="radio-mini-label">
                                            <input type="radio"
                                                v-on:change="changeStatusSesuai('kepemilikanListrikStatus')"
                                                v-model="survey.kepemilikanListrikStatus" name="kepemilikanListrik"
                                                value="tidak">
                                            <span>Tidak Sesuai</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="my-5">
                                    <hr>
                                </div>
                                <div class="mb-5">
                                    <label for="lantaiRumah" class="form-label fw-bold mb-0">Jenis Lantai Rumah</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elLantaiRumah">
                                                @if ($nilaiSurvey->lantaiRumahUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->lantaiRumahUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum dinilai</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Isian Mahasiswa: <div class="fst-italic">@{{ isianMahasiswa.lantai_rumah.valOption }}</div>
                                        </div>
                                    </div>
                                    <select class="form-select"
                                        v-on:change="change('lantaiRumah', survey.lantaiRumah, '#elLantaiRumah')"
                                        id="lantaiRumah" v-model="survey.lantaiRumah">
                                        <option value="" disabled selected>-- Pilih Jenis --</option>
                                        <option v-for="item in master.lantaiRumah" :value="item.value">
                                            @{{ item.label }}
                                        </option>
                                    </select>
                                    <div class="radio-mini-form mt-2">
                                        <label class="radio-mini-label">
                                            <input type="radio" v-on:change="changeStatusSesuai('lantaiRumahStatus')"
                                                v-model="survey.lantaiRumahStatus" name="lantaiRumah" value="sesuai">
                                            <span>Sesuai</span>
                                        </label>
                                        <label class="radio-mini-label">
                                            <input type="radio" v-on:change="changeStatusSesuai('lantaiRumahStatus')"
                                                v-model="survey.lantaiRumahStatus" name="lantaiRumah" value="tidak">
                                            <span>Tidak Sesuai</span>
                                        </label>
                                    </div>
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
                                                    <div class="d-flex flex-column mb-2">
                                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                                            Status: <div id="elKondisiRumah">
                                                                @if ($nilaiSurvey->kondisiRumahUpdateAt)
                                                                    <span class="fw-bold text-success"><i
                                                                            class="fas fa-check-circle"></i>
                                                                        update
                                                                        {{ formatDateUpdateAt($nilaiSurvey->kondisiRumahUpdateAt) }}</span>
                                                                @else
                                                                    <span class="text-warning">Belum dinilai</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="checked-kecocokan">
                                                        <div class="radio-widget-mini">
                                                            <div class="radio-mini-form">
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-on:change="change('kondisiRumah', 'sesuai', '#elKondisiRumah','sesuai')"
                                                                        v-model="survey.kondisiRumah.rumah"
                                                                        name="kondisiRumahRumah" value="sesuai">
                                                                    <span>Sesuai</span>
                                                                </label>
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-on:change="change('kondisiRumah', 'tidak', '#elKondisiRumah','tidak')"
                                                                        v-model="survey.kondisiRumah.rumah"
                                                                        name="kondisiRumahRumah" value="tidak">
                                                                    <span>Tidak Sesuai</span>
                                                                </label>
                                                            </div>
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
                                                    <div class="d-flex flex-column mb-2">
                                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                                            Status: <div id="elKondisiDapur">
                                                                @if ($nilaiSurvey->kondisiDapurUpdateAt)
                                                                    <span class="fw-bold text-success"><i
                                                                            class="fas fa-check-circle"></i>
                                                                        update
                                                                        {{ formatDateUpdateAt($nilaiSurvey->kondisiDapurUpdateAt) }}</span>
                                                                @else
                                                                    <span class="text-warning">Belum dinilai</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                                            Point kelayakan menerima:
                                                            <span class="badge"
                                                                :class="kondisiDapurBadgeClass">@{{ kondisiDapurSufficiancyText }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="checked-kecocokan">
                                                        <div class="radio-widget-mini mb-3">
                                                            <div class="radio-mini-form">
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-on:change="change('kondisiDapur', survey.kondisiRumah.dapur.kondisi, '#elKondisiDapur', 'sesuai')"
                                                                        v-model="survey.kondisiRumah.dapur.status"
                                                                        name="kondisiRumahDapur" value="sesuai">
                                                                    <span>Sesuai</span>
                                                                </label>
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-on:change="change('kondisiDapur', survey.kondisiRumah.dapur.kondisi, '#elKondisiDapur', 'tidak')"
                                                                        v-model="survey.kondisiRumah.dapur.status"
                                                                        name="kondisiRumahDapur" value="tidak">
                                                                    <span>Tidak Sesuai</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex flex-column">
                                                    <input type="range" class="form-range" min="1"
                                                        v-on:change="change('kondisiDapur', survey.kondisiRumah.dapur.kondisi, '#elKondisiDapur', survey.kondisiRumah.dapur.status)"
                                                        max="10" id="kondisiDapur" step="0.5"
                                                        :disabled="!survey.kondisiRumah.dapur.status"
                                                        v-model.number="survey.kondisiRumah.dapur.kondisi">
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <div class="fw-bold text-danger"><i
                                                                class="fas fa-chevron-left"></i>
                                                            Point 1</div>
                                                        <div class="fw-bold text-success">Point 10 <i
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
                                                    <div class="d-flex flex-column mb-2">
                                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                                            Status: <div id="elKondisiKamarMandi">
                                                                @if ($nilaiSurvey->kondisiKamarMandiUpdateAt)
                                                                    <span class="fw-bold text-success"><i
                                                                            class="fas fa-check-circle"></i>
                                                                        update
                                                                        {{ formatDateUpdateAt($nilaiSurvey->kondisiKamarMandiUpdateAt) }}</span>
                                                                @else
                                                                    <span class="text-warning">Belum dinilai</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                                            Point kelayakan menerima:
                                                            <span class="badge"
                                                                :class="kondisiKamarMandiBadgeClass">@{{ kondisiKamarMandiSufficiancyText }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="checked-kecocokan">
                                                        <div class="radio-widget-mini mb-3">
                                                            <div class="radio-mini-form">
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-on:change="change('kondisiKamarMandi', survey.kondisiRumah.kamarMandi.kondisi, '#elKondisiKamarMandi', 'sesuai')"
                                                                        v-model="survey.kondisiRumah.kamarMandi.status"
                                                                        name="kondisiKamarMandi" value="sesuai">
                                                                    <span>Sesuai</span>
                                                                </label>
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-on:change="change('kondisiKamarMandi', survey.kondisiRumah.kamarMandi.kondisi, '#elKondisiKamarMandi', 'tidak')"
                                                                        v-model="survey.kondisiRumah.kamarMandi.status"
                                                                        name="kondisiKamarMandi" value="tidak">
                                                                    <span>Tidak Sesuai</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex flex-column">
                                                    <input type="range" class="form-range" min="1"
                                                        max="10" id="kondisiKamarMandi" step="0.5"
                                                        v-on:change="change('kondisiKamarMandi', survey.kondisiRumah.kamarMandi.kondisi, '#elKondisiKamarMandi', survey.kondisiRumah.kamarMandi.status)"
                                                        :disabled="!survey.kondisiRumah.kamarMandi.status"
                                                        v-model.number="survey.kondisiRumah.kamarMandi.kondisi">
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <div class="fw-bold text-danger"><i
                                                                class="fas fa-chevron-left"></i>
                                                            Point 1</div>
                                                        <div class="fw-bold text-success">Point 10 <i
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
                                                    <div class="d-flex flex-column mb-2">
                                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                                            Status: <div id="elKondisiWc">
                                                                @if ($nilaiSurvey->kondisiWcUpdateAt)
                                                                    <span class="fw-bold text-success"><i
                                                                            class="fas fa-check-circle"></i>
                                                                        update
                                                                        {{ formatDateUpdateAt($nilaiSurvey->kondisiWcUpdateAt) }}</span>
                                                                @else
                                                                    <span class="text-warning">Belum dinilai</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                                            Point kelayakan menerima:
                                                            <span class="badge"
                                                                :class="kondisiWcMandiBadgeClass">@{{ kondisiWciSufficiancyText }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="checked-kecocokan">
                                                        <div class="radio-widget-mini mb-3">
                                                            <div class="radio-mini-form">
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-on:change="change('kondisiWc', survey.kondisiRumah.wc.kondisi, '#elKondisiWc', 'sesuai')"
                                                                        v-model="survey.kondisiRumah.wc.status"
                                                                        name="kondisiWC" value="sesuai">
                                                                    <span>Sesuai</span>
                                                                </label>
                                                                <label class="radio-mini-label">
                                                                    <input type="radio"
                                                                        v-on:change="change('kondisiWc', survey.kondisiRumah.wc.kondisi, '#elKondisiWc', 'tidak')"
                                                                        v-model="survey.kondisiRumah.wc.status"
                                                                        name="kondisiWC" value="tidak">
                                                                    <span>Tidak Sesuai</span>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex flex-column">
                                                    <input type="range" class="form-range" min="1"
                                                        max="10" id="kondisiWC" step="0.5"
                                                        v-on:change="change('kondisiWc', survey.kondisiRumah.wc.kondisi, '#elKondisiWc', survey.kondisiRumah.wc.status)"
                                                        :disabled="!survey.kondisiRumah.wc.status"
                                                        v-model.number="survey.kondisiRumah.wc.kondisi">
                                                    <div class="d-flex justify-content-between mt-1">
                                                        <div class="fw-bold text-danger"><i
                                                                class="fas fa-chevron-left"></i>
                                                            Point 1</div>
                                                        <div class="fw-bold text-success">Point 10 <i
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
                                <div>
                                    <label for="berkasGdrive" class="form-label fw-bold mb-0">Url Google Drive</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elberkasGdrive">
                                                @if ($nilaiSurvey->berkasGdriveUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->berkasGdriveUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum diperbarui</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <input type="text"
                                        v-on:input="liveChage('berkasGdrive', survey.berkasGdrive, '#elberkasGdrive')"
                                        class="form-control" id="berkasGdrive" v-model="survey.berkasGdrive">
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                {{-- <div v-if="surveyErrors">
                                    <div class="fw-bold text-danger">Kesalahan Validasi</div>
                                    <ol class="text-danger">
                                        <li v-for="error in surveyErrors">@{{ error }}</li>
                                    </ol>
                                </div> --}}
                                <div xclass="d-flex gap-3">
                                    <label for="catatan" class="form-label fw-bold mb-0">Catatan (Opsional)</label>
                                    <div class="d-flex flex-column">
                                        <div class="d-flex gap-1 flex-wrap item-align-center">
                                            Status: <div id="elCatatan">
                                                @if ($nilaiSurvey->catatanUpdateAt)
                                                    <span class="fw-bold text-success"><i class="fas fa-check-circle"></i>
                                                        update
                                                        {{ formatDateUpdateAt($nilaiSurvey->catatanUpdateAt) }}</span>
                                                @else
                                                    <span class="text-warning">Belum diperbarui</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <textarea v-on:input="liveChage('catatan', survey.catatan, '#elCatatan')" class="form-control"
                                        v-model="survey.catatan" placeholder="Ketikkan sesuatu..." id="catatan"></textarea>
                                    {{-- <button class="btn btn-lg btn-primary" type="submit"
                                        :disabled="surveyErrors !== null">
                                        <i class="fas fa-save me-1"></i> Simpan
                                    </button> --}}
                                </div>
                            </div>
                        </div>

                        <div class="card bg-danger text-bg-danger">
                            <div class="card-body text-center ">
                                Untuk mengosongkan seluruh penilaian, silakan klik RESET. Tindakan ini bersifat permanen dan
                                tidak dapat dibatalkan.
                                <div class="mt-3">
                                    <button @click="resetSurvey" type="button" class="btn btn-lg btn-light">
                                        <i class="fas fa-redo-alt me-1"></i> RESET</button>
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
            white-space: nowrap;
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
        let timeout = {};
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
                isianMahasiswa: @json($pendaftar->pendaftar->biodata_pendaftar->data->biodata),
                survey: {
                    ayah: {
                        nama: '{{ $nilaiSurvey->ayahNama }}',
                        kesehatan: {!! $nilaiSurvey->ayahKesehatan !!},
                        status: '{{ $nilaiSurvey->ayahNamaStatus }}'
                    },
                    ibu: {
                        nama: '{{ $nilaiSurvey->ibuNama }}',
                        kondisi: {!! $nilaiSurvey->ibuKondisi !!},
                        status: '{{ $nilaiSurvey->ibuNamaStatus }}'
                    },
                    pekerjaan: {
                        ayah: {
                            pekerjaanLainnya: '{{ $nilaiSurvey->ayahPekerjaanLainnya }}',
                            pekerjaan: {!! $nilaiSurvey->ayahPekerjaan == 'LAINNYA'
                                ? "'" . $nilaiSurvey->ayahPekerjaan . "'"
                                : $nilaiSurvey->ayahPekerjaan !!},
                            penghasilan: {!! $nilaiSurvey->ayahPenghasilan !!}
                        },
                        ibu: {
                            pekerjaanLainnya: '{{ $nilaiSurvey->ibuPekerjaanLainnya }}',
                            pekerjaan: {!! $nilaiSurvey->ibuPekerjaan == 'LAINNYA'
                                ? "'" . $nilaiSurvey->ibuPekerjaan . "'"
                                : $nilaiSurvey->ibuPekerjaan !!},
                            penghasilan: {!! $nilaiSurvey->ibuPenghasilan !!}
                        }
                    },
                    tanggunganKeluarga: {!! $nilaiSurvey->tanggunganKeluarga !!},
                    tanggunganKeluargaStatus: '{{ $nilaiSurvey->tanggunganKeluargaStatus }}',
                    kepemilikanRumah: {!! $nilaiSurvey->kepemilikanRumah !!},
                    kepemilikanRumahStatus: '{{ $nilaiSurvey->kepemilikanRumahStatus }}',
                    bangunanRumah: {!! $nilaiSurvey->bangunanRumah !!},
                    bangunanRumahStatus: '{{ $nilaiSurvey->bangunanRumahStatus }}',
                    lantaiRumah: {!! $nilaiSurvey->lantaiRumah !!},
                    lantaiRumahStatus: '{{ $nilaiSurvey->lantaiRumahStatus }}',
                    kepemilikanListrik: {!! $nilaiSurvey->kepemilikanListrik !!},
                    kepemilikanListrikStatus: '{{ $nilaiSurvey->kepemilikanListrikStatus }}',
                    kondisiRumah: {
                        rumah: '{{ $nilaiSurvey->kondisiRumahStatus }}',
                        dapur: {
                            status: '{{ $nilaiSurvey->kondisiDapurStatus }}',
                            kondisi: {!! $nilaiSurvey->kondisiDapur !!}
                        },
                        kamarMandi: {
                            status: '{{ $nilaiSurvey->kondisiKamarMandiStatus }}',
                            kondisi: {!! $nilaiSurvey->kondisiKamarMandi !!}
                        },
                        wc: {
                            status: '{{ $nilaiSurvey->kondisiWcStatus }}',
                            kondisi: {!! $nilaiSurvey->kondisiWc !!}
                        },
                    },
                    catatan: `{{ $nilaiSurvey->catatan }}`,
                    berkasGdrive: '{{ $nilaiSurvey->berkasGdrive }}'
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
                    if (!s.tanggunganKeluarga) errors.push("Jumlah tanggungan keluarga wajib diisi");
                    if (!s.kepemilikanRumah) errors.push("Kepemilikan rumah wajib diisi");
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
                    return this.survey.ayah.kesehatan;
                },
                kesehatanAyahBadgeClass() {
                    const value = this.survey.ayah.kesehatan;
                    if (value <= 2.5) return 'bg-danger';
                    if (value <= 5) return 'bg-warning';
                    if (value <= 7.5) return 'bg-info';
                    return 'bg-success';
                },
                kondisiIbuSufficiancyText() {
                    return `${this.survey.ibu.kondisi}`;
                },
                kondisiIbuBadgeClass() {
                    const value = this.survey.ibu.kondisi;
                    if (value <= 2.5) return 'bg-danger';
                    if (value <= 5) return 'bg-warning';
                    if (value <= 7.5) return 'bg-info';
                    return 'bg-success';
                },
                kondisiDapurSufficiancyText() {
                    return `${this.survey.kondisiRumah.dapur.kondisi}`;
                },
                kondisiDapurBadgeClass() {
                    const value = this.survey.kondisiRumah.dapur.kondisi;
                    if (value <= 2.5) return 'bg-danger';
                    if (value <= 5) return 'bg-warning';
                    if (value <= 7.5) return 'bg-info';
                    return 'bg-success';
                },
                kondisiKamarMandiSufficiancyText() {
                    return this.survey.kondisiRumah.kamarMandi.kondisi;
                },
                kondisiKamarMandiBadgeClass() {
                    const value = this.survey.kondisiRumah.kamarMandi.kondisi;
                    if (value <= 2.5) return 'bg-danger';
                    if (value <= 5) return 'bg-warning';
                    if (value <= 7.5) return 'bg-info';
                    return 'bg-success';
                },
                kondisiWciSufficiancyText() {
                    return this.survey.kondisiRumah.wc.kondisi;
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
                    //
                },
                changeStatusSesuai(key) {
                    switch (key) {
                        case 'ayahNamaStatus':
                            if (this.survey.ayah.status == 'sesuai') {
                                this.survey.ayah.nama = this.isianMahasiswa.nama_ayah.value
                                this.change('ayahNama', this.survey.ayah.nama, '#elAyahNama', this.survey.ayah
                                    .status);
                            } else {
                                this.survey.ayah.nama = ''
                            }
                            break;
                        case 'ibuNamaStatus':
                            if (this.survey.ibu.status == 'sesuai') {
                                this.survey.ibu.nama = this.isianMahasiswa.nama_ibu.value
                                this.change('ibuNama', this.survey.ibu.nama, '#elIbuNama', this.survey.ibu
                                    .status);
                            } else {
                                this.survey.ibu.nama = ''
                            }
                            break;
                        case 'kepemilikanRumahStatus':
                            if (this.survey.kepemilikanRumahStatus == 'sesuai') {
                                this.survey.kepemilikanRumah = +this.isianMahasiswa.kepemilikan_rumah.value
                                this.change('kepemilikanRumah', this.survey.kepemilikanRumah, '#elKepemilikanRumah',
                                    this.survey.kepemilikanRumahStatus);
                            } else {
                                this.survey.kepemilikanRumah = ''
                            }
                            break;
                        case 'bangunanRumahStatus':
                            if (this.survey.bangunanRumahStatus == 'sesuai') {
                                this.survey.bangunanRumah = +this.isianMahasiswa.bangunan_rumah.value
                                this.change('bangunanRumah', this.survey.bangunanRumah, '#elBangunanRumah',
                                    this.survey.bangunanRumahStatus);
                            } else {
                                this.survey.bangunanRumah = ''
                            }
                            break;
                        case 'kepemilikanListrikStatus':
                            if (this.survey.kepemilikanListrikStatus == 'sesuai') {
                                this.survey.kepemilikanListrik = +this.isianMahasiswa.kepemilikan_listrik.value
                                this.change('kepemilikanListrik', this.survey.kepemilikanListrik,
                                    '#elKepemilikanListrik',
                                    this.survey.kepemilikanListrikStatus);
                            } else {
                                this.survey.kepemilikanListrik = ''
                            }
                            break;
                        case 'lantaiRumahStatus':
                            if (this.survey.lantaiRumahStatus == 'sesuai') {
                                this.survey.lantaiRumah = +this.isianMahasiswa.lantai_rumah.value
                                this.change('lantaiRumah', this.survey.lantaiRumah,
                                    '#elLantaiRumah',
                                    this.survey.lantaiRumahStatus);
                            } else {
                                this.survey.lantaiRumah = ''
                            }
                            break;
                        case 'tanggunganKeluargaStatus':
                            if (this.survey.tanggunganKeluargaStatus == 'sesuai') {
                                this.survey.tanggunganKeluarga = +this.isianMahasiswa.tanggungan_keluarga.value
                                this.change('tanggunganKeluarga', this.survey.tanggunganKeluarga,
                                    '#elTanggunganKeluarga', this.survey
                                    .tanggunganKeluargaStatus);
                            } else {
                                this.survey.ibu.nama = ''
                            }
                            break;
                    }
                },
                change(key, data, el, sesuai) {
                    if (key == 'kondisiDapur') {
                        if (!this.survey.kondisiRumah.dapur.status) {
                            this.survey.kondisiRumah.dapur.status = 'tidak';
                        }
                        if (!this.survey.kondisiRumah.dapur.kondisi) {
                            this.survey.kondisiRumah.dapur.kondisi = 1;
                        }
                        if (!data) {
                            data = this.survey.kondisiRumah.dapur.kondisi;
                        }
                    } else if (key == 'kondisiKamarMandi') {
                        if (!this.survey.kondisiRumah.kamarMandi.status) {
                            this.survey.kondisiRumah.kamarMandi.status = 'tidak';
                        }
                        if (!this.survey.kondisiRumah.kamarMandi.kondisi) {
                            this.survey.kondisiRumah.kamarMandi.kondisi = 1;
                        }
                        if (!data) {
                            data = this.survey.kondisiRumah.kamarMandi.kondisi;
                        }
                    } else if (key == 'kondisiWc') {
                        if (!this.survey.kondisiRumah.wc.status) {
                            this.survey.kondisiRumah.wc.status = 'tidak';
                        }
                        if (!this.survey.kondisiRumah.wc.kondisi) {
                            this.survey.kondisiRumah.wc.kondisi = 1;
                        }
                        if (!data) {
                            data = this.survey.kondisiRumah.wc.kondisi;
                        }
                    }

                    if (key == 'ayahPekerjaan' && data == 'LAINNYA') {
                        // SKIP
                    } else if (key == 'ibuPekerjaan' && data == 'LAINNYA') {
                        // SKIP
                    } else {
                        saveOnce(key, data, el, sesuai);
                    }
                },
                liveChage(key, data, el, sesuai, ms = 1000) { // ms = milissecond delay
                    if (timeout[key]) {
                        clearTimeout(timeout[key]);
                        delete timeout[key];
                    }
                    timeout[key] = setTimeout(() => {
                        if (key == 'ayahPekerjaanLainnya') {
                            saveOnce('ayahPekerjaan', 'LAINNYA:' + data, el, sesuai);
                        } else if (key == 'ibuPekerjaanLainnya') {
                            saveOnce('ibuPekerjaan', 'LAINNYA:' + data, el, sesuai);
                        } else {
                            saveOnce(key, data, el, sesuai);
                        }
                    }, ms);
                },
                resetSurvey() {
                    Swal.fire({
                        title: 'Konfirmasi',
                        html: `Apaka anda yakin ingin mereset survey?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then(result => {
                        if (result.isConfirmed) {
                            resetSurvey();
                        }
                    });
                }
            }
        });

        function saveOnce(key, data, el, sesuai) {
            $(el).html('<span class="fst-italic text-muted">Menyimpan...</span>');
            $.ajax({
                url: "{{ route('surveyor.survey.update.skor') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    key,
                    data,
                    sesuai: sesuai ?? null,
                    pendaftar: '{{ $pendaftar->pendaftar->id }}'
                },
                success: (respon) => {
                    $(el).html(
                        `<span class="fw-bold text-success"><i class="fas fa-check-circle"></i> update ${respon.date}</span>`
                    );
                },
                error: () => {
                    $(el).html('<span class="fw-bold text-danger">Gagal disimpan!</span>')
                }
            })
        }

        function resetSurvey() {
            Swal.fire({
                title: 'Sedang mereset...',
                showCancelButton: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                    $.ajax({
                        url: "{{ route('surveyor.survey.update.skor.reset') }}",
                        type: "DELETE",
                        data: {
                            _token: '{{ csrf_token() }}',
                            pendaftar: '{{ $pendaftar->pendaftar->id }}'
                        },
                        success: (respon) => {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Data telah direset.',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 1500,
                                didClose: () => {
                                    window.location.reload();
                                }
                            });
                        },
                        error: (err) => {
                            Swal.fire({
                                icon: 'error',
                                title: "Gagal",
                                text: msg.responseJSON.message
                            });
                        }
                    })
                },
                allowOutsideClick: false
            });
        }
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

        function reloadPage(selectElement) {
            var selectedValue = selectElement.value;
            let url = '{{ route('surveyor.survey.show', ['id' => 'ID']) }}';
            url = url.replace('ID', selectedValue);
            window.location.href = url;
        }
    </script>
@endpush
