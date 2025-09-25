@if (count(Auth::user()->access) > 1)
    <p class="text-span">User Akses</p>
    @foreach (Auth::user()->access as $item)
        <a href="{{ Auth::user()->access_active === $item ? '#' : route('akses.ganti', ['access' => $item]) }}"
            class="dropdown-item">
            <span>
                @if ($item === Auth::user()->access_active)
                    <i class="fas fa-check"></i>
                @else
                    <svg class="pc-icon text-muted" style="margin-right:10px !important;">
                        <use xlink:href="#none"></use>
                    </svg>
                @endif
                <span>
                    @switch($item)
                        @case(0)
                            Administrator
                        @break

                        @case(1)
                            Verifikator
                        @break

                        @case(2)
                            Mahasiswa
                        @break
                    @endswitch
                </span>
            </span>
        </a>
    @endforeach
    <hr class="border-secondary border-opacity-50" />
@endif
