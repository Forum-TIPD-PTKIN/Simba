<script src="https://code.jquery.com/jquery-3.7.1.min.js"
    integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- [Page Specific JS] start -->
<script src="{{ asset('assets/admin/js/plugins/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/pages/dashboard-default.js') }}"></script>
<!-- [Page Specific JS] end -->
<!-- Required Js -->
<script src="{{ asset('assets/admin/js/plugins/popper.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/plugins/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/plugins/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/admin/js/fonts/custom-font.js') }}"></script>
<script src="{{ asset('assets/admin/js/script.js') }}"></script>
<script src="{{ asset('assets/admin/js/theme.js') }}"></script>
<script src="{{ asset('assets/admin/js/plugins/feather.min.js') }}"></script>
<!-- Buy Now Link Script -->
<script defer src="https://fomo.codedthemes.com/pixel/CDkpF1sQ8Tt5wpMZgqRvKpQiUhpWE3bc"></script>

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

@yield('script')
