<!-- Step Content (contoh isi step 2) -->
<h5 class="mb-3">Step 2: Biodata Mahasiswa</h5>
<div class="row">
    <div class="col-12 col-md-8 col-lg-6 col-xl-4">
        <div class="alert alert-info">
            <strong>Informasi</strong> Apabila data anda terdapat kesalahan dibawah ini, silahkan mengajukan perbaikan
            data di sistem SIAKAD anda.
        </div>
        <form>
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
        </form>

    </div>
    <div class="col-12 col-md-4 col-lg-6 col-xl-8">
        <div class="row justify-content-start">
            <div class="ms-5 col-5 col-md-7 col-lg-5 col-xl-3">
                <img src="https://be.iainmadura.ac.id/api/v1/external/mahasiswa/foto?nim={{ $mahasiswa->npm }}&key=6321afccabf95b9ec00ac8d193479f4f6a849d46ffbe50fc7e14a74011554fc1"
                    alt="" class="img-thumbnail">
            </div>
        </div>
    </div>
</div>
