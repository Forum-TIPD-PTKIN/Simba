@extends('admin.template.master-template')

@section('title', 'Tahun Kegiatan')

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
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Tahun Kegiatan</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Tahun Kegiatan</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!--[ Main Content ] start-->
            <div class="row" id="app-vue">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Data Tahun Kegiatan</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h5><i class="icon fas fa-exclamation-triangle"></i> Petunjuk!</h5>
                                Klik pada tombol untuk mengaktifkan tahun pelaksanaan beasiswa.
                            </div>
                            <div class="i-main" id="icon-wrapper">
                                <div :class="item.status === 1 ? 'i-block bg-green-100 fw-bold' : 'i-block'"
                                    v-for="(item, index) in tahun" v-on:click="updateStatusTahun(item, index)"
                                    data-bs-toggle="tooltip" :title="item.tahun">
                                    <span v-if="item.status === 1"
                                        class="position-absolute top-0 start-100 translate-middle p-2 bg-success border border-light rounded-circle">
                                        <span class="visually-hidden">Active</span>
                                    </span>
                                    @{{ item.tahun }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--[ Main Content ] end-->
        </div>
    </div>
    <!-- [ Main Content ] end -->
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.7.14/vue.min.js"
        integrity="sha512-BAMfk70VjqBkBIyo9UTRLl3TBJ3M0c6uyy2VMUrq370bWs7kchLNN9j1WiJQus9JAJVqcriIUX859JOm12LWtw=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        const app = new Vue({
            el: '#app-vue',
            data: {
                tahun: @JSON($tahun),
            },
            methods: {
                updateStatusTahun: (item, index) => {
                    Swal.fire({
                        title: 'Sedang memproses...',
                        showCancelButton: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                            $.ajax({
                                type: "POST",
                                url: "{{ route('admin.tahun-kegiatan.store') }}",
                                data: {
                                    '_token': "{{ csrf_token() }}",
                                    'tahun': item.encrypted_id
                                },
                                dataType: 'JSON',
                                success: function(data) {
                                    const msg = JSON.parse(JSON.stringify(data));
                                    Swal.fire({
                                        icon: msg.icon,
                                        title: msg.title,
                                        text: msg.message
                                    });

                                    app.tahun.map(val => {
                                        if (item.encrypted_id !== val
                                            .encrypted_id) {
                                            val.status = 0;
                                            return true;
                                        }
                                        return false;
                                    });
                                    app.tahun[index].status = 1;
                                },
                                error: function(data) {
                                    const msg = JSON.parse(JSON.stringify(data));
                                    Swal.fire({
                                        icon: 'error',
                                        title: "Gagal",
                                        text: msg.responseJSON.message
                                    });
                                }
                            });
                            return false;
                        },
                        allowOutsideClick: false
                    });
                }
            }
        });
    </script>
@endpush
