<input type="hidden" id="count-data-pendaftar" value="{{ $count_pendaftar }}">
<input type="hidden" id="tahun-kegiatan" value="{{ $tahun }}">
<input type="hidden" id="beasiswa" value="{{ $beasiswa }}">
<div class="table-responsive">
    <table class="table table-bordered align-middle text-center" id="tablePendaftarLolosAdministrasi">
        <thead class="bg-cyan-100">
            <tr>
                <th scope="col">#</th>
                <th scope="col">NIM</th>
                <th scope="col">Nama</th>
                <th scope="col">Prodi</th>
                <th scope="col">Beasiswa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pendaftar as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->mahasiswa?->nim }}</td>
                    <td>{{ $item->mahasiswa?->nama }}</td>
                    <td>{{ $item->mahasiswa?->prodi_name }}</td>
                    <td>{{ $item->beasiswa?->nama }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
