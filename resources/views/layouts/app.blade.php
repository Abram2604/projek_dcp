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

    <!-- [PERBAIKAN] CSS Tambahan untuk Modal & Layout -->
    <style>
        /* Pastikan Modal selalu di lapisan paling atas */
        .modal-backdrop {
            z-index: 1050 !important;
        }
        .modal {
            z-index: 1060 !important;
        }

        /* Pastikan z-index sidebar di bawah modal backdrop */
        .sidebar-wrapper {
            z-index: 1040;
        }
        
        /* 
         * Atasi modal yang terlalu lebar dan menabrak sidebar di desktop.
         * Kita beri sedikit margin kiri saat sidebar aktif.
         * Nilai margin-left harus sama dengan lebar sidebar (default 280px)
        */
        @media (min-width: 992px) {
            .modal-dialog.modal-xl,
            .modal-dialog.modal-lg {
                transition: margin-left 0.3s ease-in-out;
            }

            body:not(.sidebar-collapsed) .modal-dialog.modal-xl,
            body:not(.sidebar-collapsed) .modal-dialog.modal-lg {
                margin-left: 290px; /* Lebar sidebar + sedikit padding */
            }
        }
    </style>
    
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