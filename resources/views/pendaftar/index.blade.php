@extends('pendaftar.template.master-template')

@section('title', 'Dashboard')

@push('head')
    <style>
        .loader {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px;
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
                        <div class="card-body border-bottom pb-0">
                            <div class="d-flex align-items-center justify-content-between">
                                <h5 class="mb-0">Daftar Beasiswa</h5>
                            </div>
                            <ul class="nav nav-tabs analytics-tab" id="beasiswaTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="beasiswa-tab-1" data-bs-toggle="tab"
                                        data-bs-target="#beasiswa-tab-1-pane" type="button" role="tab"
                                        aria-controls="beasiswa-tab-1-pane" aria-selected="false">Semua</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="beasiswa-tab-2" data-bs-toggle="tab"
                                        data-bs-target="#beasiswa-tab-2-pane" type="button" role="tab"
                                        aria-controls="beasiswa-tab-2-pane" aria-selected="true">Buka</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="beasiswa-tab-3" data-bs-toggle="tab"
                                        data-bs-target="#beasiswa-tab-3-pane" type="button" role="tab"
                                        aria-controls="beasiswa-tab-3-pane" aria-selected="false">Tutup</button>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content" id="beasiswaTabContent">
                            <div class="tab-pane fade" id="beasiswa-tab-1-pane" role="tabpanel"
                                aria-labelledby="beasiswa-tab-1">
                                <div class="loader">Loading...</div>
                            </div>
                            <div class="tab-pane fade show active" id="beasiswa-tab-2-pane" role="tabpanel"
                                aria-labelledby="beasiswa-tab-2">
                                <div class="loader">Loading...</div>
                            </div>
                            <div class="tab-pane fade" id="beasiswa-tab-3-pane" role="tabpanel"
                                aria-labelledby="beasiswa-tab-3">
                                <div class="loader">Loading...</div>
                            </div>
                        </div>
                        <div class="card-footer">
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="row @if (count($beasiswa) < 4) justify-content-center @endif">
                @if (count($beasiswa))
                    @foreach ($beasiswa as $item)
                        <div class="col-lg-{{ count($beasiswa) < 4 ? 12 / count($beasiswa) : 3 }}">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">{{ $item->nama }}</h5>
                                </div>
                                <div class="card-body">
                                    {!! Str::words(strip_tags($item->deskripsi), 25, '...') !!}
                                    <div class="d-grid mt-3">
                                        <button
                                            class="btn btn-primary d-flex align-items-center justify-content-center btnDetail"
                                            data-id="{{ $item->id }}"><i class="ti ti-eye"></i> Lebih Detail</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <h4><i class="ti ti-alert-triangle"></i> Perhatian!</h4>
                            <p class="mb-0">Saat ini tidak ada pendaftaran beasiswa yang dibuka</p>
                        </div>
                    </div>
                @endif
            </div> --}}
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            // Saat tab ditampilkan
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                let target = $(e.target).data("bs-target"); // ex: #beasiswa-tab-2-pane

                // Cek kalau tab-pane masih kosong, baru load via AJAX
                if ($(target).is(':empty') || $(target).find('.loader').length > 0) {
                    $(target).html('<div class="loader">Loading...</div>');

                    // Contoh URL berbeda per tab
                    let urlTemplate = "{{ route('pendaftar.beasiswa.status', ['status' => ':status']) }}";
                    let urlMap = {
                        "#beasiswa-tab-1-pane": urlTemplate.replace(':status', 'all'),
                        "#beasiswa-tab-2-pane": urlTemplate.replace(':status', 'open'),
                        "#beasiswa-tab-3-pane": urlTemplate.replace(':status', 'close'),
                    };

                    $.get(urlMap[target], function(data) {
                        $(target).html(data); // isi tab-pane dengan response
                    });
                }
            });

            // Trigger load pertama kali untuk tab yang aktif default
            $('#beasiswaTab button.nav-link.active').trigger('shown.bs.tab');
        });

        $(document).on('click', '.btnDetail', function() {
            const id = $(this).data('id');
            let url = "{{ route('pendaftar.detail-beasiswa', ':id') }}";
            url = url.replace(':id', id);
            window.location.href = url;
        });
    </script>
@endpush
