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
                                <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Riwayat Pendaftaran</a></li>
                                <li class="breadcrumb-item" aria-current="page">Not Allowed</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Not Allowed</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="alert text-{{ $color ?? 'white' }} bg-{{ $bg ?? 'success' }}">
                        <h5 class="text-{{ $color ?? 'white' }}"><i class="ti ti-info-circle"></i> {{ $title }}</h5>
                        <p>
                            {{ $message }}</p>
                        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-light">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection
