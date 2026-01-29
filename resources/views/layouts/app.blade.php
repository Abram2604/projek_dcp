<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Sistem DPC SPSI')</title>
    
    <!-- 1. FONT AWESOME (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- 2. BOOTSTRAP CSS (dari folder public) -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    
    <!-- 3. CUSTOM CSS (dari folder public) -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    
</head>
<body>

    <!-- OVERLAY (Untuk Sidebar Mobile) -->
    <div class="sidebar-overlay"></div>

    <!-- Panggil Sidebar -->
    @include('partials.sidebar')

    <main class="main-content">
        
        <!-- Panggil Navbar -->
        @include('partials.navbar')

        <!-- Konten Halaman -->
        @yield('content')
        
    </main>

    <!-- 4. JQUERY (WAJIB paling atas sebelum script lain) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- 5. BOOTSTRAP JS (Sudah include Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- 6. CUSTOM JS (dari folder public) -->
    <script src="{{ asset('js/script.js') }}"></script>

</body>
</html>