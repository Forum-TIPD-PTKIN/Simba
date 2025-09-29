@extends('verifikator.template.master-template')

@section('title', 'Seleksi Administrasi')

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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Seleksi Administrasi</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Seleksi Administrasi</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <h5 class="mb-3 mb-sm-0">Data Pendaftar</h5>
                            <div class="d-flex gap-1">
                                <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan"
                                    id="flt_tahun">
                                    @foreach ($tahun_kegiatan as $item)
                                        <option value="{{ $item->id }}" @selected($loop->first)>{{ $item->tahun }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-select form-select-sm" aria-label="Filter beasiswa" id="flt_beasiswa">
                                    @foreach ($beasiswa as $item)
                                        <option value="{{ $item->id }}" @selected($loop->first)>{{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div
                                class="alert {{ $jadwal_kegiatan ? 'alert-warning' : 'alert-danger' }} container-alert-jadwal">
                                <h5><i class="ti ti-calendar-event"></i> Jadwal Kegiatan</h5>
                                @if ($jadwal_kegiatan)
                                    Seleksi administrasi untuk beasiswa {{ $beasiswa[0]->nama }} tahun
                                    {{ $tahun_kegiatan[0]->tahun }} dimulai dari
                                    {{ $jadwal_kegiatan?->tanggal_mulai }} s.d. {{ $jadwal_kegiatan?->tanggal_selesai }}
                                @else
                                    Jadwal kegiatan belum dibuat oleh Administrator
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover text-center align-middle"
                                    id="dataTable">
                                    <thead class="bg-cyan-100">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">NIM</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Fakultas/Prodi</th>
                                            <th scope="col">Beasiswa</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
@endpush

@push('script')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script>
        function reloadData() {
            $.ajax({
                url: "{{ route('verifikator.seleksi-administrasi.jadwal') }}",
                data: {
                    tahun: $('#flt_tahun').val(),
                    beasiswa: $('#flt_beasiswa').val()
                },
                success: (res) => {
                    const alert_jadwal = $('.container-alert-jadwal'),
                        text = res && Object.keys(res).length > 0 ?
                        `Seleksi administrasi untuk beasiswa ${res.beasiswa?.nama} tahun ${res.tahun_kegiatan?.tahun} dimulai dari ${res.tanggal_mulai} s.d. ${res.tanggal_selesai}` :
                        `Jadwal kegiatan belum dibuat oleh Administrator`;

                    alert_jadwal
                        .removeClass(function(index, className) {
                            return (className.match(/(^|\s)alert-\S+/g) || []).join(' ');
                        })
                        .addClass((res && Object.keys(res).length > 0) ? 'alert-warning' : 'alert-danger')
                        .html('')
                        .append(`
                        <h5><i class="ti ti-calendar-event"></i> Jadwal Kegiatan</h5>
                        ${text}
                    `);
                }
            });

            dataTable.ajax.reload(null, false);
        }

        const dataTable = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('verifikator.seleksi-administrasi.data') }}",
                type: "GET",
                data: (data) => {
                    data._token = "{{ csrf_token() }}";
                    data.flt_tahun = $('#flt_tahun').val();
                    data.flt_beasiswa = $('#flt_beasiswa').val();
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'mahasiswa.nim'
                },
                {
                    data: 'mahasiswa.nama'
                },
                {
                    data: 'mahasiswa.fakultas_prodi'
                },
                {
                    data: 'beasiswa'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            responsive: true,
            autoWidth: true,
            info: true,
            fixedColumns: true,
            fixedHeader: true,
            ordering: false,
            searching: false,
            language: {
                "url": 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json',
            },
            columnDefs: [{
                "targets": "_all",
                "className": "dt-head-center dt-body-center cell-border",
            }],
        });
    </script>

    <script>
        function getData(id) {
            let url = "{{ route('verifikator.seleksi-administrasi.edit', ':id') }}";
            url = url.replace(':id', id);

            return $.ajax({
                url: url,
                dataType: "JSON",
                beforeSend: () => {
                    Swal.fire({
                        title: 'Mengambil data...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });
                },
                complete: () => {
                    Swal.close();
                }
            });
        }

        async function verifikasiData(id) {
            const response = await getData(id),
                data = JSON.parse(response?.pemberkasan?.data);

            Object.keys(data).forEach(item => {
                Object.keys(data[item]).forEach(i => {
                    console.log(data[item][i]);
                });
            });
        }
    </script>
@endpush
