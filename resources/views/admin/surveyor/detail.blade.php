<div class="row">
    <div class="col-md-6">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Informasi Surveyor</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-4">Nama</dt>
                    <dd class="col-sm-8">{{ $detailSurveyor->user->name ?? '-' }}</dd>

                    <dt class="col-sm-4">Status</dt>
                    <dd class="col-sm-8">
                        {!! !$detailSurveyor->publish
                            ? '<span class="badge text-bg-warning">Draft</span>'
                            : '<span class="badge text-bg-success">Publish</span>' !!}
                    </dd>

                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Informasi Penugasan</h3>
            </div>
            <div class="card-body">
                <dl class="row">
                    <dt class="col-sm-5">Beasiswa</dt>
                    <dd class="col-sm-7">{{ $detailSurveyor->beasiswa->nama ?? '-' }}</dd>

                    <dt class="col-sm-5">Tahun Kegiatan</dt>
                    <dd class="col-sm-7">{{ $detailSurveyor->tahun_kegiatan->tahun ?? '-' }}</dd>

                </dl>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="card card-outline card-success">
    <div class="card-header">
        <h3 class="card-title">Daftar Mahasiswa yang Disurvei ({{ $detailSurveyor->surveyor_detail->count() }})</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th>Nama Mahasiswa</th>
                        <th>NIM</th>
                        <th>Alamat</th>
                        <th>Progress</th>
                        {{-- <th>Aksi</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalPersen = 0;
                        $jumlahData = 0;
                    @endphp
                    @forelse ($detailSurveyor->surveyor_detail as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                {{ $detail->pendaftar->mahasiswa->nama }}
                            </td>
                            <td>
                                {{ $detail->pendaftar->mahasiswa->nim }}
                            </td>
                            <td>
                                {{ $detail->pendaftar->biodata_pendaftar->data->biodata->alamat_ktp->value }}
                            </td>
                            <td>
                                @php
                                    $status = '';
                                    $percentage = $detail->pendaftar->hasil_survei->persen ?? 0;
                                    $totalPersen += $percentage;
                                    $jumlahData++;
                                    $colorClass = 'bg-light-danger';
                                    if ($percentage > 75) {
                                        $colorClass = 'bg-light-success';
                                    } elseif ($percentage > 50) {
                                        $colorClass = 'bg-light-info';
                                    } elseif ($percentage > 25) {
                                        $colorClass = 'bg-light-warning';
                                    }
                                    $status = '<span class="badge ' . $colorClass . '">' . $percentage . '%</span>';
                                @endphp
                                {!! $status !!}
                            </td>
                            {{-- <td>
                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                    <i class="fas fa-eye"></i> Lihat Hasil
                                </button>
                            </td> --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada pendaftar yang ditugaskan.</td>
                        </tr>
                    @endforelse

                    {{-- tampilkan rata-rata persentase --}}
                    @if ($jumlahData > 0)
                        @php
                            $rataRataPersen = round($totalPersen / $jumlahData, 2);
                            $colorTotal =
                                $rataRataPersen > 75
                                    ? 'bg-light-success'
                                    : ($rataRataPersen > 50
                                        ? 'bg-light-info'
                                        : ($rataRataPersen > 25
                                            ? 'bg-light-warning'
                                            : 'bg-light-danger'));
                        @endphp
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total Keseluruhan</td>
                            <td>
                                <span class="badge {{ $colorTotal }}">{{ $rataRataPersen }}%</span>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
