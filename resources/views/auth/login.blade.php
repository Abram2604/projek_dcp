<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Sistem Manajemen</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Load CSS Manual -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="login-page">

    <div class="login-card">
        <!-- HEADER -->
        <div class="login-header">
            <!-- SVG Background Curve (Sudah ada di style.css) -->
            <div class="logo-box">
                <img src="{{ asset('img/logo.png') }}" alt="Logo SPSI">
            </div>
            <h4>Sistem Manajemen</h4>
            <p>DPC FSP LEM SPSI KARAWANG</p>
        </div>

        <!-- FORM BODY -->
        <div class="login-body">
            
            @if ($errors->any())
            <div class="alert alert-danger d-flex align-items-center rounded-3 mb-4" role="alert">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <small class="fw-bold">{{ $errors->first() }}</small>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label>Username</label>
                    <div class="input-wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="username" class="form-control" placeholder="Masukan username anda" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Masuk
                </button>
            </form>

            <div class="text-center mt-5">
                <p class="text-muted small mb-1" style="font-size: 10px;">© 2026 DPC FSP LEM SPSI KARAWANG</p>
                <p class="text-muted small" style="font-size: 9px;">Powered by Tim Night Raid4</p>
            </div>
        </div>
    </div>
</body>
</html>