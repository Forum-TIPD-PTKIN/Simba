<!-- Step Content (contoh isi step 2) -->
<h5 class="mb-3">Step 4: Finalisasi & Pengajuan</h5>
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
        <div class="row">
            <div class="col-lg-6">
                <h5 class="mb-3">Informasi Pendaftar</h5>
                <table class="table table-borderless table-sm infodata">
                    <tbody>
                        <tr>
                            <td class="fw-bold" style="width: 150px;">NIM</td>
                            <td>: {{ $pendaftar?->mahasiswa->nim }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Nama</td>
                            <td>: {{ $pendaftar?->mahasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Fakultas/Prodi</td>
                            <td>: {{ $pendaftar?->mahasiswa->fakultas_prodi }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Beasiswa</td>
                            <td>: {{ $pendaftar?->beasiswa->nama }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Tahun Kegiatan</td>
                            <td>: {{ $pendaftar?->tahun_kegiatan->tahun }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <hr>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="checkedFinalisasi">
            <label class="form-check-label" for="checkedFinalisasi">
                Saya telah memahami dan telah menyetujui untuk difinalisasi
            </label>
        </div>
    </div>
</div>
@push('script')
    <script>
        $(document).ready(function() {
            $('#checkedFinalisasi').on('change', function() {
                // console.log(this.checked)
                if (this.checked) {
                    $('#finalisas-proses').prop('disabled', false);
                } else {
                    $('#finalisas-proses').prop('disabled', true);
                }
            });
        });
    </script>
@endpush
