@extends('admin.template.master-template')

@section('title', 'Seleksi Administrasi')

@push('head')
    <style>
        .error {
            width: 100%;
            margin-top: .25rem;
            font-size: .875em;
            color: #dc3545;
        }

        .swal2-container {
            z-index: 2000 !important;
        }
    </style>
@endpush

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
                                <select class="form-select form-select-sm" aria-label="Filter status" id="flt_status">
                                    <option value="" selected>-- Semua --</option>
                                    @foreach ($status as $item)
                                        <option value="{{ $item }}">{{ $item }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="btn-group mb-3" role="group" aria-label="Button Modal Jadwal Kegiatan">
                                <button type="button" class="btn btn-sm btn-success"
                                    id="unduhHasilSeleksiAdministrasi"><span class="far fa-file-excel"></span>
                                    Unduh data excel</button>
                                <button type="button" class="btn btn-sm btn-warning"
                                    id="rekapVerifikatorSeleksiAdministrasi"><span class="fas fa-user-check"></span>
                                    Rekap Verifikator</button>
                            </div>
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
                                            <th scope="col">Verifikator</th>
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

            <!-- Modal -->
            <div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-labelledby="modalVerifikasiLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalVerifikasiLabel">Verifikasi Pendaftaran</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Rekap Verifikator -->
            <div class="modal fade" id="modalRekapVerifikator" tabindex="-1" aria-labelledby="modalRekapVerifikatorLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalRekapVerifikatorLabel">Data Rekap Verifikator</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
        // Reload data
        function reloadData() {
            $.ajax({
                url: "{{ route('admin.laporan.verifikasi.jadwal') }}",
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

        // Datatable
        const dataTable = $("#dataTable").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.laporan.verifikasi.data') }}",
                type: "GET",
                data: (data) => {
                    data._token = "{{ csrf_token() }}";
                    data.flt_tahun = $('#flt_tahun').val();
                    data.flt_beasiswa = $('#flt_beasiswa').val();
                    data.flt_status = $('#flt_status').val();
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
                    data: 'mahasiswa.fakultas_prodi',
                    name: 'mahasiswas.prodi'
                },
                {
                    data: 'beasiswa',
                    searchable: false
                },
                {
                    data: 'verifikator',
                    name: 'ps.deskripsi'
                },
                {
                    data: 'status',
                    searchable: false
                },
                {
                    data: 'action',
                    nama: 'action',
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
            searching: true,
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
        // Lihat Verifikasi data
        function verifikasiData(id) {
            let url = "{{ route('admin.laporan.verifikasi.edit', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
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
                success: (res) => {
                    $('#modalVerifikasi .modal-body').html(res);

                    $('#modalVerifikasi').modal('show');
                    Swal.close();
                }
            });
        }

        // Batal Verifikasi Data
        $(document).on('click', '.btn-unverified', function() {
            let url = "{{ route('admin.laporan.verifikasi.update', ':id') }}";
            url = url.replace(':id', $(this).data('id'));
            const pendaftar = $(this).data('pendaftar');

            Swal.fire({
                title: 'Apa Anda Yakin?',
                html: `Status Seleksi Administrasi a.n. ${pendaftar?.mahasiswa?.nama} akan dibatalkan`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjut!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "PUT",
                        url: url,
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(data) {
                            $('#modalVerifikasi').modal('hide');
                            dataTable.ajax.reload(null, false);

                            const msg = JSON.parse(JSON.stringify(data));
                            Swal.fire({
                                icon: msg.icon,
                                title: msg.title,
                                text: msg.message,
                                timer: 1500,
                                timerProgressBar: true,
                                customClass: {
                                    timerProgressBar: 'bg-success bg-opacity-50'
                                }
                            });
                        },
                        error: function(data) {
                            const msg = JSON.parse(JSON.stringify(data));
                            Swal.fire({
                                icon: 'error',
                                title: "Gagal",
                                text: msg.responseJSON.message
                            });
                        }
                    });
                }
            });
        });
    </script>

    <script>
        function viewControl(e) {
            let container = e.closest('.berkas-control');
            let links = container.querySelectorAll('.base-berkas');
            let urls = []
            links.forEach(link => {
                let url = link.getAttribute('data-url');
                let type = link.getAttribute('data-type');
                let extension = link.getAttribute('data-extension');
                urls.push({
                    url,
                    type,
                    extension
                })
            });
            const data = {
                active: {
                    url: e.getAttribute('data-url'),
                    type: e.getAttribute('data-type'),
                    extension: e.getAttribute('data-extension')
                },
                data: urls
            }
            $.ajax({
                type: 'post',
                url: "{{ route('view.control') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    data: data
                },
                dataType: 'HTML',
                success: function(data) {
                    const winUrl = URL.createObjectURL(
                        new Blob([data], {
                            type: "text/html"
                        })
                    );

                    const margin = 100; // Jarak tepi agar tidak full full banget
                    const width = window.screen.availWidth - margin * 8;
                    const height = window.screen.availHeight - margin * 2;
                    const left = (window.screen.availWidth - width) / 2;
                    const top = (window.screen.availHeight - height) / 2;

                    const win = window.open(
                        winUrl,
                        "win",
                        `width=${width},height=${height},top=${top},left=${left}`
                    );
                }
            });
        }
    </script>

    <script>
        $(document).on('click', '#unduhHasilSeleksiAdministrasi', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val(),
                status = $('#flt_status').val();

            $.ajax({
                url: "{{ route('admin.laporan.verifikasi.unduh') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa,
                    status: status
                },
                xhr: function() {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 2) { // Headers received
                            if (xhr.status === 200) {
                                xhr.responseType = 'blob';
                            } else {
                                xhr.responseType = 'text'; // For error messages
                            }
                        }
                    };
                    return xhr;
                },
                beforeSend: () => {
                    Swal.fire({
                        title: 'Memproses berkas...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });
                },
                success: function(response, status, xhr) {
                    var disposition = xhr.getResponseHeader(
                        'content-disposition');
                    var matches = /"([^""]*)"/.exec(disposition);
                    var filename = (matches != null && matches[1] ? matches[1] :
                        'Rekap data pendaftar.xlsx');

                    var blob = new Blob([response], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });

                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = filename;
                    link.click();
                    link.remove();

                    Swal.close();
                },
                error: function(error) {
                    const msg = JSON.parse(error.responseText);
                    Swal.fire({
                        title: 'Gagal',
                        text: error && error.status !== 200 ?
                            (typeof msg === 'string' ? msg : msg.message) :
                            'Tidak dapat melakukan download file. Terjadi kesalahan atau data tidak tersedia',
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        customClass: {
                            timerProgressBar: 'bg-danger'
                        }
                    });
                }
            });
        });

        $(document).on('click', '#rekapVerifikatorSeleksiAdministrasi', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val();

            $.ajax({
                url: "{{ route('admin.laporan.verifikasi.rekap-verifikator') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa
                },
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
                success: (res) => {
                    $('#modalRekapVerifikator .modal-body').html(res);

                    $('#modalRekapVerifikator').modal('show');
                    Swal.close();
                }
            });
        });
    </script>
@endpush
