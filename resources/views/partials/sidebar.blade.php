<aside class="sidebar-wrapper">
    <!-- 1. HEADER -->
    <div class="sidebar-header">
        <button id="sidebarClose" class="btn btn-link text-white d-lg-none p-0 border-0">
            <i class="fa-solid fa-xmark fa-xl"></i>
        </button>
        <div class="logo-box">
            <img src="{{ asset('img/logo.png') }}" alt="Logo SPSI" style="width: 70%; height: auto;">
        </div>
        <div class="app-title">
            <h1>DPC FSP LEM</h1>
            <p>SPSI KARAWANG</p>
        </div>
    </div>

    <!-- 2. MENU -->
    <nav class="sidebar-menu">
        @php
            $jabatan = session('user_jabatan', 'Anggota');
            $level   = session('user_level', 'ANGGOTA');
            $isBPH   = ($level === 'BPH');
            $isPimpinanDPC  = ($jabatan === 'Ketua DPC' || $jabatan === 'Bendahara');
        @endphp

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-table-columns"></i>
            <span>Dashboard</span>
        </a>

        <!-- Absensi -->
        <a href="{{ route('absensi.index') }}" class="nav-item {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Absensi</span>
        </a>

        <!-- Laporan Harian (FIXED: Menggunakan Route Name agar spesifik) -->
        <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
            <i class="fa-regular fa-file-lines"></i>
            <span>Laporan Harian</span>
        </a>

        <!-- Program Kerja -->
        <a href="{{ route('progja.index') }}" class="nav-item {{ request()->routeIs('progja.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            <span>Program Kerja</span>
        </a>
        
        <a href="{{ route('profile') }}" class="nav-item {{ request()->routeIs('profile') ? 'active' : '' }}">
            <i class="fa-solid fa-id-card"></i>
            <span>Profil Saya</span>
        </a>

        <!-- Keuangan (Hanya BPH) -->
        @if($isBPH)
        <a href="{{ route('keuangan.index') }}" class="nav-item {{ request()->routeIs('keuangan.*') ? 'active' : '' }}">
            <i class="fa-solid fa-wallet"></i>
            <span>Keuangan</span>
        </a>
        @endif

        <!-- Laporan Keuangan (Ketua & Bendahara) -->
        @if($isPimpinanDPC)
        <a href="{{ route('laporan_keuangan.index') }}" class="nav-item {{ request()->routeIs('laporan_keuangan.*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Laporan Keuangan</span>
        </a>
        @endif

        @php
            $userJabatan = session('user_jabatan'); 
            $isPimpinan  = ($userJabatan === 'Ketua DPC' || $userJabatan === 'Sekretaris');
            $isOrg   = str_contains($jabatan, 'Organisasi'); 
            $canManageUser = ($jabatan === 'Ketua DPC' || $jabatan === 'Sekretaris' || $isOrg);
        @endphp

        @if($isPimpinan)
        <a href="{{ route('data_anggota.index') }}" class="nav-item {{ request()->routeIs('data_anggota.*') ? 'active' : '' }}">
            <i class="fa-solid fa-database"></i>
            <span>Data Anggota</span>
        </a>
        @endif

        @if($canManageUser)
        <a href="{{ route('users.index') }}" class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fa-solid fa-users-gear"></i>
            <span>Manajemen User</span>
        </a>
        @endif
    </nav>

    <!-- 3. FOOTER INFO -->
    <div class="sidebar-footer">
        <div class="info-card">
            <div class="info-label">
                <i class="fa-regular fa-building"></i>
                <span>INFORMASI DIVISI</span>
            </div>
            <p class="user-name">{{ Auth::user()->nama_lengkap ?? 'User' }}</p>
            <p class="user-dept">{{ Str::limit($jabatan, 25) }}</p>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
            </button>
        </form>
    </div>
</aside>