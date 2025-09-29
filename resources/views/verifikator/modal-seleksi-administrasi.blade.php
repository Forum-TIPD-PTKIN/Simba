<dl class="row">
    <dt class="col-sm-3">Nama</dt>
    <dd class="col-sm-9">{{ $data->mahasiswa?->nama }}</dd>

    <dt class="col-sm-3">NIM</dt>
    <dd class="col-sm-9">{{ $data->mahasiswa?->nim }}</dd>

    <dt class="col-sm-3">Fakultas/Prodi</dt>
    <dd class="col-sm-9">{{ $data->mahasiswa?->fakultas_prodi }}</dd>

    <dt class="col-sm-3 text-truncate">Beasiswa/Tahun</dt>
    <dd class="col-sm-9">{{ $data->beasiswa?->nama }}/{{ $data->tahun_kegiatan?->tahun }}</dd>
</dl>

<div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
        <thead>
            <tr>
                <th>Label</th>
                <th>Isian</th>
                <th>Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data->pemberkasan?->data as $item)
                @foreach (collect($item) as $value)
                    <tr>
                        <td>{{ $value->text }}</td>
                        <td>
                            @if ($value->type === 'file')
                                <a href="javascript:void(0);"
                                    onclick="window.open('{{ $value->value?->url }}?type=pdf', '_blank'
                                    , 'location=yes,height=570,width=520,scrollbars=yes,status=yes' );">{{ $value->value?->name }}</a>
                            @else
                                {{ $value->value }}
                            @endif
                        </td>
                        <td>
                            <input type="checkbox"
                                name="verifikasi[{{ strtolower(str_replace(' ', '_', $value->text)) }}]"
                                class="form-check-input" id="verifikasi" value="1">
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="mb-3 text-center">
        <input type="radio" class="btn-check" name="status_verval" id="success-status" value="success"
            autocomplete="off">
        <label class="btn btn-outline-success" for="success-status"><i class="ti ti-file-check"></i> Lolos</label>

        <input type="radio" class="btn-check" name="status_verval" id="fail-status" value="fail" autocomplete="off">
        <label class="btn btn-outline-danger" for="fail-status"><i class="ti ti-file-x"></i> Tidak Lolos</label>
    </div>
</div>
