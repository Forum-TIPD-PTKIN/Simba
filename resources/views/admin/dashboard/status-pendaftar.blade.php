<div class="row">
    @foreach ($rekap_status as $item)
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-square-rounded">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 13a3 3 0 1 0 0 -6a3 3 0 0 0 0 6z" />
                                    <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
                                    <path d="M6 20.05v-.05a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v.05" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex-grow-0 ms-3">
                            <h6 class="mb-0">{{ $item['label'] }}</h6>
                        </div>
                    </div>
                    <div class="bg-body p-3 mt-3 rounded">
                        <h3 class="mb-0 text-center">{{ $item['value'] }} <span class="h6">Peserta</span></h3>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
