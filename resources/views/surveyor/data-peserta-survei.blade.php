@if (count($responden))
    <div class="table-responsive">
        <table class="table table-sm table-bordered text-center align-middle" id="pc-dt-simple">
            <thead class="bg-teal-100">
                <tr>
                    <th scope="col" width="5%">No</th>
                    <th scope="col" width="7%">NIM</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Prodi</th>
                    <th scope="col">Beasiswa</th>
                    <th scope="col" width="25%">Alamat</th>
                    <th scope="col">Instrumen Survei</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                @foreach ($responden as $key => $item)
                    <tr>
                        <td scope="row">{{ $loop->iteration }}</td>
                        <td scope="row">{{ $item->mahasiswa?->nim }}</td>
                        <td scope="row">{{ $item->mahasiswa?->nama }}</td>
                        <td scope="row">
                            <div class='flex-grow-1'>
                                <div class='row g-1'>
                                    <div class='col-12'>
                                        <h6 class='mb-0'>{{ $item->mahasiswa?->prodi_name }}</h6>
                                        <p class='text-muted mb-0'><small>{{ $item->mahasiswa?->fakultas_name }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td scope="row">
                            <div class='flex-grow-1'>
                                <div class='row g-1'>
                                    <div class='col-12'>
                                        <h6 class='mb-0'>{{ $item->beasiswa?->nama }}</h6>
                                        <p class='text-muted mb-0'><small>Tahun
                                                {{ $item->tahun_kegiatan?->tahun }}</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td scope="row">{{ $item->biodata_pendaftar?->data?->biodata?->alamat_ktp?->value }}</td>
                        <td scope="row"><button class="btn btn-sm btn-danger unduhInstrumenSurvei"
                                data-pendaftar-id="{{ $item->id }}" title="Cetak Instrumen Survei"><i
                                    class="far fa-file-alt"></i> Cetak</button></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-info text-center fs-3 fw-bold">Data tidak ditemukan</div>
@endif
