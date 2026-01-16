<div class="d-flex justify-content-between align-items-center mb-5">
    
    <!-- Bagian Kiri: Tombol Mobile & Judul -->
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

    <!-- Bagian Kanan: Notifikasi & Profil -->
    <div class="d-flex align-items-center gap-3">

        <!-- 1. DROPDOWN NOTIFIKASI (BAGIAN BARU) -->
        <div class="dropdown">
            <button class="btn btn-white shadow-sm border-0 rounded-circle position-relative d-flex align-items-center justify-content-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 40px; height: 40px; background: white;">
                <i class="fa-regular fa-bell text-secondary fa-lg"></i>
                
                {{-- Badge Merah --}}
                @if(isset($navbar_unread) && $navbar_unread > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-white" style="font-size: 0.6rem;">
                        {{ $navbar_unread > 99 ? '99+' : $navbar_unread }}
                    </span>
                @endif
            </button>
            
            <div class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0 overflow-hidden" style="width: 320px;">
                <div class="p-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-dark">Notifikasi</h6>
                    <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $navbar_unread ?? 0 }} Baru</span>
                </div>

                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @if(isset($navbar_notif) && count($navbar_notif) > 0)
                        @foreach($navbar_notif as $notif)
                            <a href="{{ $notif->link_url ?? '#' }}" class="list-group-item list-group-item-action p-3 {{ $notif->is_read == 0 ? 'bg-indigo-50' : '' }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="mt-1">
                                        @if($notif->tipe == 'success') <i class="fa-solid fa-circle-check text-success"></i>
                                        @elseif($notif->tipe == 'alert') <i class="fa-solid fa-circle-exclamation text-danger"></i>
                                        @elseif($notif->tipe == 'warning') <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                        @else <i class="fa-solid fa-circle-info text-primary"></i> @endif
                                    </div>
                                    <div>
                                        <p class="mb-1 small fw-bold text-dark">{{ $notif->judul }}</p>
                                        <p class="mb-1 small text-muted text-truncate" style="max-width: 200px;">{{ $notif->pesan }}</p>
                                        <small class="text-xs text-muted">{{ \Carbon\Carbon::parse($notif->dibuat_pada)->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <div class="text-center p-4 text-muted">
                            <i class="fa-regular fa-bell-slash mb-2 fa-lg"></i>
                            <p class="small mb-0">Tidak ada notifikasi baru</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- AKHIR NOTIFIKASI -->

        <!-- 2. Profil Dropdown -->
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
</div>