@extends('pendaftar.template.master-template')

@section('title', 'Detail Beasiswa')

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
                                <li class="breadcrumb-item"><a href="{{ route('pendaftar.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0);">Beasiswa</a></li>
                                <li class="breadcrumb-item" aria-current="page">{{ $beasiswa->nama }}</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">{{ $beasiswa->nama }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Deskripsi</h5>
                        </div>
                        <div class="card-body">
                            {!! $beasiswa->deskripsi !!}
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Jadwal Kegiatan</h5>
                        </div>
                        <div class="card-body border-bottom p-0">
                            <ul class="list-group list-group-flush">
                                @foreach ($beasiswa->jadwal_kegiatan ?: [] as $item)
                                    <li class="list-group-item ps-0">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1 ms-3">
                                                <div class="row g-1 align-items-center">
                                                    <div class="col-6">
                                                        <h6 class="mb-0">{{ $item->nama }}</h6>
                                                    </div>
                                                    <div class="col-6 text-end fw-bold">
                                                        <p class="text-success mb-0 small">{{ $item->tgl_mulai_formatted }}
                                                        </p>
                                                        <p class="text-danger mb-0 small">{{ $item->tgl_selesai_formatted }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-footer">
                            <div class="row g-2 justify-content-center">
                                <div class="col-md-6">
                                    <div class="d-grid">
                                        <a class="btn btn-primary d-grid"
                                            href="{{ route('pendaftar.daftar', [$beasiswa->id]) }}"><span
                                                class="text-truncate w-100">Daftar
                                                Beasiswa</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('script')
@endpush
