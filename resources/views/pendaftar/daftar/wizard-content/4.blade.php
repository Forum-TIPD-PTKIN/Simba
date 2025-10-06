<!-- Step Content (contoh isi step 2) -->
<h5 class="mb-3">Step 4: Finalisai</h5>
<div class="row">
    <div class="col-12">
        <div class="alert alert-info py-3">
            <div class="container">
                <strong class="h2">Harap diperhatikan</strong>
                <div class="mb-3"></div>
                <p class="h3 fw-normal">
                    Pastikan seluruh data dan dokumen yang diminta telah diisi dengan lengkap dan
                    <strong>BENAR</strong> sebelum melakukan
                    finalisasi pendaftaran. Setelah proses finalisasi dilakukan, formulir <strong
                        class="text-danger">Tidak dapat lagi diubah</strong> karena
                    telah diproses oleh sistem.
                </p>
            </div>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="checkedFinalisasi">
            <label class="form-check-label" for="checkedFinalisasi">
                Saya telah memahami dan telah menyetuji untuk difinalisasi
            </label>
        </div>
    </div>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            $('#checkedFinalisasi').on('change', function() {
                console.log(this.checked)
                if (this.checked) {
                    $('#finalisas-proses').prop('disabled', false);
                } else {
                    $('#finalisas-proses').prop('disabled', true);
                }
            });
        });
    </script>
@endpush
