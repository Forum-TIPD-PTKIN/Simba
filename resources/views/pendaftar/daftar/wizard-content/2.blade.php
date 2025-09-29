<!-- Step Content (contoh isi step 2) -->
<h5 class="mb-3">Step 2: Biodata Mahasiswa</h5>
<div class="row">
    <div class="col-12 col-md-8 col-lg-6 col-xl-4">
        <div class="alert alert-info">
            <strong>Informasi</strong> Apabila data Anda terdapat kesalahan dibawah ini, silahkan mengajukan perbaikan
            data di sistem SIAKAD Anda.
        </div>
        <div class="row mb-3">
            <div class="col-5">
                <img src="https://be.iainmadura.ac.id/api/v1/external/mahasiswa/foto?nim={{ $mahasiswa->npm }}&key=6321afccabf95b9ec00ac8d193479f4f6a849d46ffbe50fc7e14a74011554fc1"
                    alt="" class="img-thumbnail">
            </div>
            <div class="col-7">
                <div class="mb-3">
                    <label for="nim" class="form-label">NIM</label>
                    <input type="text" disabled class="form-control" id="nim" value="{{ $mahasiswa->npm }}"
                        placeholder="Masukkan NIM">
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" disabled
                        value="{{ $mahasiswa->nama_mahasiswa }}" placeholder="Masukkan nama mahasiswa">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fakultas" class="form-label">Fakultas</label>
                <select class="form-select" id="fakultas" disabled>
                    <option selected>{{ $mahasiswa->prodi->fakultas->nama_fakultas }}</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="prodi" class="form-label">Program Studi</label>
                <select class="form-select" id="prodi" disabled>
                    <option selected>{{ $mahasiswa->prodi->nama_prodi_id }}</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea class="form-control" id="alamat" disabled rows="3" placeholder="Masukkan alamat lengkap">{{ $mahasiswa->alamat }}</textarea>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" disabled placeholder="nama@email.com"
                value="{{ $mahasiswa->email_institusi }}">
        </div>

        <div class="mb-3">
            <label for="telp" class="form-label">No. Telepon</label>
            <input type="tel" class="form-control" id="telp" placeholder="08xxxxxxxxxx" disabled
                value="{{ $mahasiswa->handphone }}">
        </div>

    </div>
    <div class="col-12 col-md-4 col-lg-6 col-xl-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-3 mb-sm-0 text-success">Data PMB</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-8 mb-3">
                        <label for="jalur" class="form-label">Jalur Masuk PMB</label>
                        <input type="text" disabled class="form-control" id="jalur"
                            value="{{ $jalur->nama ?? 'NOT FOUND' }}">
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label for="tahun_masuk" class="form-label">Tahun Masuk</label>
                        <input type="text" disabled class="form-control" id="tahun_masuk"
                            value="{{ $jalur->tahun_masuk ?? 'NOT FOUND' }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-8 mb-3">
                        <label for="jalur" class="form-label">Sekolah Asal</label>
                        <input type="text" disabled class="form-control" id="jalur"
                            value="{{ $jalur->sekolah_asal->master_sekolah->nama ?? 'NOT FOUND' }}">
                    </div>
                    <div class="col-12 col-md-4 mb-3">
                        <label for="tahun_masuk" class="form-label">Tahun Lulus Sekolah</label>
                        <input type="text" disabled class="form-control" id="tahun_masuk"
                            value="{{ $jalur->sekolah_asal->tahun_lulus ?? 'NOT FOUND' }}">
                    </div>
                    <div class="col-6 mb-3">
                        <label for="tahun_masuk" class="form-label">Jurusan (Saat Sekolah)</label>
                        <input type="text" disabled class="form-control" id="tahun_masuk"
                            value="{{ $jalur->sekolah_asal->master_jurusan_sekolah->nama ?? 'NOT FOUND' }}">
                    </div>
                </div>
            </div>
        </div>

        @if (!$readOnly)
            @if (!$register)
                <div class="alert alert-warning fs-6">
                    <h4 class="alert-heading"><i class="ti ti-info-circle"></i> Perhatian!</h4>
                    Silakan konfirmasi persetujuan Anda untuk melakukan pendaftaran Beasiswa
                    {{ $beasiswa->nama }}
                </div>
                <form onsubmit="return confirmPendaftaran(event);" id="form-daftar"
                    action="{{ route('pendaftar.daftar.store', ['id' => $beasiswa->id]) }}" method="post">
                    @csrf
                    @method('POST')
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg fs-5"><i class="ti ti-checkbox"></i>
                            KONFIRMASI PENDAFTARAN</button>
                    </div>
                </form>
            @else
                <div class="alert alert-success fs-4">
                    <i class="fas fa-thumbs-up me-2 text-dark"></i> Anda berhasil mendaftar, silahkan ke step
                    berikutnya
                </div>
            @endif
        @endif
    </div>
</div>

@push('script')
    <script>
        function confirmPendaftaran() {
            event.preventDefault(); // hentikan submit default

            Swal.fire({
                title: "Konfirmasi Pendaftaran",
                text: "Apakah Anda yakin ingin melakukan pendaftaran Beasiswa {{ $beasiswa->nama }}?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#198754",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, daftar",
                cancelButtonText: "Batal",
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // document.getElementById("form-daftar").submit(); // lanjut submit form
                    $.ajax({
                        type: "POST",
                        url: "{{ route('pendaftar.daftar.store', ['id' => $beasiswa->id]) }}",
                        data: {
                            '_token': "{{ csrf_token() }}",
                            'tahun_masuk': "{{ $jalur->tahun_masuk }}",
                            'tahun_lulus': "{{ $jalur->sekolah_asal?->tahun_lulus }}",
                        },
                        success: function(response) {
                            Swal.fire({
                                title: response.title,
                                text: response.message,
                                icon: response.icon,
                                timer: 3000,
                                timerProgressBar: true,
                            }).then(() => {
                                if (response.icon === 'success') {
                                    window.location.replace(response.redirect);
                                }
                            });
                        },
                        error: function(response) {
                            if (response.status !== 200) {
                                const errors = response.responseJSON?.errors;
                                errorText = errors ? Object.values(errors).map(err => err.join(', '))
                                    .join('\n') : null;
                            }
                            Swal.fire({
                                title: "Gagal",
                                icon: "error",
                                text: errorText || response.responseJSON ||
                                    'Terjadi kesalahan',
                            });
                        }
                    });
                }
            });

            return false;
        }
    </script>

    @if (session()->has('error_register'))
        <script>
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "{{ session()->get('error_register') }}",
            });
        </script>
    @endif
@endpush
