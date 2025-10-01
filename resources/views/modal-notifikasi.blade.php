<div class="modal-header">
    <h1 class="modal-title fs-5" id="modalNotifikasiLabel">Notifikasi</h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="card">
        <div class="card-body">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    <svg class="pc-icon text-primary">
                        <use xlink:href="#custom-sms"></use>
                    </svg>
                </div>
                <div class="flex-grow-1 ms-3">
                    <span
                        class="float-end text-sm text-muted">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</span>
                    <h5 class="text-body mb-2">Pesan</h5>
                    <p>{!! $notif->pesan !!}</p>
                    @if ($notif->referensi)
                        <p class="mb-0">Selengkapnya, klik <a class="fst-italic"
                                href="{{ url($notif->getRawOriginal('referensi')) }}">tautan</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
