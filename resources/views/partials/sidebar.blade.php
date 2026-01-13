<div class="sidebar-menu">
    @php
        $user = Auth::user();
        
        // LOGIC ROLE SEMENTARA (Berdasarkan Username Dummy)
        // Kita mendeteksi role dari username langsung biar gampang
        $username = $user->username; 
        
        $isBPH = in_array($username, ['ketua', 'sekretaris', 'bendahara']);
        $isOrganisasi = ($username == 'organisasi');
        
        // Tampilan Nama Jabatan di Bawah
        $labelJabatan = ucfirst($username); // Ketua, Sekretaris, dll
    @endphp

    <a href="{{ url('/dashboard') }}" class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-table-columns"></i><span>Dashboard</span>
    </a>

    <div class="nav-title">Menu Utama</div>
    
    <a href="{{ url('/absensi') }}" class="nav-link {{ Request::is('absensi*') ? 'active' : '' }}">
        <i class="fa-solid fa-calendar-check"></i><span>Absensi</span>
    </a>
    <a href="{{ url('/laporan') }}" class="nav-link {{ Request::is('laporan*') ? 'active' : '' }}">
        <i class="fa-regular fa-file-lines"></i><span>Laporan Harian</span>
    </a>
    <a href="{{ url('/progja') }}" class="nav-link {{ Request::is('progja*') ? 'active' : '' }}">
        <i class="fa-solid fa-chart-line"></i><span>Program Kerja</span>
    </a>

    <!-- Menu Khusus BPH -->
    @if($isBPH)
    <div class="nav-title">Manajemen</div>
    <a href="{{ url('/keuangan') }}" class="nav-link {{ Request::is('keuangan*') ? 'active' : '' }}">
        <i class="fa-solid fa-wallet"></i><span>Keuangan</span>
    </a>
    @endif

    <!-- Menu Khusus Admin/Organisasi/BPH -->
    @if($isBPH || $isOrganisasi)
    <a href="{{ url('/users') }}" class="nav-link {{ Request::is('users*') ? 'active' : '' }}">
        <i class="fa-solid fa-users"></i><span>Data Anggota</span>
    </a>
    @endif
    
    <!-- Tombol Logout -->
    <div class="mt-4 px-3">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger w-100 d-flex align-items-center justify-content-center gap-2 py-2 small">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </button>
        </form>
    </div>
</div>

<div class="sidebar-footer">
    <div class="info-card">
        <div class="info-header"><i class="fa-regular fa-building"></i> <span>INFO AKUN</span></div>
        <p class="user-role">{{ $user->nama_lengkap }}</p>
        <p class="user-desc">Role: {{ $labelJabatan }}</p>
    </div>
</div>