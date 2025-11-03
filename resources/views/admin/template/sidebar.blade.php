<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('assets/admin/images/logo_app_beasiswa.png') }}" class="img-fluid logo-lg"
                    alt="logo">
                <span class="badge bg-light-success rounded-pill ms-2 theme-version">v1.0.0</span>
            </a>
        </div>
        <div class="navbar-content">
            <div class="card pc-user-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('assets/admin/images/user/avatar-2.jpg') }}" alt="user-image"
                                class="user-avtar wid-45 rounded-circle" />
                        </div>
                        <div class="flex-grow-1 ms-3 me-2">
                            <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                            <small>{{ userAccessName() }}</small>
                        </div>
                        <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse"
                            href="#pc_sidebar_userlink">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-sort-outline"></use>
                            </svg>
                        </a>
                    </div>
                    <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                        <div class="pt-3">
                            <a href="#!">
                                <i class="ti ti-user"></i>
                                <span>My Account</span>
                            </a>
                            <a href="#!">
                                <i class="ti ti-settings"></i>
                                <span>Settings</span>
                            </a>
                            <a href="#!">
                                <i class="ti ti-lock"></i>
                                <span>Lock Screen</span>
                            </a>
                            <a href="{{ route('logout') }}" class="logout">
                                <i class="ti ti-power"></i>
                                <span>Logout</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <ul class="pc-navbar">
                <li class="pc-item pc-caption">
                    <label>Navigation</label>
                </li>

                <li class="pc-item">
                    <a href="{{ route('admin.dashboard') }}" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-status-up"></use>
                            </svg>
                        </span>
                        <span class="pc-mtext">Dashboard</span>
                    </a>
                </li>
                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link"><span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-setting-2"></use>
                            </svg> </span><span class="pc-mtext">Data Master</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.tahun-kegiatan') }}" class="pc-link">Tahun Kegiatan</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.beasiswa') }}" class="pc-link">Beasiswa</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.jadwal-kegiatan') }}" class="pc-link">Jadwal Kegiatan</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.form-data') }}" class="pc-link">Form Data</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.pengguna') }}" class="pc-link">Pengguna</a>
                        </li>
                    </ul>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link"><span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-user"></use>
                            </svg> </span><span class="pc-mtext">Surveyor</span><span class="pc-arrow"><i
                                data-feather="chevron-right"></i></span></a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.surveyor') }}" class="pc-link">Assign</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.surveyor.rekap') }}" class="pc-link">Rekap</a>
                        </li>
                    </ul>
                </li>
                <li class="pc-item pc-caption">
                    <label>Seleksi</label>
                    <svg class="pc-icon">
                        <use xlink:href="#custom-box-1"></use>
                    </svg>
                </li>
                <li class="pc-item">
                    <a href="{{ route('admin.laporan.verifikasi') }}" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-user-add"></use>
                            </svg> </span><span class="pc-mtext">Seleksi Administrasi
                        </span>
                    </a>
                </li>

                <li class="pc-item pc-hasmenu">
                    <a href="#!" class="pc-link">
                        <span class="pc-micon">
                            <i class="fas fa-laptop-code"></i>
                        </span>
                        <span class="pc-mtext">Seleksi TPA</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.seleksi-tpa') }}" class="pc-link">Peserta</a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.seleksi-tpa.pelulusan') }}" class="pc-link">Pelulusan</a>
                        </li>
                    </ul>
                </li>

                <li class="pc-item pc-caption">
                    <label>Laporan</label>
                    <svg class="pc-icon">
                        <use xlink:href="#custom-box-1"></use>
                    </svg>
                </li>
                <li class="pc-item">
                    <a href="{{ route('admin.laporan.rekap') }}" class="pc-link">
                        <span class="pc-micon">
                            <svg class="pc-icon">
                                <use xlink:href="#custom-document-text"></use>
                            </svg> </span><span class="pc-mtext">Rekapitulasi
                        </span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>
<!-- [ Sidebar Menu ] end -->
