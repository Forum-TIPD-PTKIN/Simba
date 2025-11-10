@extends('surveyor.template.master-template')

@section('title', 'Detail Survey Kondisi Mahasiswa')

@push('head')
    <style>
        .table th,
        .table td {
            vertical-align: top;
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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Surveyor</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('surveyor.survey') }}">Survei</a></li>
                                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Detail Survei</a></li>
                                <li class="breadcrumb-item" aria-current="page">Hasil Survei</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Hasil Survei Kondisi Mahasiswa</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="btn-group mb-3" role="group" aria-label="Button Cetak">
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary text-light"><span
                                class="ti ti-arrow-back-up"></span>
                            Kembali</a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-4">
                                <h6 class="fw-bold bg-light p-2 border">Identitas Mahasiswa</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0 w-100">
                                        <tr>
                                            <th style="width: 200px;">Nama</th>
                                            <td>{{ $data->mahasiswa->nama ?? '-' }}</td>
                                            <td rowspan="6" class="text-center">
                                                @if (!empty($data->mahasiswa->foto))
                                                    <img src="{{ $data->mahasiswa->foto }}" alt="Foto Mahasiswa"
                                                        class="rounded" style="max-width:120px">
                                                @else
                                                    <div class="text-muted small fst-italic">Foto tidak tersedia</div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="width: 200px;">NIM</th>
                                            <td>{{ $data->mahasiswa->nim ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Fakultas / Prodi</th>
                                            <td>{{ $data->mahasiswa->fakultas_prodi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. HP</th>
                                            <td>{{ $data->biodata_pendaftar->data->biodata->no_hp->value ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat KTP</th>
                                            <td>{{ $data->biodata_pendaftar->data->biodata->alamat_ktp->value ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            {{-- Informasi Beasiswa --}}
                            <div class="mb-4 table-responsive">
                                <h6 class="fw-bold bg-light p-2 border">Informasi Beasiswa</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <tr>
                                        <th style="width: 200px;">Nama Beasiswa</th>
                                        <td>{{ $data->beasiswa->nama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Tahun Kegiatan</th>
                                        <td>{{ $data->tahun_kegiatan->tahun ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kategori</th>
                                        <td>{{ $data->pemberkasan->data->pemberkasan->kategori->valOption ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Deskripsi</th>
                                        <td>{!! $data->beasiswa->deskripsi ?? '-' !!}</td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Biodata Keluarga --}}
                            <div class="mb-4 table-responsive">
                                <h6 class="fw-bold bg-light p-2 border">Biodata Keluarga</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <tr>
                                        <th style="width: 200px;">Nama Ayah</th>
                                        <td>{{ $data->hasil_survei->nilai->ayahNama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi Ayah</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->ayahKesehatan->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->ayahKesehatan?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin kelayakan
                                                    <span
                                                        class="badge bg-secondary">{{ $data->hasil_survei?->nilai?->ayahKesehatan?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Pekerjaan Ayah</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->ayahPekerjaan->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->ayahPekerjaan?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->ayahPekerjaan?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Penghasilan Ayah</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->ayahPenghasilan->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->ayahPenghasilan?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin kelayakan
                                                    <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->ayahPenghasilan?->value ?? 0 }}
                                                    </span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Nama Ibu</th>
                                        <td>{{ $data->hasil_survei->nilai->ibuNama ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi Ibu</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->ibuKondisi->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->ibuKondisi?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->ibuKondisi?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Pekerjaan Ibu</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->ibuPekerjaan->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->ibuPekerjaan?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->ibuPekerjaan?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Penghasilan Ibu</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->ibuPenghasilan->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->ibuPenghasilan?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->ibuPenghasilan?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Tanggungan Keluarga</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->tanggunganKeluarga->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->tanggunganKeluarga?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->tanggunganKeluarga?->value ?? 0 }}
                                                    </span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Kondisi Rumah --}}
                            <div class="mb-4 table-responsive">
                                <h6 class="fw-bold bg-light p-2 border">Kondisi Rumah</h6>
                                <table class="table table-sm table-bordered mb-0">
                                    <tr>
                                        <th style="width: 200px;">Status Kepemilikan Rumah</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->kepemilikanRumah->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->kepemilikanRumah?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->kepemilikanRumah?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Bangunan Rumah</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->bangunanRumah->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->bangunanRumah?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->bangunanRumah?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Lantai Rumah</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->lantaiRumah->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->lantaiRumah?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->lantaiRumah?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi Dapur</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->kondisiDapur->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->kondisiDapur?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->kondisiDapur?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi Kamar Mandi</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->kondisiKamarMandi->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->kondisiKamarMandi?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->kondisiKamarMandi?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Kondisi WC</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->kondisiWc->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->kondisiWc?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->kondisiWc?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Kepemilikan Listrik</th>
                                        <td>
                                            {{ $data->hasil_survei->nilai->kepemilikanListrik->text ?? '-' }}
                                            @if ($data->hasil_survei?->nilai?->kepemilikanListrik?->value)
                                                <span class="fw-bold fst-italic text-muted">- poin
                                                    kelayakan <span class="badge bg-secondary">
                                                        {{ $data->hasil_survei?->nilai?->kepemilikanListrik?->value ?? 0 }}</span>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Hasil Survei --}}
                            <div class="mb-4 table-responsive">
                                <h6 class="fw-bold bg-light p-2 border">Hasil Survei Lapangan</h6>
                                <table class="table table-sm table-bordered">
                                    <tr>
                                        <th style="width: 200px;" class="align-middle">Total Point</th>
                                        <td class="fw-bold fs-3 align-middle">{{ $data->hasil_survei->point ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Progress Survei (%)</th>
                                        <td>{{ $data->hasil_survei->persen ?? '-' }}%</td>
                                    </tr>
                                    <tr>
                                        <th>Catatan Surveyor</th>
                                        <td>{{ $data->hasil_survei->nilai->catatan ?? 'Tidak ada catatan' }}</td>
                                    </tr>
                                </table>
                            </div>

                            {{-- Lampiran --}}
                            <div class="mb-5 table-responsive">
                                <h6 class="fw-bold bg-light p-2 border">Lampiran / Berkas Google Drive</h6>
                                @if (!empty($data->hasil_survei->nilai->berkasGdrive))
                                    <a href="{{ $data->hasil_survei->nilai->berkasGdrive }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-link-45deg"></i> Lihat Berkas di Google Drive
                                    </a>
                                @else
                                    <p class="text-muted fst-italic mb-0">Tidak ada tautan berkas.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
