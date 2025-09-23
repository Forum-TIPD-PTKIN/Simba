@extends('pendaftar.template.master-template')

@section('title', 'Dashboard')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ Main Content ] start -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-7 col-lg-5 col-xl-4">
                    <div class="card text-{{ $color ?? 'white' }} bg-{{ $bg ?? 'success' }}">
                        <div class="card-header">{{ $title }}</div>
                        <div class="card-body">
                            <h5 class="card-title text-{{ $color ?? 'white' }}">{{ $subtitle ?? '' }}</h5>
                            <p class="card-text">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection
