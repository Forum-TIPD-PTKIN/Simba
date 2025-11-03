@extends('admin.template.master-template')

@section('title', 'Tes Potensi Akademik')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">

    <style>
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
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Tes Potensi Akademik</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Tes Potensi Akademik</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!--[ Main Content ] start-->
            <div class="row" id="app-vue">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                            <h5 class="mb-3 mb-sm-0">Data Peserta Tes Potensi Akademik</h5>
                            <div class="d-flex gap-1 align-items-start flex-wrap">
                                <div>
                                    <label for="flt_tahun" class="form-label small">Tahun</label>
                                    <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan"
                                        id="flt_tahun">
                                        @foreach ($tahun_kegiatan as $item)
                                            <option value="{{ $item->id }}" @selected($loop->first)>
                                                {{ $item->tahun }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="flt_beasiswa" class="form-label small">Beasiswa</label>
                                    <select class="form-select form-select-sm" aria-label="Filter beasiswa"
                                        id="flt_beasiswa">
                                        @foreach ($beasiswa as $item)
                                            <option value="{{ $item->id }}" @selected($loop->first)>
                                                {{ $item->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="flt_tanggal_ujian" class="form-label small">Tanggal</label>
                                    <select class="form-select form-select-sm" aria-label="Filter tanggal ujian"
                                        id="flt_tanggal_ujian">
                                        <option value="" selected>-- All --</option>
                                        @foreach ($tanggal_ujian as $item)
                                            <option value="{{ $item }}">
                                                {{ \Carbon\Carbon::parse($item)->translatedFormat('d-m-Y') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="flt_sesi" class="form-label small">Sesi</label>
                                    <select class="form-select form-select-sm" aria-label="Filter sesi" id="flt_sesi">
                                        <option value="" selected>-- All --</option>
                                        @foreach ($sesi as $item)
                                            <option value="{{ $item }}">
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="flt_ruang" class="form-label small">Ruang</label>
                                    <select class="form-select form-select-sm" aria-label="Filter ruang" id="flt_ruang">
                                        <option value="" selected>-- All --</option>
                                        @foreach ($ruang as $item)
                                            <option value="{{ $item }}">
                                                {{ $item }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="align-self-end">
                                    <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-auto g-2 gap-1 mb-3" role="group"
                                aria-label="Button Generate Peserta Tes">
                                <button type="button" class="btn btn-sm btn-primary" id="generateDataPesertaTes"><span
                                        class="ti ti-file-import"></span>
                                    Generate Data Peserta Tes</button>
                                <button type="button" class="btn btn-sm btn-secondary" id="cetakDaftarHadirPeserta"><span
                                        class="ti ti-id"></span>
                                    Cetak Daftar Hadir Peserta</button>
                                <button type="button" class="btn btn-sm btn-warning" id="sinkronNilaiCBT"><span
                                        class="fas fa-sync-alt"></span>
                                    Sinkron Nilai Tes</button>
                                <button type="button" class="btn btn-sm btn-success" id="unduhDataPesertaTes"><span
                                        class="far fa-file-excel"></span>
                                    Unduh Data Peserta Tes</button>
                                <button type="button" class="btn btn-sm btn-danger" id="hapusDataPesertaTes"><span
                                        class="far fa-trash-alt"></span>
                                    Hapus Data Peserta Tes</button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle text-center" id="tablePendaftar">
                                    <thead class="bg-cyan-100">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">NIM</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Prodi</th>
                                            <th scope="col">Beasiswa</th>
                                            <th scope="col">Tanggal Ujian</th>
                                            <th scope="col">Sesi</th>
                                            <th scope="col">Ruang</th>
                                            <th scope="col">Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--[ Main Content ] end-->

            <!-- Modal -->
            <div class="modal fade" id="modalDataPendaftar" tabindex="-1" aria-labelledby="modalDataPendaftarLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalDataPendaftarLabel">Generate Data Peserta Tes</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Proses</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('script')
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>

    <script>
        function reloadData() {
            dataTable.ajax.reload(null, false);
        }
    </script>

    <script>
        const dataTable = $("#tablePendaftar").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.seleksi-tpa.data') }}",
                type: "POST",
                data: (data) => {
                    data._token = "{{ csrf_token() }}";
                    data.flt_tahun = $('#flt_tahun').val();
                    data.flt_beasiswa = $('#flt_beasiswa').val();
                    data.flt_tanggal_ujian = $('#flt_tanggal_ujian').val();
                    data.flt_sesi = $('#flt_sesi').val();
                    data.flt_ruang = $('#flt_ruang').val();
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'pendaftar.mahasiswa.nim',
                    name: 'mahasiswas.nim'
                },
                {
                    data: 'pendaftar.mahasiswa.nama',
                    name: 'mahasiswas.nama'
                },
                {
                    data: 'prodi',
                    name: 'mahasiswas.prodi'
                },
                {
                    data: 'beasiswa',
                    searchable: false
                },
                {
                    data: 'tanggal_ujian',
                    searchable: false
                },
                {
                    data: 'sesi'
                },
                {
                    data: 'ruang'
                },
                {
                    data: 'nilai'
                }
            ],
            "columnDefs": [{
                    "targets": "_all",
                    "className": "dt-head-center dt-body-center cell-border",
                    "visible": true
                },
                {
                    "targets": 0,
                    "width": "5%"
                },
                {
                    "targets": 5,
                    "width": "15%"
                },
                {
                    "targets": 6,
                    "width": "10%"
                },
            ],
            "responsive": true,
            "autoWidth": true,
            "fixedColumns": true,
            "fixedHeader": true,
            "language": {
                "url": 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json',
            },
        });
    </script>

    <script>
        $(document).on('click', '#generateDataPesertaTes', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val();

            let url = "{{ route('admin.seleksi-tpa.show', ['tahun' => ':tahun', 'beasiswa' => ':beasiswa']) }}";
            url = url.replace(':tahun', tahun).replace(':beasiswa', beasiswa);

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
                    $('#modalDataPendaftar .modal-body').html(res);

                    const tablePendaftarLolosAdministrasi = $("#tablePendaftarLolosAdministrasi")
                        .DataTable({
                            "columnDefs": [{
                                    "targets": "_all",
                                    "className": "dt-head-center dt-body-center cell-border",
                                    "visible": true
                                },
                                {
                                    "targets": 0,
                                    "width": "5%"
                                },
                            ],
                            "responsive": true,
                            "autoWidth": true,
                            "fixedColumns": true,
                            "fixedHeader": true,
                            "searching": false,
                            "language": {
                                "url": 'https://cdn.datatables.net/plug-ins/2.1.8/i18n/id.json',
                            },
                        });

                    $('#modalDataPendaftar').modal('show');
                    Swal.close();
                }
            });
        });
    </script>

    <script>
        $('#modalDataPendaftar button[type="submit"]').on('click', function(e) {
            e.preventDefault();
            startBatchGenerate();
        });

        function startBatchGenerate() {
            const count = parseInt($('#count-data-pendaftar').val(), 10),
                perBatch = 50,
                totalBatch = Math.ceil(count / perBatch),
                tahun = $('#tahun-kegiatan').val(),
                beasiswa = $('#beasiswa').val();

            Swal.fire({
                title: 'Konfirmasi',
                html: `Anda akan memproses <strong>${count}</strong> data dalam <strong>${totalBatch}</strong> batch`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    runBatch(tahun, beasiswa, count, perBatch, totalBatch);
                }
            });
        }

        function runBatch(tahun, beasiswa, totalData, perBatch, totalBatch) {
            let currentBatch = 0;

            const execute = () => {
                if (currentBatch >= totalBatch) {
                    $('.modal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Selesai!',
                        text: 'Semua data berhasil diproses.',
                        timer: 1500,
                        timerProgressBar: true
                    });
                    reloadData();
                    return;
                }

                const offset = currentBatch * perBatch;
                const limit = Math.min(perBatch, totalData - offset);

                showProgress(currentBatch + 1, totalBatch);

                $.ajax({
                    url: "{{ route('admin.seleksi-tpa.store') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tahun,
                        beasiswa,
                        start: offset,
                        per_process: limit
                    },
                    success: () => {
                        currentBatch++;
                        setTimeout(() => {
                            execute();
                        }, 1000); // jeda 300ms antar batch
                    },
                    error: () => {
                        retryBatch(currentBatch, execute);
                    }
                });
            };

            execute();
        }

        function showProgress(current, total) {
            Swal.fire({
                title: `Memproses batch ${current} dari ${total}`,
                html: `<progress value="${current}" max="${total}" style="width:100%"></progress>`,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        }

        function retryBatch(batchIndex, retryCallback) {
            Swal.fire({
                icon: 'error',
                title: `Batch ${batchIndex + 1} gagal`,
                text: 'Mencoba ulang dalam 2 detik...',
                showConfirmButton: false,
                timer: 2000
            }).then(() => {
                retryCallback();
            });
        }
    </script>

    <script>
        $('#flt_tahun, #flt_beasiswa').on('change', function() {
            $.ajax({
                url: "{{ route('admin.seleksi-tpa') }}",
                type: 'GET',
                data: {
                    flt_tahun: $('#flt_tahun').val(),
                    flt_beasiswa: $('#flt_beasiswa').val(),
                },
                beforeSend: () => {
                    Swal.fire({
                        title: 'Mengambil informasi ujian...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });
                },
                success: (res) => {
                    $('#flt_tanggal_ujian').empty();
                    res.tanggal_ujian.forEach(opt => {
                        const date = new Date(opt);
                        const day = String(date.getDate()).padStart(2, '0');
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const year = date.getFullYear();

                        const formatted = `${day}-${month}-${year}`;

                        $('#flt_tanggal_ujian').append($('<option>', {
                            value: opt,
                            text: formatted
                        }));
                    });

                    $('#flt_sesi').empty();
                    res.sesi.forEach(opt => {
                        $('#flt_sesi').append($('<option>', {
                            value: opt,
                            text: opt
                        }));
                    });

                    $('#flt_ruang').empty();
                    res.ruang.forEach(opt => {
                        $('#flt_ruang').append($('<option>', {
                            value: opt,
                            text: opt
                        }));
                    });
                    Swal.close();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON.message ??
                            'Terdapat kesalahan server, periksa lebih lanjut!',
                    });
                }
            });
        });

        $(document).on('click', '#cetakDaftarHadirPeserta', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val(),
                tanggal_ujian = $('#flt_tanggal_ujian').val(),
                sesi = $('#flt_sesi').val(),
                ruang = $('#flt_ruang').val();

            $.ajax({
                url: "{{ route('admin.seleksi-tpa.daftar-hadir') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa,
                    tanggal_ujian: tanggal_ujian,
                    sesi: sesi,
                    ruang: ruang
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
                        'Daftar hadir peserta TPA.pdf');

                    var blob = new Blob([response]);
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
    </script>

    <script>
        $(document).on('click', '#unduhDataPesertaTes', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val();

            $.ajax({
                url: "{{ route('admin.seleksi-tpa.unduh') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa
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
                        'Data peserta TPA.xlsx');

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
    </script>

    <script>
        $(document).on('click', '#sinkronNilaiCBT', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val();

            let url =
                "{{ route('admin.seleksi-tpa.sinkron-nilai', ['tahun' => ':tahun', 'beasiswa' => ':beasiswa']) }}";
            url = url.replace(':tahun', tahun).replace(':beasiswa', beasiswa);

            Swal.fire({
                title: 'Sinkron Nilai Sekarang?',
                html: `Pastikan semua peserta di setiap sesi sudah selesai melaksanakan ujian CBT`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjut!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        beforeSend: () => {
                            Swal.fire({
                                title: 'Memproses data...',
                                showCancelButton: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                                allowOutsideClick: false
                            });
                        },
                        success: (res) => {
                            reloadData();
                            Swal.fire({
                                icon: res.icon,
                                title: res.title,
                                text: res.message,
                                timer: 1500,
                                timerProgressBar: true
                            });
                        },
                        error: function(error) {
                            const msg = JSON.parse(error.responseText);
                            Swal.fire({
                                title: 'Gagal',
                                text: error && error.status !== 200 ?
                                    (typeof msg === 'string' ? msg : msg.message) :
                                    'Tidak dapat melakukan sinkron nilai dari aplikasi CBT. Terjadi kesalahan atau data tidak tersedia',
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
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '#hapusDataPesertaTes', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val();

            Swal.fire({
                title: 'Apa Anda Yakin?',
                html: `Anda akan menghapus semua data peserta yang sudah generate kartu dan terdaftar di aplikasi CBT`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.seleksi-tpa.destroy') }}",
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}",
                            tahun: tahun,
                            beasiswa: beasiswa
                        },
                        beforeSend: () => {
                            Swal.fire({
                                title: 'Memproses data...',
                                showCancelButton: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                },
                                allowOutsideClick: false
                            });
                        },
                        success: (res) => {
                            reloadData();
                            Swal.fire({
                                icon: res.icon,
                                title: res.title,
                                text: res.message,
                                timer: 1500,
                                timerProgressBar: true
                            });
                        },
                        error: function(error) {
                            const msg = JSON.parse(error.responseText);
                            Swal.fire({
                                title: 'Gagal',
                                text: error && error.status !== 200 ?
                                    (typeof msg === 'string' ? msg : msg.message) :
                                    'Tidak dapat menghapus data peserta tes. Terjadi kesalahan atau data tidak tersedia',
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
                }
            });
        });
    </script>
@endpush
