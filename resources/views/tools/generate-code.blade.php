<!doctype html>
<html lang="en" data-bs-theme="auto">

<title>Generator Code</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

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

                <div class="col-12 my-3">
                    <h4 class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-primary">Files</span>
                    </h4>
                    <div class="row row-cols-1 text-center">
                        <div class="col">
                            <div class="card rounded-3 shadow-sm">
                                <div class="card-body">
                                    <ul class="list-group" id="fileList">
                                        <li class="list-group-item text-center text-muted">
                                            Pilih kandidat untuk melihat daftar file.
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 my-3">
                    <h4 class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-primary">Code</span>
                    </h4>
                    <div class="row row-cols-1 row-cols-md-2 mb-3 text-center">
                        <div class="col">
                            <div class="card rounded-3 shadow-sm">
                                <div class="card-header py-3">
                                    <h4 class="my-0 fw-normal">Code 1</h4>
                                </div>
                                <div class="card-body">
                                    <div style="max-height: 112px;overflow: auto;">
                                        <pre id="codeBlock1" class="bg-light p-3 border rounded text-start" style="white-space: pre-wrap;"></pre>
                                    </div>
                                    <button type="button" class="w-100 btn btn-lg btn-outline-primary" id="copyBtn1">
                                        Copy
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card rounded-3 shadow-sm">
                                <div class="card-header py-3">
                                    <h4 class="my-0 fw-normal">Code 2</h4>
                                </div>
                                <div class="card-body">
                                    <div style="max-height: 112px;overflow: auto;">
                                        <pre id="codeBlock2" class="bg-light p-3 border rounded text-start" style="white-space: pre-wrap;"></pre>
                                    </div>
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

    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="myToast" class="toast bg-success text-light" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">Notifikasi</strong>
                <small class="text-muted">Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Pesan toast dari JavaScript.
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#candidate").select2({
                theme: 'bootstrap-5',
                placeholder: 'Choose...',
                allowClear: true
            });

            function loadCandidates() {
                let year = $('#year').val();
                let scholarship = $('#scholarship').val();

                if (year && scholarship) {
                    $.ajax({
                        url: "{{ route('get.candidates') }}",
                        type: 'GET',
                        data: {
                            year: year,
                            scholarship: scholarship
                        },
                        success: function(response) {
                            $('#candidate').select2({
                                data: response.map(item => ({
                                    id: item.id,
                                    text: `${item.mahasiswa?.nama} - ${item.mahasiswa?.nim}`
                                }))
                            }).trigger('change');
                        },
                    });
                }
            }

            // trigger saat halaman siap
            loadCandidates();

            // trigger saat salah satu select berubah
            $('#year, #scholarship').on('change', function() {
                loadCandidates();
                $('#codeBlock1').empty();
                $('#codeBlock2').empty();

            });

            $('#candidate').on('change', function() {
                let candidateId = $(this).val();
                let $fileList = $('#fileList');
                $('#codeBlock1').empty();
                $('#codeBlock2').empty();


                if (candidateId) {
                    // Tampilkan status loading
                    $fileList.html(
                        '<li class="list-group-item text-center text-muted">Loading files...</li>');

                    $.ajax({
                        url: "{{ route('get.candidates.files') }}",
                        type: 'GET',
                        data: {
                            candidate: candidateId
                        },
                        success: function(response) {
                            $fileList.empty();
                            if (response && response.length > 0) {
                                response.forEach(function(file) {
                                    let listItem = `
                                        <li class="list-group-item d-flex justify-content-between align-items-center text-start">
                                            <span>${file.name}</span>
                                            <a href="${file.url}" class="btn btn-sm btn-danger" target="_blank">Download</a>
                                        </li>
                                    `;
                                    $fileList.append(listItem);
                                });
                            } else {
                                $fileList.html(
                                    '<li class="list-group-item text-center text-muted">Tidak ada file untuk kandidat ini.</li>'
                                );
                            }
                        },
                        error: function() {
                            $fileList.html(
                                '<li class="list-group-item text-center text-danger">Gagal mengambil data file.</li>'
                            );
                        }
                    });
                } else {
                    $fileList.html(
                        '<li class="list-group-item text-center text-muted">Pilih kandidat untuk melihat daftar file.</li>'
                    );
                }
            });
            // -------------------------------------------------------------

            // generate code variables
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
                const firstContentLine = lines.find(line => line.trim().length > 0);
                if (!firstContentLine) return str.trim();

                const match = firstContentLine.match(/^[ \t]*/);
                const indent = match ? match[0].length : 0;

                return lines
                    .map(line => {
                        const currentIndent = line.match(/^[ \t]*/)[0].length;
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

            function generateCode() {
                let year = $('#year').val();
                let scholarship = $('#scholarship').val();
                let candidate = $('#candidate').val();

                if (year && scholarship && candidate) {
                    $.ajax({
                        url: "{{ route('get.candidates.code') }}",
                        type: 'GET',
                        data: {
                            year: year,
                            scholarship: scholarship,
                            candidate: candidate
                        },
                        success: function(response) {
                            const berkasArr = Object.entries(response.pemberkasan?.data
                                ?.pemberkasan).map(([key, value]) => ({
                                key,
                                ...value
                            }));

                            berkasArr.sort((a, b) => a.index - b.index);
                            console.log(berkasArr);

                            const bio = response.biodata_pendaftar.data.biodata;

                            step1["Nama Mahasiswa"] = response.mahasiswa.nama;
                            step1["Nama Fakultas"] =
                                `Fakultas ${response.mahasiswa.fakultas_name}`;
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

                            $('#codeBlock1').empty();
                            $('#codeBlock2').empty();

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
            }

            // ketika tombol generate diklik
            $('#generateBtn').on('click', function() {
                generateCode();
            });

            // tombol copy
            $('#copyBtn1').on('click', function() {
                const code = $('#codeBlock1').text();
                navigator.clipboard.writeText(code).then(() => {
                    showToast('Code 1 copied to clipboard!');
                });
            });
            $('#copyBtn2').on('click', function() {
                const code = $('#codeBlock2').text();
                navigator.clipboard.writeText(code).then(() => {
                    showToast('Code 2 copied to clipboard!');
                });
            });

            function showToast(message) {
                const toastEl = document.getElementById('myToast');
                toastEl.querySelector('.toast-body').textContent = message;

                const toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    </script>
</body>

</html>
