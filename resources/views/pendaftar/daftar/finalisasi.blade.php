@extends('pendaftar.template.master-template')

@section('title', 'Pendaftaran')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('pendaftar.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Pendaftaran</li>
                                <li class="breadcrumb-item" aria-current="page">{{ $pendaftar?->beasiswa->nama }}</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Pendaftaran Beasiswa {{ $pendaftar?->beasiswa->nama }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- konten --}}

            <!-- [ Main Content ] end -->
            <div class="card shadow-sm">
                <div class="card-body">
                    -
                </div>
            </div>
        </div>
    </div>
@endsection
