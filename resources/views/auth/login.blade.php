<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Sistem Manajemen</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Load CSS/JS -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="login-page">

    <div class="login-card">
        <!-- HEADER -->
        <div class="login-header">
            <!-- SVG Background Curve -->
            <div class="header-bg">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" style="width:100%; height:100%;">
                    <path d="M0 100 C 20 0 50 0 100 100 Z" fill="white" />
                </svg>
            </div>

            <!-- Logo Box -->
        <div class="logo-box">
            <!-- Menggunakan logo.png dari folder public/img -->
            <img src="{{ asset('img/logo.png') }}" alt="Logo SPSI" style="width: 70%; height: auto;">
        </div>
            
            <h4>Sistem Manajemen</h4>
            <p>DPC FSP LEM SPSI KARAWANG</p>
        </div>

        <!-- FORM BODY -->
        <div class="login-body">
            
            <!-- Alert Error -->
            @if ($errors->any())
            <div class="alert alert-danger d-flex align-items-center rounded-3 mb-4 border-0 bg-danger bg-opacity-10 text-danger" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <small class="fw-bold">{{ $errors->first() }}</small>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <!-- Input Username -->
                <div class="mb-4">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="username" class="form-control" placeholder="Masukan username anda" required autofocus>
                    </div>
                </div>

                <!-- Input Password -->
                <div class="mb-4">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-login">
                    Masuk ke Dashboard
                </button>
            </form>

            <!-- Footer -->
            <div class="text-center mt-5">
                <p class="text-muted small mb-1" style="font-size: 10px;">© 2026 DPC FSP LEM SPSI KARAWANG</p>
                <p class="text-muted small text-uppercase fw-bold" style="font-size: 9px; letter-spacing: 0.5px;">Powered by Tim Night Raid4</p>
            </div>
        </div>
    </div>
</body>
</html>