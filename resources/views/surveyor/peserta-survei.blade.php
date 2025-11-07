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
                                <button type="button" class="btn btn-sm btn-warning" id="unduhInstrumenSurvei"><span
                                        class="far fa-file-pdf"></span>
                                    Unduh Instrumen Survei</button>
                            </div>
                            {!! $view_daftar_responden !!}
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

        $(document).on('click', '#unduhInstrumenSurvei', function() {
            const tahun = $('#flt_tahun').val(),
                beasiswa = $('#flt_beasiswa').val();

            $.ajax({
                url: "{{ route('surveyor.cetak.instrumen-survei') }}",
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
@endpush
