<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

@include('admin.template.head')
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr"
    data-pc-theme_contrast="" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->

    @include('admin.template.sidebar')

    @include('admin.template.header')

    @yield('content')

    @include('admin.template.footer')

    @include('admin.template.script')

    <!-- Modal Notifikasi -->
    <div class="modal fade" id="modalNotifikasi" tabindex="-1" aria-labelledby="modalNotifikasiLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            </div>
        </div>
    </div>
</body>
<!-- [Body] end -->

</html>
