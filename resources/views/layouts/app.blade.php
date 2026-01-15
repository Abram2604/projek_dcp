<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Portal DPC SPSI')</title>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap JS (Wajib untuk Dropdown & Modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Load SCSS & JS via Vite -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>

    <!-- 1. Panggil Sidebar -->
    @include('partials.sidebar')

    <main class="main-content">
        
        <!-- 2. PANGGIL NAVBAR (Ini perbaikan utamanya) -->
        <!-- Kode manual dihapus, diganti dengan include ini agar dinamis -->
        @include('partials.navbar')

        <!-- 3. Konten Halaman -->
        @yield('content')
        
    </main>

</body>
</html>