<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Sistem DPC SPSI')</title>
    
    <!-- 1. FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Load SCSS & JS via Vite -->
    
    <!-- 2. Vite (Load CSS & JS) -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>

    <!-- Panggil Sidebar -->
    @include('partials.sidebar')

    <main class="main-content">
        
        <!-- 5. Panggil Navbar (YANG ADA NOTIFIKASINYA) -->
        <!-- Pastikan nama filenya sesuai lokasi kamu menyimpan navbar tadi -->
        @include('partials.navbar')

        <!-- 6. Konten Halaman -->
        @yield('content')
        
        
    </main>

    <!-- 7. Script Bootstrap (Taruh di bawah agar loading cepat) -->
       <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>