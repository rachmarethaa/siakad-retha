<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIAKAD') }} - @yield('title', 'Dashboard')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

        <!-- Flowbite -->
        <link href="https://cdn.jsdelivr.net/npm/flowbite@2.0.0/dist/flowbite.min.css" rel="stylesheet" />

        <!-- Custom Styles -->
        <style>
            :root {
                --primary-color: #0d6efd;
                --sidebar-width: 260px;
                --transition: all 0.3s ease;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html, body {
                width: 100%;
                height: 100%;
                overflow-x: hidden;
            }

            body {
                font-family: 'Figtree', sans-serif;
                background-color: #f8f9fa;
            }

            .wrapper {
                display: flex;
                min-height: 100vh;
                width: 100%;
            }

            /* Sidebar */
            .sidebar {
                width: var(--sidebar-width);
                background: linear-gradient(135deg, #4a90e2 0%, #2563eb 100%);
                color: white;
                padding: 20px;
                position: fixed;
                left: 0;
                top: 0;
                height: 100vh;
                overflow-y: auto;
                box-shadow: 0 0 20px rgba(0,0,0,0.1);
                z-index: 999;
                transition: left 0.3s ease;
            }

            .sidebar.collapsed {
                left: calc(-1 * var(--sidebar-width));
            }

            .sidebar-header {
                display: flex;
                align-items: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 1px solid rgba(255,255,255,0.2);
            }

            .sidebar-header img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                margin-right: 15px;
                flex-shrink: 0;
            }

            .sidebar-header h6 {
                margin: 0;
                font-size: 14px;
                font-weight: 600;
                white-space: nowrap;
            }

            .sidebar-menu {
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .sidebar-menu li {
                margin-bottom: 0;
            }

            .sidebar-menu a,
            .sidebar-menu button {
                display: flex;
                align-items: center;
                color: rgba(255,255,255,0.8);
                text-decoration: none;
                padding: 12px 15px;
                margin-bottom: 5px;
                border-radius: 8px;
                transition: var(--transition);
                width: 100%;
                text-align: left;
                border: none;
                background: none;
                cursor: pointer;
                font-size: 14px;
            }

            .sidebar-menu a:hover,
            .sidebar-menu a.active,
            .sidebar-menu button:hover {
                background-color: rgba(255,255,255,0.2);
                color: white;
            }

            .sidebar-menu i {
                width: 20px;
                margin-right: 10px;
                flex-shrink: 0;
            }

            /* Main Content */
            .main-content {
                margin-left: var(--sidebar-width);
                flex: 1;
                display: flex;
                flex-direction: column;
                width: calc(100% - var(--sidebar-width));
                transition: all 0.3s ease;
            }

            /* Ensure action buttons (delete/reset/confirm) have readable text color */
            .btn-delete,
            .btn-confirm-yes,
            .btn-confirm-no,
            .btn-reset,
            .btn-reset-yes,
            .btn-reset-no {
                color: #000 !important;
            }

            /* Make sure confirm buttons have visible borders/background when needed */
            .btn-delete {
                background-color: transparent !important;
                border: 1px solid rgba(0,0,0,0.1);
                color: #000 !important;
            }

            .delete-confirm .btn,
            .reset-confirm .btn {
                color: #000 !important;
            }

            /* Prevent Bootstrap danger hover styles from turning buttons red */
            .btn-delete,
            .btn-confirm-yes,
            .btn-reset,
            .btn-reset-yes {
                background-color: transparent !important;
                border-color: rgba(0,0,0,0.08) !important;
                color: #000 !important;
                box-shadow: none !important;
            }

            .btn-delete:hover,
            .btn-confirm-yes:hover,
            .btn-reset:hover,
            .btn-reset-yes:hover,
            .btn-delete:focus,
            .btn-confirm-yes:focus,
            .btn-reset:focus,
            .btn-reset-yes:focus {
                background-color: rgba(0,0,0,0.04) !important;
                color: #000 !important;
                border-color: rgba(0,0,0,0.12) !important;
            }

            /* Specific hover styles: delete = red, reset = yellow */
            .btn-delete:hover,
            .btn-confirm-yes:hover,
            .btn-delete:focus,
            .btn-confirm-yes:focus {
                background-color: #dc3545 !important; /* bootstrap danger */
                border-color: #dc3545 !important;
                color: #fff !important;
            }

            .btn-reset:hover,
            .btn-reset-yes:hover,
            .btn-reset:focus,
            .btn-reset-yes:focus {
                background-color: #ffc107 !important; /* yellow */
                border-color: #ffc107 !important;
                color: #000 !important;
            }

            .main-content.expanded {
                margin-left: 0;
                width: 100%;
            }

            /* Top Navigation */
            .topbar {
                background-color: white;
                padding: 15px 25px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                display: flex;
                justify-content: space-between;
                align-items: center;
                position: sticky;
                top: 0;
                z-index: 500;
                min-height: 70px;
                flex-shrink: 0;
            }

            .topbar-left {
                display: flex;
                align-items: center;
                gap: 20px;
                flex: 1;
                min-width: 0;
            }

            .topbar-right {
                display: flex;
                align-items: center;
                gap: 20px;
                flex-shrink: 0;
            }

            .toggle-sidebar {
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #4a90e2;
                padding: 8px;
                min-width: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                transition: var(--transition);
            }

            .toggle-sidebar:hover {
                color: #1565c0;
            }

            .topbar-left h5 {
                margin: 0;
                color: #333;
                font-weight: 600;
                font-size: 16px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .user-profile {
                display: flex;
                align-items: center;
                gap: 12px;
                cursor: pointer;
                position: relative;
                flex-shrink: 0;
                padding: 8px 12px;
                border-radius: 8px;
                transition: var(--transition);
            }

            .user-profile:hover {
                background-color: #f8f9fa;
            }

            .user-profile img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                flex-shrink: 0;
            }

            .user-profile > div {
                display: flex;
                flex-direction: column;
                min-width: 0;
            }

            .user-profile p {
                margin: 0;
                white-space: nowrap;
            }

            .page-content {
                padding: 30px;
                flex: 1;
                overflow-y: auto;
            }

            .page-header {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 30px;
                flex-wrap: wrap;
                gap: 20px;
            }

            .page-header h1 {
                font-size: 28px;
                font-weight: 700;
                color: #333;
            }

            .page-header p {
                color: #777;
                margin-top: 5px;
            }

            .breadcrumb {
                background-color: transparent;
                padding: 0;
                margin-bottom: 20px;
            }

            /* Cards */
            .stat-card {
                background: white;
                border-radius: 12px;
                padding: 20px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.05);
                transition: var(--transition);
                height: 100%;
            }

            .stat-card:hover {
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                transform: translateY(-5px);
            }

            .stat-card-icon {
                font-size: 32px;
                margin-bottom: 15px;
            }

            .stat-card-title {
                font-size: 12px;
                color: #999;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 10px;
            }

            .stat-card-value {
                font-size: 32px;
                font-weight: 700;
                color: #333;
            }

            /* Responsive */
            @media (max-width: 768px) {
                :root {
                    --sidebar-width: 250px;
                }

                .sidebar {
                    width: 250px;
                }

                .sidebar.collapsed {
                    left: -250px;
                }

                .main-content {
                    margin-left: 0;
                    width: 100%;
                }

                .topbar {
                    padding: 12px 15px;
                    min-height: 60px;
                }

                .topbar-left {
                    gap: 12px;
                }

                .topbar-left h5 {
                    font-size: 14px;
                }

                .toggle-sidebar {
                    font-size: 20px;
                    min-width: 36px;
                }

                .page-content {
                    padding: 15px;
                }

                .user-profile {
                    gap: 8px;
                }

                .user-profile img {
                    width: 35px;
                    height: 35px;
                }

                .user-profile > div {
                    display: none;
                }

                .page-header {
                    margin-bottom: 20px;
                }

                .page-header h1 {
                    font-size: 22px;
                }
            }

            /* Alert Styles */
            .alert {
                border-radius: 8px;
                border: none;
                margin-bottom: 20px;
            }

            .alert-success {
                background-color: #d4edda;
                color: #155724;
            }

            .alert-danger {
                background-color: #f8d7da;
                color: #721c24;
            }

            .alert-info {
                background-color: #d1ecf1;
                color: #0c5460;
            }

            .btn-logout {
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                border: none;
                color: white;
                padding: 8px 20px;
                border-radius: 6px;
                cursor: pointer;
                transition: var(--transition);
            }

            .btn-logout:hover {
                transform: scale(1.05);
                box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
            }

            /* Row spacing untuk cards */
            .row {
                margin: 0 -15px;
            }

            .row > [class*="col-"] {
                padding: 0 15px;
                margin-bottom: 25px;
            }

            /* Sidebar overlay on mobile */
            @media (max-width: 768px) {
                .sidebar:not(.collapsed) {
                    box-shadow: 0 0 30px rgba(0,0,0,0.3);
                }
            }
        </style>

        @yield('extra-css')
    </head>
    <body>
        <div class="wrapper">
            <script>
                // Silence console output in the browser to avoid showing warnings/logs
                if (typeof window !== 'undefined' && window.console) {
                    ['log','info','warn','error','debug','table','trace'].forEach(function(m){
                        try { window.console[m] = function(){}; } catch(e) {}
                    });
                }
            </script>
            <!-- Sidebar -->
            <aside class="sidebar" id="sidebar">
                <div class="sidebar-header">
                    <img src="{{ asset('Politeknik Bhakti Semesta.png') }}" alt="Logo Politeknik Bhakti Semesta" title="SIAKAD - Politeknik Bhakti Semesta">
                    <h6>SIAKAD</h6>
                </div>

                <ul class="sidebar-menu">
                    @php
                        $guard = auth()->getDefaultDriver();
                    @endphp

                    @if ($guard === 'staff')
                        <li>
                            <a href="{{ route('staff.dashboard') }}" class="@if(Route::currentRouteName() === 'staff.dashboard') active @endif">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.dosen.index') }}" class="@if(request()->routeIs('staff.dosen.*')) active @endif">
                                <i class="bi bi-people"></i> Kelola Dosen
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.mahasiswa.index') }}" class="@if(request()->routeIs('staff.mahasiswa.*')) active @endif">
                                <i class="bi bi-mortarboard"></i> Kelola Mahasiswa
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.laporan.dashboard') }}" class="@if(request()->routeIs('staff.laporan.*')) active @endif">
                                <i class="bi bi-bar-chart"></i> Laporan Presensi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.tiket.index') }}" class="@if(request()->routeIs('staff.tiket.*')) active @endif">
                                <i class="bi bi-ticket"></i> Tiket Keluhan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.jadwal.index') }}" class="@if(request()->routeIs('staff.jadwal.*')) active @endif">
                                <i class="bi bi-calendar3"></i> Jadwal Mengajar
                            </a>
                        </li>
                    @elseif ($guard === 'dosen')
                        <li>
                            <a href="{{ route('dosen.dashboard') }}" class="@if(Route::currentRouteName() === 'dosen.dashboard') active @endif">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                       
                        <li>
                            <a href="{{ route('dosen.presensi.index') }}" class="@if(request()->routeIs('dosen.presensi.*')) active @endif">
                                <i class="bi bi-check2-square"></i> Manajemen Presensi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dosen.laporan.dashboard') }}" class="@if(request()->routeIs('dosen.laporan.*')) active @endif">
                                <i class="bi bi-graph-up"></i> Laporan Presensi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dosen.jadwal.index') }}" class="@if(request()->routeIs('dosen.jadwal.*')) active @endif">
                                <i class="bi bi-calendar3"></i> Jadwal Mengajar
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dosen.nilai.index') }}" class="@if(request()->routeIs('dosen.nilai.*')) active @endif">
                                <i class="bi bi-pencil-square"></i> Input Nilai
                            </a>
                        </li>
                        {{-- Konsultasi Mahasiswa dihapus sesuai permintaan --}}
                         <li>
                            <a href="{{ route('dosen.tiket.index') }}" class="@if(request()->routeIs('dosen.tiket.*')) active @endif">
                                <i class="bi bi-ticket"></i> Tiket Keluhan
                            </a>
                        </li>
                    @elseif ($guard === 'mahasiswa')
                        <li>
                            <a href="{{ route('mahasiswa.dashboard') }}" class="@if(Route::currentRouteName() === 'mahasiswa.dashboard') active @endif">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mahasiswa.presensi.index') }}" class="@if(request()->routeIs('mahasiswa.presensi.*')) active @endif">
                                <i class="bi bi-check2-square"></i> Data Presensi Saya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mahasiswa.jadwal.index') }}" class="@if(request()->routeIs('mahasiswa.jadwal.*')) active @endif">
                                <i class="bi bi-calendar3"></i> Jadwal Kuliah
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mahasiswa.nilai.index') }}" class="@if(request()->routeIs('mahasiswa.nilai.*')) active @endif">
                                <i class="bi bi-graph-up"></i> Nilai & Transkrip
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('mahasiswa.krs.index') }}" class="@if(request()->routeIs('mahasiswa.krs.*')) active @endif">
                                <i class="bi bi-card-text"></i> KRS
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('tiket.mahasiswa.index') }}" class="@if(request()->routeIs('tiket.mahasiswa.*')) active @endif">
                                <i class="bi bi-ticket"></i> Tiket Keluhan
                            </a>
                        </li>
                    @endif
                </ul>

                <hr style="border-color: rgba(255,255,255,0.2); margin-top: 30px; margin-bottom: 15px;">

                <ul class="sidebar-menu">
                    @if ($guard === 'dosen')
                        <li>
                            <a href="{{ route('dosen.profile.edit') }}" class="@if(Route::currentRouteName() === 'dosen.profile.edit') active @endif">
                                <i class="bi bi-person-gear"></i> Edit Profil
                            </a>
                        </li>
                    @elseif ($guard === 'mahasiswa')
                        <li>
                            <a href="{{ route('mahasiswa.profile.edit') }}" class="@if(Route::currentRouteName() === 'mahasiswa.profile.edit') active @endif">
                                <i class="bi bi-person-gear"></i> Edit Profil
                            </a>
                        </li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout.all') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="background: none; border: none; width: 100%; text-align: left; padding: 0;">
                                <a style="cursor: pointer; display: flex; align-items: center; color: rgba(255,255,255,0.8); text-decoration: none; padding: 12px 15px; margin-bottom: 5px; border-radius: 8px; transition: all 0.3s ease;">
                                    <i class="bi bi-box-arrow-right" style="width: 20px; margin-right: 10px;"></i> Logout
                                </a>
                            </button>
                        </form>
                    </li>
                </ul>
            </aside>

            <!-- Main Content -->
            <div class="main-content" id="mainContent">
                <!-- Top Navigation -->
                <div class="topbar">
                    <div class="topbar-left">
                        <button class="toggle-sidebar" id="toggleSidebar">
                            <i class="bi bi-list"></i>
                        </button>
                        <h5 style="margin: 0; color: #333;">
                            @yield('page-title', 'Dashboard')
                        </h5>
                    </div>

                    <div class="topbar-right">
                        <div class="user-profile" id="userProfileDropdown" style="position: relative; cursor: pointer;">
                            <img src="https://ui-avatars.com/api/?name={{ auth()->user()->name }}&background=4a90e2&color=fff" alt="Profile" style="border-radius: 50%;">
                            <div>
                                <p style="margin: 0; font-weight: 600; color: #333; font-size: 14px;">
                                    {{ auth()->user()->name }}
                                </p>
                                <p style="margin: 0; font-size: 12px; color: #777;">
                                    @php
                                        $role = auth()->getDefaultDriver();
                                        echo ucfirst(str_replace(['staff', 'dosen', 'mahasiswa'], 
                                                ['Staff/Admin', 'Dosen', 'Mahasiswa'], $role));
                                    @endphp
                                </p>
                            </div>

                            <!-- User Dropdown Menu -->
                            <div id="userDropdownMenu" style="
                                display: none;
                                position: absolute;
                                right: 0;
                                top: 100%;
                                background: white;
                                border-radius: 8px;
                                box-shadow: 0 5px 20px rgba(0,0,0,0.15);
                                margin-top: 10px;
                                z-index: 1001;
                                min-width: 200px;
                            ">
                                @php $guard = auth()->getDefaultDriver(); @endphp
                                
                                @if ($guard === 'dosen')
                                    <a href="{{ route('dosen.profile.edit') }}" style="
                                        display: block;
                                        padding: 12px 20px;
                                        color: #333;
                                        text-decoration: none;
                                        border-bottom: 1px solid #f0f0f0;
                                        transition: all 0.3s ease;
                                    " onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                        <i class="bi bi-person-gear"></i> Edit Profil
                                    </a>
                                @elseif ($guard === 'mahasiswa')
                                    <a href="{{ route('mahasiswa.profile.edit') }}" style="
                                        display: block;
                                        padding: 12px 20px;
                                        color: #333;
                                        text-decoration: none;
                                        border-bottom: 1px solid #f0f0f0;
                                        transition: all 0.3s ease;
                                    " onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                        <i class="bi bi-person-gear"></i> Edit Profil
                                    </a>
                                @endif

                                <form method="POST" action="{{ route('logout.all') }}" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="
                                        width: 100%;
                                        padding: 12px 20px;
                                        border: none;
                                        background: none;
                                        color: #dc3545;
                                        text-align: left;
                                        cursor: pointer;
                                        border-radius: 0 0 8px 8px;
                                        transition: all 0.3s ease;
                                    " onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='white'">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page Content -->
                <div class="page-content">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Flowbite JS -->
        <script src="https://cdn.jsdelivr.net/npm/flowbite@2.0.0/dist/flowbite.min.js"></script>

        <script>
            // Toggle Sidebar
            const toggleBtn = document.getElementById('toggleSidebar');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');

            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });

            // Close sidebar on mobile when clicking menu item
            if (window.innerWidth < 768) {
                document.querySelectorAll('.sidebar-menu a').forEach(link => {
                    link.addEventListener('click', function() {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('expanded');
                    });
                });
            }

            // Toggle User Profile Dropdown
            const userProfileDropdown = document.getElementById('userProfileDropdown');
            const userDropdownMenu = document.getElementById('userDropdownMenu');

            if (userProfileDropdown && userDropdownMenu) {
                userProfileDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (userDropdownMenu.style.display === 'none') {
                        userDropdownMenu.style.display = 'block';
                    } else {
                        userDropdownMenu.style.display = 'none';
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userProfileDropdown.contains(e.target)) {
                        userDropdownMenu.style.display = 'none';
                    }
                });

                // Close dropdown when clicking on a link inside it
                userDropdownMenu.querySelectorAll('a, button').forEach(item => {
                    item.addEventListener('click', function() {
                        userDropdownMenu.style.display = 'none';
                    });
                });
            }
        </script>

        @yield('extra-js')
    </body>
</html>
