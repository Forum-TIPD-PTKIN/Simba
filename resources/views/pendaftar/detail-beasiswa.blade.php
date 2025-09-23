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
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">{{ $beasiswa->nama }}</h5>
                        </div>
                        <div class="card-body">
                            {!! $beasiswa->deskripsi !!}
                            <div class="d-grid mt-3">
                                <button class="btn btn-primary d-flex align-items-center justify-content-center btnDetail"
                                    data-id="{{ $beasiswa->id }}"><i class="ti ti-eye"></i> Lebih Detail</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Project - Able Pro</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <p class="mb-2">Release v1.2.0<span class="float-end">70%</span></p>
                                <div class="progress progress-primary" style="height: 8px">
                                    <div class="progress-bar" style="width: 70%"></div>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="#" class="btn btn-link-secondary">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-warning rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 mx-2">
                                            <p class="mb-0 d-grid text-start">
                                                <span class="text-truncate w-100">Horizontal Layout</span>
                                            </p>
                                        </div>
                                        <div class="badge bg-light-secondary f-12"><i class="ti ti-paperclip text-sm"></i>
                                            2</div>
                                    </div>
                                </a>
                                <a href="#" class="btn btn-link-secondary">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-warning rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 mx-2">
                                            <p class="mb-0 d-grid text-start">
                                                <span class="text-truncate w-100">Invoice Generator</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="btn btn-link-secondary">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-warning rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 mx-2">
                                            <p class="mb-0 d-grid text-start">
                                                <span class="text-truncate w-100">Package Upgrades</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <a href="#" class="btn btn-link-secondary">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <span class="p-1 d-block bg-success rounded-circle">
                                                <span class="visually-hidden">New alerts</span>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1 mx-2">
                                            <p class="mb-0 d-grid text-start">
                                                <span class="text-truncate w-100">Figma Auto Layout</span>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="d-grid mt-3">
                                <button class="btn btn-primary d-flex align-items-center justify-content-center"><i
                                        class="ti ti-plus"></i> Add task</button>
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

@section('script')
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
@endsection
