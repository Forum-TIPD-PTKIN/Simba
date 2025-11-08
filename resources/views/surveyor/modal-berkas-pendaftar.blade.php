@php
    $deskripsi_verifikasi = json_decode($data->latest_status?->deskripsi);
@endphp
<div class="row">
    <div class="col-12">
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
</div>

<div class="table-responsive">
    <table class="table table-bordered text-center align-middle">
        <thead>
            <tr>
                <th>Label</th>
                <th>Isian</th>
                <th>
                    <div class="d-flex justify-content-center align-items-center gap-1">
                        <span>Aksi</span>
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
                            @else
                                {{ $value->valOption }}
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            @if ($value->type === 'file')
                                <a target="_blank"
                                    href="{{ route('download.file', ['path' => urlencode(\Crypt::encrypt($value->value?->path))]) }}">Unduh
                                    File</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
