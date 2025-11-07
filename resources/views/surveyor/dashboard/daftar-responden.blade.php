<div class="row">
    @foreach ($responden as $key => $item)
        @php
            $label = [
                'total_responden' => [
                    'bg' => 'bg-blue-100',
                    'icon' => 'fas fa-users',
                    'text' => 'Total Peserta Survei',
                ],
                'sudah_disurvei' => ['bg' => 'bg-green-100', 'icon' => 'fas fa-user-check', 'text' => 'Proses Survei'],
                'belum_disurvei' => [
                    'bg' => 'bg-yellow-100',
                    'icon' => 'fas fa-user-clock',
                    'text' => 'Belum Disurvei',
                ],
            ];

            $bg = $label[$key]['bg'] ?? 'bg-danger-100';
            $icon = $label[$key]['icon'] ?? 'fas fa-user-times';
            $text = $label[$key]['text'] ?? 'Total Peserta Survei';
        @endphp
        <div class="col-md-4">
            <div class="card {{ $bg }}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-1">{{ $item }} <span class="fs-6">Pelamar</span>
                            </h3>
                            <p class="text-muted mb-0">{{ $text }}</p>
                        </div>
                        <div class="col-4 text-end">
                            <i class="{{ $icon }} text-secondary f-36"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
