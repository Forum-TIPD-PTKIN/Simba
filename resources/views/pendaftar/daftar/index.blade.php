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
                                <li class="breadcrumb-item" aria-current="page">{{ $beasiswa->nama }}</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Pendaftaran Beasiswa {{ $beasiswa->nama }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- konten --}}

            <!-- [ Main Content ] end -->
            <div class="card shadow-sm">
                <div class="card-body">

                    @include('pendaftar.daftar.wizard-content.control-top')

                    <!-- Progress bar -->
                    <div class="progress mb-4" style="height:6px;">
                        <div class="progress-bar" style="width:{{ (100 / 3) * $step }}%;"></div>
                    </div>

                    @if ($readOnly)
                        <div class="alert alert-warning">
                            <strong>Perhatian</strong> Tidak dapat mengubah data pendaftaran ini, dikarenakan tahun kegiatan
                            telah tidak aktif
                        </div>
                    @endif

                    @include('pendaftar.daftar.wizard-content.' . $step)

                    @include('pendaftar.daftar.wizard-content.control-bottom')

                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <style>
        .wizard-step {
            text-align: center;
            width: 28%;
        }

        .wizard-step .step-circle {
            width: 4.4rem;
            height: 4.4rem;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bolder;
            font-size: 30px;
        }

        .wizard-step.active .step-circle {
            background-color: var(--bs-primary);
            color: #fff;
        }

        .wizard-step.completed .step-circle {
            background-color: var(--bs-success);
            color: #fff;
        }

        .wizard-step.pending .step-circle {
            background-color: var(--bs-light);
            border: 1px solid var(--bs-secondary);
            color: var(--bs-secondary);
        }
    </style>
@endpush

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
