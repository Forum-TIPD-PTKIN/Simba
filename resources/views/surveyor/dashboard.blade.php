@extends('surveyor.template.master-template')

@section('title', 'Dashboard')

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
                                <li class="breadcrumb-item" aria-current="page">Home</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Home</h2>
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
                            <h5 class="mb-3 mb-sm-0">Rekapitulasi Responden</h5>
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

            let url = "{{ route('surveyor.dashboard.show', ['tahun' => ':tahun', 'beasiswa' => ':beasiswa']) }}"
                .replace(':tahun', tahun)
                .replace(':beasiswa', beasiswa);

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
                    let target;
                    target = $('#daftar-responden');
                    target.children().remove();
                    target.html(res);

                    Swal.close();
                }
            });
        });
    </script>
@endpush
