@if (count($beasiswa))
    <ul class="list-group list-group-flush">
        @foreach ($beasiswa as $item)
            <li class="list-group-item">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="avtar avtar-s border"> {{ textInitials($item->nama) }} </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="row g-1">
                            <div class="col-6">
                                <h6 class="mb-0">{{ $item->nama }}</h6>
                                <p class="text-muted mb-0"><small>{!! Str::words(strip_tags($item->deskripsi), 10, '...') !!}</small></p>
                            </div>
                            <div class="col-6 d-flex justify-content-end align-items-center">
                                <a href="javascript:void(0);" role="button" data-id="{{ $item->id }}"
                                    class="fw-bold btnDetail">Lihat selengkapnya</a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@else
    <div class="loader">Tidak Ada Data</div>
@endif
