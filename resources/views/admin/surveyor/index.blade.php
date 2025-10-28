@extends('admin.template.master-template')

@section('title', 'Tahun Kegiatan')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="pc-container" id="app-surveyor">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Surveyor</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Surveyor @{{ titleKip }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAssignSurveyor">
                        <i class="ti ti-user-plus"></i> Assign Surveyors
                    </button>
                </div>
                <div class="d-flex gap-1">
                    <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan" id="flt_tahun">
                        @foreach ($master_tahun as $item)
                            <option value="{{ $item->id }}" @selected($loop->first)>{{ $item->tahun }}
                            </option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" aria-label="Filter beasiswa" id="flt_beasiswa">
                        @foreach ($master_beasiswa as $item)
                            <option value="{{ $item->id }}" @selected($loop->first)>{{ $item->nama }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                </div>
            </div>

            <!--[ Main Content ] start-->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Dosen & Pegawai</h5>
                        </div>
                        <div class="card-body">
                            <input class="form-control" id="alamat_keyword_surveyor" autofocus>
                            <div class="d-flex gap-1 flex-wrap my-2">
                                <dib class="badge text-bg-warning">@{{ surveyorCalon.length }} Calon</dib>
                                <dib class="badge text-bg-danger">@{{ surveyorTidakBersedia.length }} Tidak Bersedia</dib>
                                <dib class="badge text-bg-success">@{{ surveyorBersedia.length }} Bersedia</dib>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Mahasiswa</h5>
                        </div>
                        <div class="card-body">
                            <input class="form-control" id="alamat_keyword_mahasiswa" autofocus>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Assign Surveyor -->
            <div class="modal fade" id="modalAssignSurveyor" aria-labelledby="modalAssignSurveyorLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAssignSurveyorLabel">Pilih Calon Surveyor
                                @{{ titleKip }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="sticky-top bg-white py-2 mb-3"
                                style="top: -1rem; /* Sesuaikan dengan padding modal-body */">
                                <input type="text" class="form-control" v-model="searchQuery"
                                    placeholder="Cari nama pegawai...">
                            </div>
                            <ul class="list-group">
                                <li v-for="pegawai in filteredPegawai" :key="pegawai.id"
                                    @click="toggleSelection(pegawai.id)" style="cursor: pointer;"
                                    class="list-group-item d-flex justify-content-between align-items-center list-group-item-action"
                                    :class="{ 'active': selectedPegawai.includes(pegawai.id) }">
                                    <div>
                                        @{{ pegawai.nama }}
                                        <div class="small">@{{ pegawai.nip }}</div>
                                    </div>
                                    <input class="form-check-input" type="checkbox" :value="pegawai.id"
                                        v-model="selectedPegawai">
                                </li>
                                <li v-if="filteredPegawai.length === 0" class="list-group-item text-center">
                                    Data tidak ditemukan.
                                </li>
                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                                :disabled="isLoading">Batal</button>
                            <button type="button" class="btn btn-primary" @click="saveSurveyors" :disabled="isLoading">
                                <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                                <span v-if="isLoading">Menyimpan...</span>
                                <span v-else>Simpan</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('head')
    <style>
        .modal-body .list-group {
            max-height: 66vh;
            overflow-y: auto;
        }
    </style>
    <link href="{{ asset('assets/plugins/jquery-tags/dist/tagify.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="{{ asset('assets/plugins/jquery-tags/dist/tagify.js') }}"></script>
    <script src="{{ asset('assets/plugins/jquery-tags/dist/tagify.polyfills.min.js') }}"></script>
    <script>
        new Tagify(document.getElementById('alamat_keyword_surveyor'), {
            placeholder: 'alamat surveyor...'
        });
        new Tagify(document.getElementById('alamat_keyword_mahasiswa'), {
            placeholder: 'alamat mahasiswa...'
        });

        new Vue({
            el: '#app-surveyor',
            data: {
                titleKip: '{{ $kip_select->nama }} {{ $tahun_select->tahun }}',
                searchQuery: '',
                selectedPegawai: [],
                pegawaiList: @json($master_pegawai),
                isLoading: false,
                currentTahunKegiatanId: '{{ $tahun_select->id }}',
                currentBeasiswaId: '{{ $kip_select->id }}',
                surveyorList: @json($surveyor)
            },
            computed: {
                filteredPegawai() {
                    return this.pegawaiList.filter(pegawai => {
                        return pegawai.nama.toLowerCase().includes(this.searchQuery.toLowerCase());
                    });
                },
                surveyorCalon() {
                    return this.surveyorList.filter(surveyor => {
                        return surveyor.bersedia === null;
                    });
                },
                surveyorBersedia() {
                    return this.surveyorList.filter(surveyor => {
                        return surveyor.bersedia === 1;
                    });
                },
                surveyorTidakBersedia() {
                    return this.surveyorList.filter(surveyor => {
                        return surveyor.bersedia === 0;
                    });
                }
            },
            methods: {
                toggleSelection(pegawaiId) {
                    const index = this.selectedPegawai.indexOf(pegawaiId);
                    if (index > -1) {
                        this.selectedPegawai.splice(index, 1); // Hapus jika sudah ada
                    } else {
                        this.selectedPegawai.push(pegawaiId); // Tambah jika belum ada
                    }
                },
                saveSurveyors() {
                    if (this.selectedPegawai.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Pilih setidaknya satu surveyor.',
                        });
                        return;
                    }

                    this.isLoading = true;
                    Swal.fire({
                        title: 'Menyimpan data...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        },
                        allowOutsideClick: false
                    });

                    $.ajax({
                        url: '{{ route('admin.surveyor.assign') }}', // Anda perlu membuat route ini di Laravel
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            selected_surveyors: this.selectedPegawai,
                            tahun_kegiatan_id: this.currentTahunKegiatanId,
                            beasiswa_id: this.currentBeasiswaId
                        },
                        success: (response) => {
                            Swal.fire({
                                icon: response.icon,
                                title: response.title,
                                text: response.message,
                                timer: 1500,
                                timerProgressBar: true,
                            });
                            $('#modalAssignSurveyor').modal('hide');
                            this.selectedPegawai = [];
                            // reload table
                        },
                        error: (xhr) => {
                            const msg = xhr.responseJSON ? xhr.responseJSON.message :
                                'Terjadi kesalahan saat menyimpan data.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: msg,
                            });
                        },
                        complete: () => {
                            this.isLoading = false;
                        }
                    });
                }
            }
        });
    </script>
@endpush
