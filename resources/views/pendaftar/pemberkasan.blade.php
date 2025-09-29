@extends('pendaftar.template.master-template')

@section('title', 'Pemberkasan')

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
                                <li class="breadcrumb-item" aria-current="page">Pemberkasan</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Pemberkasan Beasiswa {{ $data->beasiswa->nama }}
                                    {{ $data->tahun_kegiatan->tahun }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="card">
                    <div class="card-body">
                        @include('pendaftar.daftar.wizard-content.3', [
                            $generated_form,
                            'nowizard' => true,
                        ])
                        <div class="d-flex justify-content-end">
                            <button onclick="simpanFile()" class="btn btn-primary">Simpan File</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        function simpanFileManual() {
            $('#form-berkas').submit()
        }
    </script>
@endpush
