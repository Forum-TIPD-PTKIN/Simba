<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

@include('verifikator.template.head')
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

    @include('verifikator.template.sidebar')

    @include('verifikator.template.header')

    @yield('content')

    @include('verifikator.template.footer')

    @include('verifikator.template.script')

</body>
<!-- [Body] end -->

</html>
