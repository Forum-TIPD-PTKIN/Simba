<!-- Navigation -->
<div class="d-flex justify-content-between mt-4">
    @if ($step <= 1)
        <button disabled class="btn btn-outline-secondary">Prev</button>
    @else
        <a href="{{ route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=' . ($step - 1) }}"
            class="btn btn-outline-secondary">Prev</a>
    @endif
    @if ($step >= 3)
        <button onclick="simpanFile()" class="btn btn-primary">Simpan File</button>
    @else
        <a href="{{ route('pendaftar.daftar', ['id' => $beasiswa->id]) . '?step=' . ($step + 1) }}"
            class="btn btn-primary">Next</a>
    @endif
</div>
