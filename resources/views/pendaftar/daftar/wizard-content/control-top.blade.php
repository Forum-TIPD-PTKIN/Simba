<!-- Wizard Header -->
<div class="d-flex justify-content-center gap-md-3 gap-lg-5 gap-2 mb-4">
    <div
        class="wizard-step @if ($step == 1) active @elseif ($step > 1) completed @endif">
        <a href="?step=1" class="text-dark">
            <div class="step-circle">1</div>
            <div class="xsmall">Tentang Beasiswa</div>
        </a>
    </div>
    <div
        class="wizard-step @if ($step == 2) active @elseif ($step > 2) completed @endif">
        {{-- completed --}}
        <a href="?step=2" class="text-dark">
            <div class="step-circle">2</div>
            <div class="xsmall">Biodata Mahasiswa</div>
        </a>
    </div>
    <div
        class="wizard-step @if ($step == 3) active @elseif ($step > 3) completed @endif">
        <a href="?step=3" class="text-dark">
            <div class="step-circle">3</div>
            <div class="xsmall">Pemberkasan</div>
        </a>
    </div>
    <div class="wizard-step @if ($step == 4) active @endif">
        <a href="?step=4" class="text-dark">
            <div class="step-circle">4</div>
            <div class="xsmall">Finalisasi</div>
        </a>
    </div>
</div>
