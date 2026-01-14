<aside class="sidebar-wrapper">
    <!-- 1. HEADER -->
    <div class="sidebar-header">
        <div class="logo-box">
            <!-- Ganti dengan <img> jika ada logo.png, atau pakai icon -->
            <i class="fa-solid fa-shield-halved"></i>
        </div>
        <div class="app-title">
            <h1>DPC FSP LEM</h1>
            <p>SPSI KARAWANG</p>
        </div>
    </div>

    <!-- 2. MENU -->
    <nav class="sidebar-menu">
        @php
            // Ambil data dari session (karena kita pakai AuthController yang simpan session)
            $jabatan = session('user_jabatan', 'Anggota');
            $level   = session('user_level', 'ANGGOTA');
            $isBPH   = ($level === 'BPH');
            $isOrg   = str_contains($jabatan, 'Organisasi');
        @endphp

        <!-- Dashboard -->
        <a href="{{ url('/dashboard') }}" class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <i class="fa-solid fa-table-columns"></i>
            <span>Dashboard</span>
        </a>

        <!-- Absensi -->
        <a href="{{ url('/absensi') }}" class="nav-item {{ Request::is('absensi*') ? 'active' : '' }}">
            <i class="fa-solid fa-calendar-check"></i>
            <span>Absensi</span>
        </a>

        <!-- Laporan -->
        <a href="{{ url('/laporan') }}" class="nav-item {{ Request::is('laporan*') ? 'active' : '' }}">
            <i class="fa-regular fa-file-lines"></i>
            <span>Laporan Harian</span>
        </a>

        <!-- Program Kerja -->
        <a href="{{ url('/progja') }}" class="nav-item {{ Request::is('progja*') ? 'active' : '' }}">
            <i class="fa-solid fa-chart-line"></i>
            <span>Program Kerja</span>
        </a>

        <!-- Keuangan (Hanya BPH) -->
        @if($isBPH)
        <a href="{{ url('/keuangan') }}" class="nav-item {{ Request::is('keuangan*') ? 'active' : '' }}">
            <i class="fa-solid fa-wallet"></i>
            <span>Keuangan</span>
        </a>
        @endif

        <!-- Users (BPH & Organisasi) -->
        @if($isBPH || $isOrg)
        <a href="{{ url('/users') }}" class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
            <i class="fa-solid fa-users"></i>
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