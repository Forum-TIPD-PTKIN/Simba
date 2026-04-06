@extends('admin.template.master-template')

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
                            <div class="page-header-title">
                                <h2 class="mb-0">Dashboard</h2>
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
                            <ul class="nav nav-tabs analytics-tab" id="rekapTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="status-pendaftar-tab" data-bs-toggle="tab"
                                        data-bs-target="#status-pendaftar-tab-pane" type="button" role="tab"
                                        aria-controls="status-pendaftar-tab-pane" aria-selected="true">Status
                                        Pendaftar</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="prodi-pendaftar-tab" data-bs-toggle="tab"
                                        data-bs-target="#prodi-pendaftar-tab-pane" type="button" role="tab"
                                        aria-controls="prodi-pendaftar-tab-pane" aria-selected="false"
                                        tabindex="-1">Program Studi Pendaftar</button>
                                </li>
                            </ul>
                            <div class="d-flex gap-1 form-filter">
                                <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan"
                                    id="flt_tahun">
                                    @foreach ($tahun_kegiatan as $item)
                                        <option value="{{ $item->id }}" @selected($loop->first)>
                                            {{ $item->tahun }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-select form-select-sm" aria-label="Filter beasiswa" id="flt_beasiswa">
                                    @foreach ($beasiswa as $item)
                                        <option value="{{ $item->id }}" @selected($loop->first)>
                                            {{ $item->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary btnFilter">Filter</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12" id="tabContent">
                                    {!! $view_rekap !!}
                                </div>
                            </div>
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

            let url = "{{ route('admin.dashboard.show', ['tahun' => ':tahun', 'beasiswa' => ':beasiswa']) }}"
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
                    let target = $('#tabContent');
                    target.children().remove();
                    target.html(res);

                    Swal.close();
                }
            });
        });
    </script>
@endpush
