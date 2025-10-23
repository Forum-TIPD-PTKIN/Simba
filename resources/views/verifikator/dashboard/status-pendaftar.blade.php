<div class="row">
    @foreach ($rekap_status as $item)
        <div class="col-md-6">
            <div class="card {{ $item['label'] === 'SUDAH DIVERIFIKASI' ? 'bg-teal-100' : 'bg-red-100' }}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-1">{{ $item['jumlah'] }} <span class="fs-6">Pendaftar</span></h3>
                            <p class="text-muted mb-0">{{ $item['label'] }}</p>
                        </div>
                        <div class="col-4 text-end">
                            <i
                                class="ti {{ $item['label'] === 'SUDAH DIVERIFIKASI' ? 'ti-user-check' : 'ti-user-x' }} text-secondary f-36"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
