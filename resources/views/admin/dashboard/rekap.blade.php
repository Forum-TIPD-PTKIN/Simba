<div class="tab-pane fade show active" id="status-pendaftar-tab-pane" role="tabpanel" aria-labelledby="status-pendaftar-tab"
    tabindex="0">
    <div class="row">
        @forelse ($rekap_status as $item)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avtar avtar-s bg-light-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-user-square-rounded">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" />
                                        <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
                                        <path d="M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-grow-0 ms-3">
                                <h6 class="mb-0">{{ $item->status }}</h6>
                            </div>
                        </div>
                        <div class="bg-body p-3 mt-3 rounded">
                            <h3 class="mb-0 text-center">{{ $item->jumlah }} <span class="h6">Peserta</span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info d-flex flex-column align-items-center justify-content-center" role="alert">
                <i class="material-icons-two-tone f-40"> info</i>
                <div class="fw-bold">Tidak ada data</div>
            </div>
        @endforelse
    </div>
</div>

<div class="tab-pane fade" id="prodi-pendaftar-tab-pane" role="tabpanel" aria-labelledby="prodi-pendaftar-tab"
    tabindex="0">
    <table class="table table-bordered text-center align-middle" id="pc-dt-simple">
        <thead>
            <tr>
                <th>Program Studi</th>
                <th>Status Pendaftaran Terakhir</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekap_prodi as $key => $item)
                <tr>
                    @php
                        [$kd_prodi, $nm_prodi] = explode('|', $key);
                    @endphp
                    <td rowspan="{{ count($item) + 1 }}">{{ $nm_prodi }}</td>
                </tr>
                @foreach ($item as $k => $i)
                    <tr>
                        <td>{{ $i->status }}</td>
                        <td>{{ $i->jumlah }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="3" class="text-center">
                        <div class="d-flex align-items-center justify-content-center" role="alert">
                            <i class="material-icons-two-tone f-14"> info</i>
                            <div>Tidak ada data</div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
