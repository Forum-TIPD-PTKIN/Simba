<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Login | APP Beasiswa</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Able Pro is a trending dashboard template built with the Bootstrap 5 design framework. It is available in multiple technologies, including Bootstrap, React, Vue, CodeIgniter, Angular, .NET, and more.">
    <meta name="keywords"
        content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard">
    <meta name="author" content="Phoenixcoded">

    <!-- [Favicon] icon -->
    <link rel="icon" href="https://api.iainmadura.ac.id/assets/imgs/logo.png" type="image/x-icon">
    <!-- [Font] Family -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/inter/inter.css') }}" id="main-font-link" />
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/tabler-icons.min.css') }}">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/feather.css') }}">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/fontawesome.css') }}">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="{{ asset('assets/admin/fonts/material.css') }}">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style-preset.css') }}">
</head>
<!-- [Head] end -->
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

    <div class="auth-main">
        <div class="auth-wrapper v1">
            <div class="auth-form">
                <div class="card my-5">
                    <form action="{{ route('login.post') }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="text-center">
                                <a href="#"><img src="{{ asset('assets/admin/images/logo_app_beasiswa.png') }}"
                                        alt="img" class="w-50"></a>
                            </div>
                            <h4 class="text-center f-w-500 mb-3 mt-5">Halaman Login</h4>
                            <div class="form-group mb-3">
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Username" autocomplete="off" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" autocomplete="off" required>
                            </div>
                            <div class="d-flex mt-1 justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input input-primary" type="checkbox" id="customCheckc1"
                                        checked="">
                                    <label class="form-check-label text-muted" for="customCheckc1">Remember me?</label>
                                </div>
                                <h6 class="text-secondary f-w-400 mb-0">Forgot Password?</h6>
                            </div>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
    <!-- Required Js -->
    <script src="{{ asset('assets/admin/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/fonts/custom-font.js') }}"></script>
    <script src="{{ asset('assets/admin/js/script.js') }}"></script>
    <script src="{{ asset('assets/admin/js/theme.js') }}"></script>
    <script src="{{ asset('assets/admin/js/plugins/feather.min.js') }}"></script>










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

</body>
<!-- [Body] end -->

</html>
