@extends('pendaftar.template.master-template')

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
                @if (count($beasiswa))
                    @foreach ($beasiswa as $item)
                        <div class="col-lg-3">
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
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.btnDetail').on('click', function() {
                const id = $(this).data('id');
                let url = "{{ route('pendaftar.detail-beasiswa', ':id') }}";
                url = url.replace(':id', id);
                window.location.href = url;
            });
        });
    </script>
@endpush
