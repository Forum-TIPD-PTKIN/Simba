@extends('admin.template.master-template')

@section('title', 'Tahun Kegiatan')

@section('content')
    <div class="pc-container" style="display: none" id="app-surveyor">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Surveyor
                                </li>
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

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAssignSurveyor">
                        <i class="ti ti-user-plus"></i> Assign Surveyors
                    </button>
                </div>
                <div class="d-flex gap-1">
                    <select class="form-select form-select-sm" aria-label="Filter tahun kegiatan" id="flt_tahun">
                        @foreach ($master_tahun as $item)
                            <option value="{{ $item->id }}" @selected($item->id == $tahun_select->id)>{{ $item->tahun }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm" aria-label="Filter beasiswa" id="flt_beasiswa">
                        @foreach ($master_beasiswa as $item)
                            <option value="{{ $item->id }}" @selected($item->id == $kip_select->id)>{{ $item->nama }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-primary" onclick="reloadData()">Filter</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>
                                <div>Dosen & Pegawai</div>
                                <span v-if="activeSurveyorId" class="badge text-bg-info">Surveyor Aktif:
                                    @{{ activeSurveyorName }}</span>
                            </h5>
                        </div>
                        <div class="card-body">
                            <input class="form-control" ref="alamat_keyword_surveyor" autofocus>
                            <div class="d-flex gap-1 flex-wrap my-2">
                                <div class="badge text-bg-warning">@{{ surveyorCalon.length }}
                                    Calon
                                </div>
                                <div class="badge text-bg-danger">@{{ surveyorTidakBersedia.length }}
                                    Tidak Bersedia</div>
                                <div class="badge text-bg-success">@{{ surveyorBersedia.length }}
                                    Bersedia</div>
                            </div>
                            <div class="small text-muted">Klik nama surveyor untuk mengaktifkan
                                mode plotting.
                            </div>
                            <div class="list-group list-group-flush mt-3 surveyor-list-container">
                                <div v-if="surveyorBersediaFilter.length === 0" class="text-center text-muted p-3">
                                    Tidak ada data surveyor yang cocok.
                                </div>
                                <a v-for="(surveyor, index) in surveyorBersediaFilter" :key="surveyor.id"
                                    @click="toggleActiveSurveyor(surveyor.id, surveyor.user.name)"
                                    href="javascript:void(0);" class="list-group-item list-group-item-action"
                                    :class="{ 'active-surveyor': surveyor.id === activeSurveyorId }">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0"><i class="ti ti-user-circle ti-lg"></i></div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">
                                                @{{ index + 1 }}. @{{ surveyor.user.name }}</h6>
                                            <small class="text-muted">@{{ surveyor.alamat }}</small>
                                            <div class="list-detail d-flex gap-1 flex-wrap flex-column mt-2">
                                                <div class="data-pendaftar-in-detail list-group-item list-group-item-action"
                                                    v-for="(detail, index2) in surveyor.surveyor_detail"
                                                    :key="detail.id" style="cursor: default;">

                                                    <div class="d-flex flex-column">
                                                        <div class="">
                                                            @{{ index2 + 1 }}. @{{ detail.pendaftar ? detail.pendaftar.mahasiswa.nama : detail.mahasiswa.nama }}
                                                        </div>
                                                        <div class="alamat text-muted small">
                                                            @{{ detail.pendaftar ? detail.pendaftar.biodata_pendaftar.data.biodata.alamat_ktp.value : detail.mahasiswa.alamat }}
                                                        </div>
                                                    </div>
                                                    <div @click.stop="removeMahasiswa(surveyor, detail)"
                                                        class="text-danger float-end" title="Hapus"
                                                        style="cursor: pointer;">
                                                        <i class="ti ti-trash"></i>
                                                    </div>
                                                </div>
                                                <div class="text-muted fst-italic py-2 text-center"
                                                    v-if="surveyor.surveyor_detail.length===0">
                                                    Belum ada mahasiswa
                                                    diplotting
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Mahasiswa Belum di Plotting</h5>
                        </div>
                        <div class="card-body">
                            <input class="form-control" ref="alamat_keyword_mahasiswa" autofocus>
                            <div class="d-flex gap-1 flex-wrap my-2">
                                <div class="badge text-bg-danger">@{{ pendaftarList.length - countPlotted }}
                                    Belum Plotting</div>
                                <div class="badge text-bg-success">@{{ countPlotted }}
                                    Sudah Plotting</div>
                            </div>

                            <div class="list-group list-group-flush mt-3 surveyor-list-container">
                                <a v-for="(mhs, index) in pendaftarFilter" :key="mhs.id"
                                    @click="toggleSelectPendaftar(mhs.id)" href="javascript:void(0);"
                                    class="list-group-item list-group-item-action"
                                    :class="{ 'active': selectedPendaftar.includes(mhs.id), 'disabled-list': !activeSurveyorId }">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0"><i class="ti ti-user-circle ti-lg"></i></div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-0">
                                                @{{ index + 1 }}. @{{ mhs.mahasiswa.nama }}</h6>
                                            <small class="text-muted">@{{ mhs.biodata_pendaftar.data.biodata.alamat_ktp.value }}</small>
                                        </div>
                                    </div>
                                </a>
                                <div v-if="countPlotted === pendaftarList.length && pendaftarList.length !== 0"
                                    class="text-center text-muted p-3">
                                    Semua mahasiswa sudah diplotting.
                                </div>
                                <div v-if="pendaftarList.length === 0" class="text-center text-muted p-3">
                                    Tidak ada data pendaftar.
                                </div>
                            </div>

                            <div v-if="activeSurveyorId" class="my-3 text-center">
                                <button class="btn btn-sm btn-success" :disabled="selectedPendaftar.length === 0"
                                    @click="plotSelectedMahasiswa()">
                                    Plot @{{ selectedPendaftar.length }} Mahasiswa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Assign Surveyor -->
            <div class="modal fade" id="modalAssignSurveyor" aria-labelledby="modalAssignSurveyorLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAssignSurveyorLabel">Pilih Calon Surveyor
                                @{{ titleKip }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
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
                            <button type="button" class="btn btn-primary" @click="saveSurveyors"
                                :disabled="isLoading">
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

        .tagify__tag>div::before {
            background: #daeeff !important;
            box-shadow: unset !important;
        }

        .surveyor-list-container {
            max-height: 48vh;
            overflow-y: auto;
        }

        /* Hapus styling drag-and-drop */
        .list-group-item {
            cursor: pointer;
            /* Ubah cursor menjadi pointer */
        }

        .list-group-item-action {
            padding: 3px;
            background: #fdfdfd;
            border-bottom: 1px solid #c0c0c0 !important;
            padding-top: 7px;
        }

        /* New styles for active surveyor and selected pendaftar */
        .active-surveyor {
            background-color: #e9ecef !important;
            /* Warna abu-abu untuk surveyor aktif */
            border-left: 5px solid #0d6efd !important;
            /* Garis biru sebagai penanda aktif */
        }

        .list-group-item.active {
            background-color: #cfe2ff !important;
            /* Warna biru muda untuk mahasiswa yang dipilih */
            color: #000 !important;
        }

        .disabled-list {
            opacity: 0.6;
            pointer-events: none;
        }

        /* Hapus .ghost style */
        .list-detail .list-group-item-action {
            border: 1px solid #eee !important;
            margin-bottom: -4px;
        }
    </style>

    <link href="{{ asset('assets/plugins/jquery-tags/dist/tagify.css') }}" rel="stylesheet" type="text/css" />
@endpush

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    {{-- Hapus Sortable dan vuedraggable --}}

    <script src="{{ asset('assets/plugins/jquery-tags/dist/tagify.js') }}"></script>

    <script src="{{ asset('assets/plugins/jquery-tags/dist/tagify.polyfills.min.js') }}"></script>

    <script>
        // Hapus Vue.component('draggable', vuedraggable);
        new Vue({
            el: '#app-surveyor',
            data: {
                // disabledDragSurveyor: true, // Hapus
                titleKip: '{{ $kip_select->nama }} {{ $tahun_select->tahun }}',
                searchQuery: '',
                selectedPegawai: [],
                pegawaiList: @json($master_pegawai),
                pendaftarList: @json($pendaftar),
                isLoading: false,
                currentTahunKegiatanId: '{{ $tahun_select->id }}',
                currentBeasiswaId: '{{ $kip_select->id }}',
                surveyorList: @json($surveyor),
                alamatSurveyor: [],
                alamatMahasiswa: [],
                // Logika baru: Active Surveyor dan Selected Pendaftar
                activeSurveyorId: null,
                activeSurveyorName: null,
                selectedPendaftar: [],
            },
            created() {
                $('#app-surveyor').css('display', 'block');
            },
            mounted() {

                // Logika Tagify (Filter Keyword)
                const surveyorTagify = new Tagify(this.$refs.alamat_keyword_surveyor, {
                    placeholder: 'nama/alamat pisah dengan tanda ,'
                });
                const mahasiswaTagify = new Tagify(this.$refs.alamat_keyword_mahasiswa, {
                    placeholder: 'nama/alamat/NIM pisah dengan tanda ,'
                });

                surveyorTagify.on('change', (e) => {
                    if (!e.detail.value) {
                        this.alamatSurveyor = [];
                        return;
                    }
                    this.alamatSurveyor = JSON.parse(e.detail.value).map(v => v
                        .value.trim());
                });

                mahasiswaTagify.on('change', (e) => {
                    if (!e.detail.value) {
                        this.alamatMahasiswa = [];
                        return;
                    }
                    this.alamatMahasiswa = JSON.parse(e.detail.value).map(v => v
                        .value.trim());
                });
            },
            methods: {
                toggleActiveSurveyor(surveyorId, name) {
                    // Jika surveyor yang sama diklik, nonaktifkan
                    if (this.activeSurveyorId === surveyorId) {
                        this.activeSurveyorId = null;
                        this.activeSurveyorName = null;
                        this.selectedPendaftar = []; // Clear selection
                    } else {
                        // Aktifkan surveyor baru
                        this.activeSurveyorId = surveyorId;
                        this.activeSurveyorName = name;
                        this.selectedPendaftar = []; // Clear selection
                    }
                },
                toggleSelectPendaftar(pendaftarId) {
                    if (!this.activeSurveyorId) {
                        Swal.fire('Peringatan', 'Aktifkan (klik) surveyor terlebih dahulu.', 'warning');
                        return;
                    }

                    const index = this.selectedPendaftar.indexOf(pendaftarId);
                    if (index > -1) {
                        this.selectedPendaftar.splice(index, 1); // Hapus jika sudah ada
                    } else {
                        this.selectedPendaftar.push(pendaftarId); // Tambah jika belum ada
                    }
                },
                plotSelectedMahasiswa() {
                    if (this.selectedPendaftar.length === 0 || !this.activeSurveyorId) {
                        return;
                    }
                    const surveyorName = this.activeSurveyorName;
                    const selectedCount = this.selectedPendaftar.length;

                    Swal.fire({
                        title: 'Konfirmasi Plotting',
                        text: `Anda yakin ingin memplot ${selectedCount} mahasiswa ke surveyor ${surveyorName}?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Plot!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.isLoading = true;
                            Swal.fire({
                                title: `Memploting ${selectedCount} mahasiswa...`,
                                showCancelButton: false,
                                showConfirmButton: false,
                                didOpen: () => Swal.showLoading(),
                                allowOutsideClick: false
                            });

                            $.ajax({
                                url: '{{ route('admin.surveyor.plot_multi') }}', // Asumsi ada endpoint baru untuk multi-plot
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    surveyor_id: this.activeSurveyorId,
                                    pendaftar_ids: this.selectedPendaftar,
                                },
                                success: (response) => {
                                    Swal.fire({
                                        icon: response.icon,
                                        title: response.title,
                                        text: response.message,
                                        timerProgressBar: true,
                                    });
                                    const ind = this.surveyorList.findIndex(sv => {
                                        return sv.id === this.activeSurveyorId;
                                    })
                                    if (ind !== -1) {
                                        this.surveyorList[ind].surveyor_detail = response
                                            .surveyor_detail;
                                    }

                                    this.selectedPendaftar = []; // Clear selection
                                    Swal.fire('Berhasil!', response.message, 'success');
                                },
                                error: (xhr) => {
                                    const msg = xhr.responseJSON ? xhr.responseJSON.message :
                                        'Terjadi kesalahan saat memplot mahasiswa.';
                                    Swal.fire('Gagal!', msg, 'error');
                                },
                                complete: () => {
                                    this.isLoading = false;
                                }
                            });
                        }
                    });
                },
                removeMahasiswa(surveyor, detail) {
                    const pendaftarName = (detail.pendaftar ? detail.pendaftar : detail).mahasiswa.nama;
                    const action = () => {
                        Swal.fire({
                            title: `Menghapus ploting ${pendaftarName}...`,
                            showCancelButton: false,
                            showConfirmButton: false,
                            didOpen: () => Swal.showLoading(),
                            allowOutsideClick: false
                        });

                        $.ajax({
                            url: '{{ route('admin.surveyor.remove') }}',
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                surveyor_detail_id: detail.id,
                            },
                            success: (response) => {
                                // Hapus detail dari list surveyor lokal
                                const detailIndex = surveyor.surveyor_detail.findIndex(d => d.id ===
                                    detail.id);
                                if (detailIndex > -1) {
                                    surveyor.surveyor_detail.splice(detailIndex, 1);
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 1500,
                                    timerProgressBar: true,
                                });
                            },
                            error: (xhr) => {
                                const msg = xhr.responseJSON ? xhr.responseJSON.message :
                                    'Terjadi kesalahan.';
                                Swal.fire('Gagal!', msg, 'error');
                            }
                        });
                    };

                    Swal.fire({
                        title: 'Anda yakin?',
                        text: `Akan menghapus ${pendaftarName} dari surveyor ${surveyor.user.name}.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            action();
                        }
                    });
                },
                // Metode lain (toggleSelection, saveSurveyors) tetap sama...
                toggleSelection(pegawaiId) {
                    const index = this.selectedPegawai.indexOf(pegawaiId);
                    if (index > -1) {
                        this.selectedPegawai.splice(index, 1); // Hapus jika sudah ada
                    } else {
                        this.selectedPegawai.push(pegawaiId); // Tambah jika belum ada
                    }
                },

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
                        url: '{{ route('admin.surveyor.assign') }}',
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
                            }).then(() => {
                                // Perlu memuat ulang halaman untuk menyegarkan data surveyorList
                                window.location.reload();
                            });
                            $('#modalAssignSurveyor').modal('hide');
                            this.selectedPegawai = [];
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
                },
                checkPlottedPendaftar(id) {
                    return this.surveyorList.some(surveyor => {
                        return surveyor.surveyor_detail.some(detail => detail.pendaftar && detail.pendaftar
                            .id === id);
                    });
                }
            },
            computed: {
                countPlotted() {
                    return this.surveyorList.reduce((total, surveyor) => {
                        return total + surveyor.surveyor_detail.length;
                    }, 0);
                },
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
                },
                surveyorBersediaFilter() {
                    if (this.alamatSurveyor.length === 0) {
                        return this.surveyorBersedia;
                    }
                    return this.surveyorBersedia.filter(surveyor => {
                        const surveyorAlamat = surveyor.alamat ? surveyor.alamat.toLowerCase() : '';
                        const surveyorName = surveyor.user.name ? surveyor.user.name.toLowerCase() : '';
                        return this.alamatSurveyor.some(alamat => surveyorAlamat.includes(alamat
                            .toLowerCase()) || surveyorName.includes(alamat
                            .toLowerCase()));
                    });
                },
                pendaftarFilter() {
                    // Filter data pendaftar berdasarkan keyword
                    if (this.alamatMahasiswa.length === 0) {
                        return this.pendaftarList.filter(mhs => !this.checkPlottedPendaftar(mhs.id));
                    }
                    return this.pendaftarList.filter(mhs => {
                        const mahasiswaAlamat = mhs.biodata_pendaftar.data.biodata.alamat_ktp.value ? mhs
                            .biodata_pendaftar.data.biodata.alamat_ktp.value.toLowerCase() : '';
                        const mahasiswaNama = mhs.mahasiswa.nama ? mhs.mahasiswa.nama.toLowerCase() : '';
                        const mahasiswaNIM = mhs.mahasiswa.nim ? '' + (mhs.mahasiswa.nim).toLowerCase() :
                            '';
                        return (this.alamatMahasiswa.some(alamat => mahasiswaAlamat.includes(alamat
                            .toLowerCase()) || mahasiswaNama.includes(alamat
                            .toLowerCase()) || mahasiswaNIM.includes(alamat
                            .toLowerCase()))) && !this.checkPlottedPendaftar(mhs.id);
                    });
                }
            }
        });

        // Fungsi untuk filter data (di luar Vue)
        function reloadData() {
            const tahunId = $('#flt_tahun').val();
            const beasiswaId = $('#flt_beasiswa').val();
            // Lakukan reload dengan parameter filter baru
            window.location.href = `{{ route('admin.surveyor') }}?tahun=${tahunId}&beasiswa=${beasiswaId}`;
        }
    </script>

    @if (request()->get('assign') == '1')
        <script>
            $(document).ready(function() {
                $('#modalAssignSurveyor').modal('show');
            });
        </script>
    @endif
@endpush
