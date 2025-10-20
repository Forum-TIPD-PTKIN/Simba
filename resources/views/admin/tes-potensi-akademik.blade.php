@extends('admin.template.master-template')

@section('title', 'Tes Potensi Akademik')

@push('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.min.css">
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
                            <h5 class="mb-3 mb-sm-0">Data Peserta Lolos Seleksi Administrasi</h5>
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
                            <div class="btn-group mb-3" role="group" aria-label="Button Generate Peserta CBT">
                                <button type="button" class="btn btn-sm btn-primary" id="generateDataPesertaCBT"><span
                                        class="ti ti-file-import"></span>
                                    Generate Data Peserta CBT</button>
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
                                            <th scope="col">Aksi</th>
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
                }
            },
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1
                },
                {
                    data: 'nim_pendaftar',
                    name: 'mahasiswas.nim'
                },
                {
                    data: 'nama_pendaftar',
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
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
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
        function getData(id) {
            let url = "{{ route('admin.jadwal-kegiatan.edit', ':id') }}";
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

        async function editData(id) {
            const response = await getData(id),
                tanggal_mulai = new Date(response.tanggal_mulai),
                tanggal_selesai = new Date(response.tanggal_selesai);

            $('#jadwal_id').val(response.encrypted_id);
            $(`#beasiswa option[value="${response.beasiswa_id}"]`).prop('selected', true);
            $(`#role option[value="${response.role}"]`).prop('selected', true);
            $('#nama').val(response.nama);
            $('#tanggal_mulai').data('daterangepicker').setStartDate(tanggal_mulai);
            $('#tanggal_mulai').data('daterangepicker').setEndDate(tanggal_mulai);
            $('#tanggal_selesai').data('daterangepicker').setStartDate(tanggal_selesai);
            $('#tanggal_selesai').data('daterangepicker').setEndDate(tanggal_selesai);
            $('#tanggal_mulai').val(moment(tanggal_mulai).format('D/M/YYYY HH:mm'));
            $('#tanggal_selesai').val(moment(tanggal_selesai).format('D/M/YYYY HH:mm'));
            if (response.deskripsi) {
                tinymce.activeEditor.setProgressState(true)
                tinymce.activeEditor.setProgressState(false, 1000)
                setTimeout(() => {
                    tinymce.activeEditor.setContent(response.deskripsi);
                    tinymce.triggerSave();
                }, 500);
            }
            $('#modalJadwal').modal('show');
        }

        async function deleteData(id) {
            const response = await getData(id);

            Swal.fire({
                title: 'Apa Anda Yakin?',
                html: `Anda akan menghapus data jadwal : <span class="fw-bold fst-italic">"${response.nama} (${response.beasiswa?.nama} - ${response.tahun_kegiatan?.tahun})"</span>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    url = "{{ route('admin.jadwal-kegiatan.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    Swal.fire({
                        title: 'Sedang memproses...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                url: url,
                                type: "DELETE",
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function(res) {
                                    dataTable.ajax.reload(null, false);

                                    Swal.fire({
                                        title: res.title,
                                        text: res.message,
                                        icon: res.icon,
                                        timer: 2000,
                                        timerProgressBar: true,
                                    });
                                },
                                error: function(res) {
                                    Swal.fire({
                                        title: 'Gagal',
                                        icon: 'error',
                                        text: res.responseJSON.message ??
                                            'Ada kesalahan'
                                    });
                                },
                                complete: () => {
                                    $("form.needs-validation").trigger('reset');
                                }
                            });
                        },
                        allowOutsideClick: false
                    });
                }
            });
        }
    </script>
@endpush
