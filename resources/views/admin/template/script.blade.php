<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- Required Js -->
<script src="{{ asset('assets/admin/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('assets/admin/js/script.js') }}"></script>
<script src="{{ asset('assets/admin/js/theme.js') }}"></script>
<script src="{{ asset('assets/admin/js/plugins/feather.min.js') }}"></script>

<!-- Jquery Validate -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/additional-methods.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.10.8/sweetalert2.all.min.js"
    integrity="sha512-ziDG00v9lDjgmzxhvyX5iztPHpSryN/Ct/TAMPmMmS2O3T1hFPRdrzVCSvwnbPbFNie7Yg5mF7NUSSp5smu7RA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    change_box_container('false');
</script>
<script>
    layout_caption_change('true');
</script>
<script>
    layout_rtl_change('false');
</script>
<script>
    preset_change("preset-1");
</script>

<script>
    $('.modal').on('hidden.bs.modal', function(e) {
        const form = $(this).find('form');
        if (form.length) {
            form[0].reset(); // Reset input from form
            form.find('.select').val(null).trigger('change'); // reset select2
            form.validate().resetForm(); // Reset state after validated
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.is-valid').removeClass('is-valid');
        }
    });
</script>

<script>
    $('.logout').on('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Sedang memproses...',
                    showCancelButton: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        $.ajax({
                            url: $(this).attr('href'),
                            success: function(res) {
                                Swal.fire({
                                    title: res.title,
                                    text: res.message,
                                    icon: res.icon,
                                    timer: 1500,
                                    timerProgressBar: true,
                                }).then(() => {
                                    if (res.icon === 'success') {
                                        window.location.replace(res
                                            .redirect);
                                    }
                                });
                            },
                            error: function(res) {
                                Swal.fire({
                                    title: 'Gagal',
                                    icon: 'error',
                                    text: 'Ada kesalahan'
                                });
                            }
                        });
                    },
                    allowOutsideClick: false
                });
            }
        });
    });
</script>


@stack('script')
