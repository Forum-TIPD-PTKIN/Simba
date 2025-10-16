@if (!isset($nowizard) || $nowizard !== true)
    <h5 class="mb-3">Step 3: Pemberkasan</h5>
@endif
<div class="row">
    <div class="col-12">
        @foreach ($generated_form as $item)
            <div class="card">
                <div class="card-header bg-gray-300">
                    <h5>{{ $item['jenis'] }}</h5>
                </div>
                <div class="card-body">
                    <form enctype="multipart/form-data" action="{{ route('pendaftar.pemberkasan.store') }}"
                        id="form-berkas" method="POST">
                        @csrf
                        <input type="hidden" name="beasiswa" value="{{ $beasiswa->id }}">
                        {!! $item['form'] !!}
                    </form>
                    <div class="d-flex justify-content-end">
                        <button onclick="simpanFile()" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>


@push('script')
    <script>
        function viewControl(e) {
            let container = document.getElementById('form-berkas');
            let links = container.querySelectorAll('.base-berkas');
            let urls = []
            links.forEach(link => {
                let url = link.getAttribute('data-url');
                let type = link.getAttribute('data-type');
                let extension = link.getAttribute('data-extension');
                urls.push({
                    url,
                    type,
                    extension
                })
            });
            const data = {
                active: {
                    url: e.getAttribute('data-url'),
                    type: e.getAttribute('data-type'),
                    extension: e.getAttribute('data-extension')
                },
                data: urls
            }
            $.ajax({
                type: 'post',
                url: "{{ route('view.control') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    data: data
                },
                dataType: 'HTML',
                success: function(data) {
                    console.log(data)
                    const winUrl = URL.createObjectURL(
                        new Blob([data], {
                            type: "text/html"
                        })
                    );

                    const margin = 100; // Jarak tepi agar tidak full full banget
                    const width = window.screen.availWidth - margin * 8;
                    const height = window.screen.availHeight - margin * 2;
                    const left = (window.screen.availWidth - width) / 2;
                    const top = (window.screen.availHeight - height) / 2;

                    const win = window.open(
                        winUrl,
                        "win",
                        `width=${width},height=${height},top=${top},left=${left}`
                    );
                }
            });
        }

        function simpanFile() {
            Swal.fire({
                title: "Tunggu!",
                text: "Sedang menyimpan...",
                didOpen: () => {
                    Swal.showLoading();
                    let form = document.getElementById('form-berkas');
                    let formData = new FormData(form);

                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            Swal.fire({
                                title: response.title,
                                text: response.message,
                                icon: response.icon,
                            }).then(() => {
                                window.location.reload();
                            })
                        },
                        error: function(xhr) {
                            const errors = xhr.responseJSON;
                            let html = "<ul style='text-align:left'>";
                            for (let field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    errors[field].forEach(msg => {
                                        html += `<li>${msg}</li>`;
                                    });
                                }
                            }
                            html += "</ul>";

                            Swal.fire({
                                icon: 'error',
                                title: 'Validasi Gagal',
                                html: html
                            });
                        },
                    });

                },
            }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                    console.log("I was closed by the timer");
                }
            });


        }
    </script>
@endpush
