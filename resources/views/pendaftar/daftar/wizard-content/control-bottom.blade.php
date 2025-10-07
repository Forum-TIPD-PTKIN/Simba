<!-- Navigation -->
<div class="d-flex justify-content-between mt-4">
    @if ($step <= 1)
        <button disabled class="btn btn-outline-secondary">Prev</button>
    @else
        <a href="{{ route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=' . ($step - 1) }}"
            class="btn btn-outline-secondary">Prev</a>
    @endif
    @if ($step >= 4)
        <form action="{{ route('pendaftar.daftar.finalisasi', ['id' => $beasiswa->id]) }}" method="post">
            @csrf
            <button id="finalisas-proses" disabled type="submit" class="btn btn-primary">Finalisasi & Ajukan</button>
        </form>
    @else
        <a href="{{ route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=' . ($step + 1) }}"
            class="btn btn-primary">Next</a>
    @endif
</div>
