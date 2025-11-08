@extends('surveyor.template.master-template')

@section('title', 'Peserta Survei')

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
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Surveyor</a></li>
                                <li class="breadcrumb-item" aria-current="page">Peserta Survei</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Peserta Survei</h2>
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
                            <h5 class="mb-3 mb-sm-0">Daftar Peserta Survei</h5>
                            <div class="d-flex gap-1 form-filter">
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
                                <button class="btn btn-sm btn-primary btnFilter">Filter</button>
                            </div>
                        </div>

                        <div class="card-body" id="daftar-responden">
                            <div class="btn-group mb-4" role="group" aria-label="Button Cetak">
                                <button type="button" class="btn btn-sm btn-danger" id="unduhDaftarPesertaSurvei"><span
                                        class="far fa-file-pdf"></span>
                                    Unduh Daftar Peserta</button>
                            </div>
                            {!! $view_daftar_responden !!}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="modalBerkasPendaftar" data-bs-backdrop="static" tabindex="-1"
                aria-labelledby="modalBerkasPendaftarLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="modalBerkasPendaftarLabel">Berkas Pendaftar</h1>
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

@push('script')
    <script>
        $(document).on('click', '.btnFilter', function() {
            const container = $(this).closest('div.form-filter'),
                tahun = container.find('#flt_tahun').val(),
                beasiswa = container.find('#flt_beasiswa').val();

            $.ajax({
                url: "{{ route('surveyor.peserta-survei') }}",
                type: 'GET',
                data: {
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
                    let target;
                    target = $('#daftar-responden');
                    target.children().remove();
                    target.html(res);

                    Swal.close();
                }
            });
        });
    </script>

    <script>
        $(document).on('click', '#unduhDaftarPesertaSurvei', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val();

            $.ajax({
                url: "{{ route('surveyor.cetak.peserta-survei') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa,
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
                        'Daftar peserta survei.pdf');

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

        $(document).on('click', '.unduhInstrumenSurvei', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val(),
                pendaftar_id = $(this).data('pendaftar-id');

            $.ajax({
                url: "{{ route('surveyor.cetak.instrumen-survei') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    tahun: tahun,
                    beasiswa: beasiswa,
                    pendaftar_id: pendaftar_id,
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
                        'Instrumen survei.pdf');

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
        function lihatBerkasPendaftar(id) {
            let url = "{{ route('surveyor.berkas-pendaftar', ':id') }}";
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
                    $('#modalBerkasPendaftar .modal-body').html(res);

                    $('#modalBerkasPendaftar').modal('show');
                    Swal.close();
                }
            });
        }

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
@endpush
