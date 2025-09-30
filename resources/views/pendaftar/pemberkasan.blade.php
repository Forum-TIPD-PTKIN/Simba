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
                        <div class="d-flex gap-2 justify-content-end align-items-center mb-3">
                            <div style="max-width: 400px; width:100%">
                                <label for="filterForm" class="form-label mb-0">Filter Beasiswa</label>
                                <select class="form-select" id="filterForm" aria-label="Filter Form"
                                    onchange="filterBeasiswa(this)">
                                    @foreach ($filter_beasiswa as $item)
                                        <option value="{{ $item->id }}" @selected(request()->get('beasiswa') == $item->id)>
                                            {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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

    <script>
        function filterBeasiswa(selectElement) {
            const beasiswaId = selectElement.value;
            const url = `{{ route('pendaftar.pemberkasan') }}?beasiswa=${beasiswaId}`;
            window.location.href = url;
        }
    </script>
@endpush
