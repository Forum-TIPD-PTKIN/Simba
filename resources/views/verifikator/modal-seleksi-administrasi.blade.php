@php
    $deskripsi_verifikasi = json_decode($data->latest_status?->deskripsi);
@endphp
<input type="text" class="d-none" name="pendaftar_id" value="{{ $data->id }}">
<input type="text" class="d-none" name="pendaftar_status_id" value="{{ $data->latest_status?->id }}">
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
        </dl>
    </div>
    <div class="col-12 col-md-6">
        <dl class="row">
            <dt class="col-sm-9">Tahun Masuk Kuliah</dt>
            <dd class="col-sm-3">{{ $data_pmb->tahun_masuk ?? '-' }}</dd>

            <dt class="col-sm-9">Tahun Lulus Jenjang SMA/sederajat</dt>
            <dd class="col-sm-3">{{ $data_pmb->sekolah_asal?->tahun_lulus ?? '-' }}</dd>
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
                                        {{ $value->type === 'select' || $value->type === 'radio' ? $value->valOption : $value->value }}
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
                        <th>
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <span>Valid?</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="berkas-control">
                    @foreach ($data->pemberkasan?->data ?? [] as $item)
                        @foreach (collect($item) as $value)
                            <tr>
                                <td class="text-start w-25">{{ $value->text }}</td>
                                <td>
                                    @if ($value->type === 'file')
                                        <a href="javascript:void(0);" data-extension="{{ $value->value?->extension }}"
                                            data-url="{{ $value->value?->url }}" data-type="{{ $value->text }}"
                                            class="fw-bold text-decoration-underline base-berkas"
                                            onclick="viewControl(this)">{{ $value->value?->name }}</a>
                                        {{-- <a href="javascript:void(0);"
                                    onclick="window.open('{{ $value->value?->url }}?type=pdf', '_blank'
                                    , 'location=yes,height=570,width=520,scrollbars=yes,status=yes' );">{{ $value->value?->name }}</a> --}}
                                    @else
                                        {{ $value->valOption }}
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @php
                                        $filter_deskripsi = array_filter(
                                            $deskripsi_verifikasi?->valid_form ?? [],
                                            function ($item) use ($value) {
                                                return array_key_exists(
                                                    strtolower(str_replace(' ', '_', $value->text)),
                                                    (array) $item,
                                                );
                                            },
                                        );
                                        $reset_filter = reset($filter_deskripsi);
                                        $is_valid = $reset_filter ? current((array) $reset_filter) : null;
                                    @endphp
                                    <div class="form-check form-switch d-flex justify-content-center">
                                        <input type="text" class="d-none"
                                            name="verifikasi[{{ strtolower(str_replace(' ', '_', $value->text)) }}]"
                                            value="0">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                            name="verifikasi[{{ strtolower(str_replace(' ', '_', $value->text)) }}]"
                                            id="verifikasi-{{ strtolower(str_replace(' ', '-', $value->text)) }}"
                                            value="1" @checked($is_valid === 'Valid')>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-center align-middle">
                                <div class="d-flex gap-2 justify-content-center align-items-center">
                                    <button type="button" title="Semua benar" class="btn btn-sm btn-outline-primary"
                                        onclick="benarSemua(this, true)">
                                        <i class="ti ti-check"></i>
                                    </button>
                                    <button type="button" title="Semua salah" class="btn btn-sm btn-outline-danger"
                                        onclick="benarSemua(this, false)">
                                        <i class="ti ti-x"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mb-3 text-center">
    <div class="form-check">
        <input type="radio" class="btn-check" name="status_verval" id="success-status" value="success"
            autocomplete="off" @checked($data->latest_status?->status === 'LOLOS ADMINISTRASI')>
        <label class="btn btn-outline-success" for="success-status"><i class="ti ti-file-check"></i>
            Lolos</label>

        <input type="radio" class="btn-check" name="status_verval" id="fail-status" value="fail"
            autocomplete="off" @checked($data->latest_status?->status === 'GAGAL ADMINISTRASI')>
        <label class="btn btn-outline-danger" for="fail-status"><i class="ti ti-file-x"></i> Tidak
            Lolos</label>
    </div>
</div>

<div class="mb-3">
    <label for="catatan" class="form-label">Catatan</label>
    <textarea class="form-control" id="catatan" name="catatan" rows="3">{!! $deskripsi_verifikasi?->catatan !!}</textarea>
</div>

<script>
    function benarSemua(e, status = true) {
        const isChecked = status;
        const container = e.closest('.berkas-control');
        const checkboxes = container.querySelectorAll('.form-check-input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    }
</script>
