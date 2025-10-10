<input type="text" class="d-none" name="pendaftar_id" value="{{ $data->id }}">
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
            <dd class="col-sm-3">{{ $data_pmb->tahun_masuk }}</dd>

            <dt class="col-sm-9">Tahun Lulus Jenjang SMA/sederajat</dt>
            <dd class="col-sm-3">{{ $data_pmb->sekolah_asal?->tahun_lulus }}</dd>
        </dl>
    </div>
</div>

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
        <tbody id="berkas-control">
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
                                {{-- <a href="javascript:void(0);"
                                    onclick="window.open('{{ $value->value?->url }}?type=pdf', '_blank'
                                    , 'location=yes,height=570,width=520,scrollbars=yes,status=yes' );">{{ $value->value?->name }}</a> --}}
                            @else
                                {{ $value->valOption }}
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input type="text" class="d-none"
                                    name="verifikasi[{{ strtolower(str_replace(' ', '_', $value->text)) }}]"
                                    value="0">
                                <input class="form-check-input" type="checkbox" role="switch"
                                    name="verifikasi[{{ strtolower(str_replace(' ', '_', $value->text)) }}]"
                                    id="verifikasi-{{ strtolower(str_replace(' ', '-', $value->text)) }}"
                                    value="1">
                            </div>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="2"></td>
                    <td class="text-center align-middle">
                        <div class="d-flex gap-2 justify-content-center align-items-center">
                            <button type="button" title="Semua benar" class="btn btn-sm btn-outline-primary"
                                onclick="benarSemua(true)">
                                <i class="ti ti-check"></i>
                            </button>
                            <button type="button" title="Semua salah" class="btn btn-sm btn-outline-danger"
                                onclick="benarSemua(false)">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mb-3 text-center">
        <div class="form-check">
            <input type="radio" class="btn-check" name="status_verval" id="success-status" value="success"
                autocomplete="off">
            <label class="btn btn-outline-success" for="success-status"><i class="ti ti-file-check"></i> Lolos</label>

            <input type="radio" class="btn-check" name="status_verval" id="fail-status" value="fail"
                autocomplete="off">
            <label class="btn btn-outline-danger" for="fail-status"><i class="ti ti-file-x"></i> Tidak Lolos</label>
        </div>
    </div>

    <div class="mb-3">
        <label for="catatan" class="form-label">Catatan</label>
        <textarea class="form-control" id="catatan" name="catatan" rows="3"></textarea>
    </div>
</div>

<script>
    function benarSemua(status = true) {
        const isChecked = status;
        const checkboxes = document.querySelectorAll('#berkas-control .form-check-input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    }
</script>
