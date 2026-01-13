<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal DPC SPSI')</title>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Load SCSS & JS via Vite -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>

    <!-- Panggil Sidebar -->
    @include('partials.sidebar')

    <main class="main-content">
        <!-- Topbar Mobile & Desktop -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <button id="sidebarToggle" class="btn btn-light shadow-sm d-lg-none">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div>
                    <h4 class="fw-bold text-dark m-0">@yield('header_title')</h4>
                    <small class="text-muted">@yield('header_subtitle')</small>
                </div>
            </div>

            <!-- Profil Dropdown -->
            <div class="dropdown">
                <button class="btn bg-white shadow-sm border-0 rounded-pill py-2 px-3 d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <div class="text-end d-none d-md-block line-height-sm">
                        <div class="fw-bold small">Admin User</div>
                        <div class="text-muted" style="font-size: 10px;">Ketua DPC</div>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=4f46e5&color=fff" class="rounded-circle" width="35">
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
                    <li><a class="dropdown-item" href="#">Profil Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="#">Logout</a></li>
                </ul>
            </div>
        </div>

        <!-- Konten Halaman -->
        @yield('content')
    </main>

</body>
</html>