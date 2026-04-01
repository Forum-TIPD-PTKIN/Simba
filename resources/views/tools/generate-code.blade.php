<!doctype html>
<html lang="en" data-bs-theme="auto">

@include('admin.template.head')

<body class="bg-body-tertiary">
    <div class="container">
        <main>
            <div class="py-5 text-center">
                <h1 class="h2">Generate Code</h1>
            </div>
            <div class="row g-5">
                <div class="col-12">
                    <h4 class="mb-3">Data</h4>
                    <form class="needs-validation" novalidate>
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="year" class="form-label">Year</label>
                                <select class="form-select" id="year" required>
                                    <option value="" disabled>Choose...</option>
                                    @foreach ($year as $item)
                                        <option value="{{ $item->id }}">{{ $item->tahun }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please select a valid year.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="scholarship" class="form-label">Scholarship</label>
                                <select class="form-select" id="scholarship" required>
                                    <option value="" disabled>Choose...</option>
                                    @foreach ($scholarship as $item)
                                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback">
                                    Please provide a valid scholarship.
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="candidate" class="form-label">Candidate</label>
                                <select class="form-select" id="candidate" required>
                                    <option value="" disabled>Choose...</option>
                                </select>
                                <div class="invalid-feedback">
                                    Please provide a valid candidate.
                                </div>
                            </div>
                        </div>
                        <button class="w-100 btn btn-primary btn-lg" type="button" id="generateBtn">
                            Generate Code
                        </button>
                    </form>
                </div>

                <div class="col-12">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">Code</span>
                    </h4>
                    <div class="row row-cols-1 row-cols-md-2 mb-3 text-center">
                        <div class="col">
                            <div class="card mb-4 rounded-3 shadow-sm">
                                <div class="card-header py-3">
                                    <h4 class="my-0 fw-normal">Code 1</h4>
                                </div>
                                <div class="card-body">
                                    <small>
                                        <pre id="codeBlock1" class="bg-light p-3 border rounded text-start" style="white-space: pre-wrap;"></pre>
                                    </small>
                                    <button type="button" class="w-100 btn btn-lg btn-outline-primary" id="copyBtn1">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card mb-4 rounded-3 shadow-sm">
                                <div class="card-header py-3">
                                    <h4 class="my-0 fw-normal">Code 2</h4>
                                </div>
                                <div class="card-body">
                                    <small>
                                        <pre id="codeBlock2" class="bg-light p-3 border rounded text-start" style="white-space: pre-wrap;"></pre>
                                    </small>
                                    <button type="button" class="w-100 btn btn-lg btn-outline-primary" id="copyBtn2">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            function loadCandidates() {
                let year = $('#year').val();
                let scholarship = $('#scholarship').val();

                if (year && scholarship) {
                    $.ajax({
                        url: "{{ route('get.candidates') }}", // buat route di Laravel
                        type: 'GET',
                        data: {
                            year: year,
                            scholarship: scholarship
                        },
                        success: function(response) {
                            // kosongkan dulu option candidate
                            $('#candidate').empty();
                            $('#candidate').append(
                                '<option value="" disabled selected>Choose...</option>');

                            // isi dengan data dari server
                            response.forEach(function(item) {
                                $('#candidate').append(
                                    `<option value="${item.id}">${item.mahasiswa?.nama} - ${item.mahasiswa?.nim}</option>`
                                );
                            });
                        }
                    });
                }
            }

            // trigger saat halaman siap
            loadCandidates();

            // trigger saat salah satu select berubah
            $('#year, #scholarship').on('change', function() {
                loadCandidates();
            });

            // generate code
            const step1 = {
                "Nama Mahasiswa": "",
                "Universitas/Perguruan Tinggi": "Universitas Islam Negeri Madura",
                "Nama Fakultas": "",
                "Nama Program Studi": "",
                "Nomor Induk Mahasiswa (NIM)": "",
                "Jenis Kelamin": "",
                "Tempat & Tanggal lahir": "",
                "IPK Semester Terakhir": "",
                "Nomor HP": "",
                "Alamat Email": "",
                "Alamat Tinggal Saat Ini": "",
                "Kabupaten/Kota (Alamat Tinggal Saat Ini)": "",
                "Alamat Tinggal sesuai KTP ?": "",
                "Alamat Tinggal (Sesuai KTP)": "",
                "Kabupaten/Kota (Sesuai KTP)": "",
            };

            const step2 = {
                "Nama Ibu Kandung/Wali Ibu": "",
                "Pekerjaan Ibu Kandung/Wali Ibu": "",
                "Penghasilan Ibu Kandung/Wali Ibu": "",
                "Nama Bapak Kandung/Wali Bapak": "",
                "Pekerjaan Bapak Kandung/Wali Bapak": "",
                "Penghasilan Bapak Kandung/Wali Bapak": "",
                "Alamat Tinggal Orangtua/Wali": "",
                "Nama Akun Instagram": "",
                "Nama Akun Tiktok": "",
                "Ceklist Berkas Mahasiswa": [
                    "Form A1",
                    "SKTM (Diterbitkan Kepala Desa/Kelurahan)",
                    "Slip Gaji Orang Tua/Wali (Diterbitkkan Kantor Bekerja/Kepala Desa/Kelurahan)",
                    "Transkrip Nilai",
                    "KTP",
                    "KTM",
                    "Kartu Keluarga",
                    "Surat Pernyataan Mahasiswa",
                    "Surat Komitmen GenBI",
                    "Surat Keterangan Mahasiswa Aktif",
                    "Surat Rekomendasi Tokoh Akademik",
                    "Curriculum Vitae (CV)",
                    "Motivation Letter",
                    "Sertifikat",
                    "Dokumen Lainnya (Bukti Bayar Listrik, PBB dan PDAM terakhir)"
                ],
            };

            function dedent(str) {
                const lines = str.split('\n');

                // 1. Cari baris konten pertama untuk acuan indentasi
                const firstContentLine = lines.find(line => line.trim().length > 0);
                if (!firstContentLine) return str.trim();

                const match = firstContentLine.match(/^[ \t]*/);
                const indent = match ? match[0].length : 0;

                // 2. Potong spasi HANYA jika jumlah spasi di baris tersebut >= indent
                return lines
                    .map(line => {
                        const currentIndent = line.match(/^[ \t]*/)[0].length;
                        // Hanya potong jika barisnya menjorok, jika tidak (seperti isi ${code}), biarkan saja
                        return currentIndent >= indent ? line.slice(indent) : line;
                    })
                    .join('\n')
                    .trim();
            }

            const code = dedent(`
                const items = Array.from(document.querySelectorAll('div[role="listitem"]'));
                Object.keys(data).forEach((key) => {
                    const value = data[key];
                    if (!value) return;

                    const item = items.find((el) => {
                        const heading = el.querySelector('[role="heading"]');
                        const labelText = heading
                        ? heading.innerText.replace(/\\n|\\*/g, "").trim()
                        : "";
                        return labelText === key;
                    });

                    if (!item) return;

                    const textInput = item.querySelector(
                        'input[type="text"], input[type="email"], textarea, .whsOnd'
                    );
                    if (textInput) {
                        textInput.value = value;
                        textInput.dispatchEvent(new Event("input", { bubbles: true }));
                        return;
                    }

                    const radio = item.querySelector(\`div[role="radio"][data-value="\${value}"]\`);
                    if (radio) {
                        radio.click();
                        return;
                    }

                    const checkValues = Array.isArray(value) ? value : [value];
                    checkValues.forEach((val) => {
                        const checkbox = item.querySelector(
                        \`div[role="checkbox"][data-answer-value="\${val}"]\`
                        );
                        if (checkbox && checkbox.getAttribute("aria-checked") !== "true") {
                            checkbox.click();
                        }
                    });
                });
                clear();
            `);

            // ketika tombol generate diklik
            $('#generateBtn').on('click', function() {
                let year = $('#year').val();
                let scholarship = $('#scholarship').val();
                let candidate = $('#candidate').val();

                if (year && scholarship && candidate) {
                    $.ajax({
                        url: "{{ route('get.candidates.code') }}", // buat route di Laravel
                        type: 'GET',
                        data: {
                            year: year,
                            scholarship: scholarship,
                            candidate: candidate
                        },
                        success: function(response) {
                            // ambil biodata dari response
                            const bio = response.biodata_pendaftar.data.biodata;

                            // isi step1 dengan value dari response
                            step1["Nama Mahasiswa"] = response.mahasiswa.nama;
                            step1["Nama Fakultas"] = response.mahasiswa.fakultas_name;
                            step1["Nama Program Studi"] =
                                `S1 ${response.mahasiswa.prodi_long_name}`;
                            step1["Nomor Induk Mahasiswa (NIM)"] = response.mahasiswa.nim;
                            step1["Jenis Kelamin"] = bio.jenis_kelamin.value === '1' ?
                                "Laki - Laki" : "Perempuan";
                            step1["Tempat & Tanggal lahir"] = bio.ttl.value;
                            step1["IPK Semester Terakhir"] = bio.ipk.value;
                            step1["Nomor HP"] = bio.no_hp.value;
                            step1["Alamat Email"] = bio.email.value;
                            step1["Alamat Tinggal Saat Ini"] = bio.alamat.value;
                            step1["Kabupaten/Kota (Alamat Tinggal Saat Ini)"] = bio.kabupaten
                                .value;
                            step1["Alamat Tinggal sesuai KTP ?"] = bio.alamat_sesuai_ktp
                                .value === '1' ? "Iya" : "Tidak";
                            step1["Alamat Tinggal (Sesuai KTP)"] = bio.alamat_ktp.value;
                            step1["Kabupaten/Kota (Sesuai KTP)"] = bio.kabupaten_ktp.value;

                            // isi step2 dengan value dari response
                            step2["Nama Ibu Kandung/Wali Ibu"] = bio.nama_ibu.value;
                            step2["Pekerjaan Ibu Kandung/Wali Ibu"] = bio.kerja_ibu.value;
                            step2["Penghasilan Ibu Kandung/Wali Ibu"] = bio.penghasilan_ibu
                                .value;
                            step2["Nama Bapak Kandung/Wali Bapak"] = bio.nama_bapak.value;
                            step2["Pekerjaan Bapak Kandung/Wali Bapak"] = bio.kerja_bapak.value;
                            step2["Penghasilan Bapak Kandung/Wali Bapak"] = bio
                                .penghasilan_bapak.value;
                            step2["Alamat Tinggal Orangtua/Wali"] = bio.alamat_orang_tua.value;
                            step2["Nama Akun Instagram"] = bio.akun_ig.value;
                            step2["Nama Akun Tiktok"] = bio.akun_tiktok.value;
                            // step2["Ceklist Berkas Mahasiswa"] = response.pemberkasan.data
                            //     .pemberkasan ?
                            //     Object.values(response.pemberkasan.data.pemberkasan).map(item =>
                            //         item.text) : [];

                            // kosongkan dulu option candidate
                            $('#codeBlock1').empty();
                            $('#codeBlock2').empty();

                            // tampilkan kode sebagai teks
                            $('#codeBlock1').text(dedent(`
                                const step1 = ${JSON.stringify(step1, null, 2)};

                                const data = step1;
                                ${code}
                            `));

                            $('#codeBlock2').text(dedent(`
                                const step2 = ${JSON.stringify(step2, null, 2)};

                                const data = step2;
                                ${code}
                            `));
                        }
                    });
                }
            });

            // tombol copy
            $('#copyBtn1').on('click', function() {
                const code = $('#codeBlock1').text();
                navigator.clipboard.writeText(code).then(() => {
                    alert('Code 1 copied to clipboard!');
                });
            });
            $('#copyBtn2').on('click', function() {
                const code = $('#codeBlock2').text();
                navigator.clipboard.writeText(code).then(() => {
                    alert('Code 2 copied to clipboard!');
                });
            });
        });
    </script>
</body>

</html>
