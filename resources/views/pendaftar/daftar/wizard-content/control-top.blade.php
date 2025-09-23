<!-- Wizard Header -->
<div class="d-flex justify-content-center gap-md-3 gap-lg-5 gap-2  mb-4">
    <div
        class="wizard-step @if ($step == 1) active @elseif ($step > 1) completed @endif">
        <div class="step-circle">1</div>
        <div class="xsmall">Tentang Beasiswa</div>
    </div>
    <div class="wizard-step @if ($step == 2) active @endif"> {{-- completed --}}
        <div class="step-circle">2</div>
        <div class="xsmall">Biodata Mahasiswa</div>
    </div>
    <div class="wizard-step @if ($step == 3) active @endif">
        <div class="step-circle">3</div>
        <div class="xsmall">Pemberkasan</div>
    </div>
</div>
