<div class="d-flex justify-content-between align-items-center mb-5">
    <div class="d-flex align-items-center gap-3">
        <!-- Tombol Mobile Toggle -->
        <button id="sidebarToggle" class="btn btn-light shadow-sm d-lg-none">
            <i class="fa-solid fa-bars"></i>
        </button>
        <div>
            <h4 class="fw-bold text-dark m-0">@yield('header_title')</h4>
            <small class="text-muted">@yield('header_subtitle')</small>
        </div>
    </div>

    <!-- Profil Dropdown -->
    <div class="dropdown">
        <button class="btn bg-white shadow-sm border-0 rounded-pill py-2 px-3 d-flex align-items-center gap-2" data-bs-toggle="dropdown">
            <div class="text-end d-none d-md-block lh-sm">
                <div class="fw-bold small">{{ Auth::user()->nama_lengkap ?? 'User' }}</div>
                <div class="text-muted" style="font-size: 10px;">{{ Auth::user()->username }}</div>
            </div>
            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->nama_lengkap ?? 'User' }}&background=4f46e5&color=fff" class="rounded-circle" width="35">
        </button>
        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
            <li>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                </form>
            </li>
        </ul>
    </div>
</div>