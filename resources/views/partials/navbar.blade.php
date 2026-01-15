<div class="d-flex justify-content-between align-items-center mb-5">
    <div class="d-flex align-items-center gap-3">
        <!-- Tombol Mobile Toggle -->
        <button id="sidebarToggle" class="btn btn-light d-lg-none shadow-sm border-0 me-3">
            <i class="fa-solid fa-bars text-primary fa-lg"></i>
        </button>
        <div>
            <h4 class="fw-bold text-dark m-0">@yield('header_title')</h4>
            <small class="text-muted">@yield('header_subtitle')</small>
        </div>
    </div>

    <!-- Profil Dropdown -->
    <div class="dropdown">
        <button class="btn bg-white shadow-sm border-0 rounded-pill py-2 px-3 d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            
            <div class="text-end d-none d-md-block lh-sm">
                {{-- PERBAIKAN 1: Nama Dinamis --}}
                <div class="fw-bold small">{{ Auth::user()->nama_lengkap ?? 'User' }}</div>
                
                {{-- PERBAIKAN 2: Jabatan Dinamis dari Session --}}
                <div class="text-muted" style="font-size: 10px;">
                    {{ session('user_jabatan') ?? 'Anggota' }}
                </div>
            </div>

            {{-- Avatar Dinamis --}}
            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap ?? 'User') }}&background=4f46e5&color=fff" class="rounded-circle" width="35">
        </button>
        
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
            <!-- Header Mobile (Hanya muncul di HP) -->
            <li class="d-md-none px-3 py-2 text-center border-bottom mb-2">
                <div class="fw-bold">{{ Auth::user()->nama_lengkap }}</div>
                <small class="text-muted">{{ session('user_jabatan') }}</small>
            </li>

            <!-- Menu Profil -->
            <li>
                <a class="dropdown-item py-2" href="{{ route('profile') }}">
                    <i class="fa-regular fa-id-card me-2 text-primary"></i> Profil Saya
                </a>
            </li>
            
            <li><hr class="dropdown-divider"></li>

            <!-- Tombol Logout -->
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger py-2">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</div>