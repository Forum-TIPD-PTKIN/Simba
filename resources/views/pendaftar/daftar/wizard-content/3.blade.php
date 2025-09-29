@if (!isset($nowizard) || $nowizard !== true)
    <h5 class="mb-3">Step 3: Pemberkasan</h5>
@endif
<div class="row">
    <div class="col-12">
        @foreach ($generated_form as $item)
            <form enctype="multipart/form-data" action="{{ route('pendaftar.pemberkasan.store') }}" id="form-berkas"
                method="POST">
                @csrf
                <input type="hidden" name="beasiswa" value="{{ $beasiswa->id }}">
                {!! $item['form'] !!}
            </form>
        @endforeach
    </div>
</div>


@push('script')
    <script>
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
