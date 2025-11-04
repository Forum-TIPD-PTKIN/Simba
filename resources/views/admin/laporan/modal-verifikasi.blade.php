@php
    $status_seleksi_administrasi = collect($data->pendaftar_status)
        ->filter(fn($item) => in_array($item->status, ['LOLOS ADMINISTRASI', 'GAGAL ADMINISTRASI']))
        ->first();
    $deskripsi_verifikasi = json_decode($status_seleksi_administrasi?->deskripsi);
@endphp
<div class="row">
    <div class="col-12 col-md-6">
        <dl class="row">
            <dt class="col-sm-6">Nama</dt>
            <dd class="col-sm-6">{{ $data->mahasiswa?->nama }}</dd>

            <dt class="col-sm-6">NIM</dt>
            <dd class="col-sm-6">{{ $data->mahasiswa?->nim }}</dd>

            <dt class="col-sm-6">Fakultas/Prodi</dt>
            <dd class="col-sm-6">{{ $data->mahasiswa?->fakultas_prodi }}</dd>

            <dt class="col-sm-6 text-truncate">Beasiswa/Tahun</dt>
            <dd class="col-sm-6">{{ $data->beasiswa?->nama }}/{{ $data->tahun_kegiatan?->tahun }}</dd>

            <dt class="col-sm-8">Tahun Masuk Kuliah</dt>
            <dd class="col-sm-4">{{ $data_pmb->tahun_masuk }}</dd>

            <dt class="col-sm-8">Tahun Lulus SMA/sederajat</dt>
            <dd class="col-sm-4">{{ $data_pmb->sekolah_asal?->tahun_lulus }}</dd>
        </dl>
    </div>
    <div class="col-12 col-md-6">
        <dl class="row">
            <dt class="col-sm-8">Status Seleksi Administrasi</dt>
            <dd class="col-sm-4">
                @if (str_contains(strtolower($data->latest_status?->status), 'lolos'))
                    <span class="badge badge-sm bg-success">LOLOS</span>
                @else
                    <span class="badge badge-sm bg-danger">GAGAL</span>
                @endif
            </dd>

            <dt class="col-sm-8">Verifikator</dt>
            <dd class="col-sm-4">{{ $deskripsi_verifikasi?->verifikator }}</dd>

            <dt class="col-sm-8">Tanggal Verifikasi</dt>
            <dd class="col-sm-4">
                {{ \Carbon\Carbon::parse(count($data->pendaftar_status) ? $data->pendaftar_status[0]->created_at : null)->translatedFormat('d-m-Y H:i:s') }}
            </dd>
        </dl>
    </div>
</div>

<ul class="nav nav-tabs analytics-tab" id="dataPendaftarTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="biodata-tab" data-bs-toggle="tab" data-bs-target="#biodata-tab-pane" type="button"
            role="tab" aria-controls="biodata-tab-pane" aria-selected="false">Biodata</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="berkas-tab" data-bs-toggle="tab" data-bs-target="#berkas-tab-pane"
            type="button" role="tab" aria-controls="berkas-tab-pane" aria-selected="true">Berkas</button>
    </li>
</ul>
<div class="tab-content" id="dataPendaftarTabContent">
    <div class="tab-pane fade" id="biodata-tab-pane" role="tabpanel" aria-labelledby="biodata-tab">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Isian</th>
                    </tr>
                </thead>
                <tbody class="berkas-control">
                    @foreach ($data->biodata_pendaftar?->data ?? [] as $item)
                        @foreach (collect($item) as $value)
                            <tr>
                                <td class="text-start w-25">{{ $value->text }}</td>
                                <td>
                                    @if ($value->type === 'file')
                                        <a href="javascript:void(0);" data-extension="{{ $value->value?->extension }}"
                                            data-url="{{ $value->value?->url }}" data-type="{{ $value->text }}"
                                            class="fw-bold text-decoration-underline base-berkas"
                                            onclick="viewControl(this)">{{ $value->value?->name }}</a>
                                    @else
                                        {{ $value->type === 'select' ? $value->valOption : $value->value }}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade show active" id="berkas-tab-pane" role="tabpanel" aria-labelledby="berkas-tab">
        <div class="table-responsive">
            <table class="table table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th>Label</th>
                        <th>Isian</th>
                        <th>Valid?</th>
                    </tr>
                </thead>
                <tbody class="berkas-control">
                    @foreach ($data->pemberkasan?->data as $item)
                        @foreach (collect($item) as $value)
                            <tr>
                                <td class="text-start w-25">{{ $value->text }}</td>
                                <td>
                                    @if ($value->type === 'file')
                                        <a href="javascript:void(0);" data-extension="{{ $value->value?->extension }}"
                                            data-url="{{ $value->value?->url }}" data-type="{{ $value->text }}"
                                            class="fw-bold text-decoration-underline base-berkas"
                                            onclick="viewControl(this)">{{ $value->value?->name }}</a>
                                    @else
                                        {{ $value->valOption }}
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @php
                                        $filter_deskripsi = array_filter($deskripsi_verifikasi->valid_form, function (
                                            $item,
                                        ) use ($value) {
                                            return array_key_exists(
                                                strtolower(str_replace(' ', '_', $value->text)),
                                                (array) $item,
                                            );
                                        });
                                        $reset_filter = reset($filter_deskripsi);
                                        $is_valid = $reset_filter ? current((array) $reset_filter) : null;
                                    @endphp

                                    @if (strtolower($is_valid) === 'valid')
                                        <span class="text-success h4 fw-bold"><i class="ti ti-checkbox"></i></span>
                                    @else
                                        <span class="text-danger h4 fw-bold"><i class="ti ti-x"></i></span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mb-3">
    <div class="alert alert-info">
        <h5><i class="ti ti-notes"></i> Catatan Verifikator</h5>
        {!! $deskripsi_verifikasi?->catatan !!}
    </div>
</div>

@if ($is_jadwal_verifikasi || $is_jadwal_sanggah)
    <div class="d-grid gap-2">
        <button type="button" class="btn btn-danger btn-unverified" data-id="{{ $data->latest_status?->id }}"
            data-pendaftar="{{ $data }}"><i class="ti ti-rotate-2"></i>
            Batalkan Status Seleksi Administrasi</button>
    </div>
@endif
