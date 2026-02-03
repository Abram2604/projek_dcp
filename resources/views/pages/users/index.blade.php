@extends('layouts.app')

@section('header_title', 'Manajemen User')
@section('header_subtitle', 'Kelola akun, hak akses, dan data personil.')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if(session('success'))
    <script>Swal.fire('Berhasil', '{{ session('success') }}', 'success');</script>
@endif
@if(session('error'))
    <script>Swal.fire('Gagal', '{{ session('error') }}', 'error');</script>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
       <!-- Gunakan justify-content-between agar Search di Kiri dan Tombol mentok di Kanan -->
<div class="d-flex justify-content-between align-items-center mb-4">
    
    <form action="{{ route('users.index') }}" method="GET" style="width: 300px; max-width: 80%;">
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" 
                   placeholder="Cari nama..." 
                   value="{{ request('search') }}">
        </div>
    </form>
    
    <!-- 2. TOMBOL TAMBAH -->
    <button class="btn btn-primary px-3 fw-bold text-nowrap shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="fa-solid fa-plus"></i> 
        <span class="d-none d-sm-inline ms-1">Tambah Anggota</span>
    </button>

</div>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light text-uppercase text-muted small">
                    <tr>
                        <th class="py-3 ps-3">Nama Lengkap</th>
                        <th class="py-3">Username</th>
                        <th class="py-3">Divisi / Peran</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- LOOPING DATA DARI DATABASE (Via Controller) --}}
                    @forelse($users as $user)
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle bg-indigo-100 text-indigo-700 fw-bold rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    {{ substr($user->nama_lengkap, 0, 1) }}
                                </div>
                                <span class="fw-bold text-dark">{{ $user->nama_lengkap }}</span>
                            </div>
                        </td>
                        <td class="text-muted">{{ $user->username }}</td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-dark">{{ $user->nama_jabatan }}</span>
                                <span class="small text-muted">{{ $user->nama_divisi ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            @if($user->status_aktif == 1)
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">Aktif</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Non-Aktif</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="d-flex justify-content-end gap-2">
                                
                                {{-- 1. TOMBOL QR CODE --}}
                                <button type="button" class="btn btn-sm btn-light text-primary" 
                                        onclick="bukaModalQr(
                                            '{{ $user->nama_lengkap }}', 
                                            '{{ $user->nama_jabatan }}', 
                                            '{{ $user->string_kode_qr }}', 
                                            '{{ $user->id }}'
                                        )"
                                        title="Lihat QR">
                                    <i class="fa-solid fa-qrcode"></i>
                                </button>

                                {{-- 2. TOMBOL RESET PASSWORD --}}
                                <button type="button" class="btn btn-sm btn-light text-warning"
                                        onclick="bukaModalReset('{{ $user->id }}', '{{ $user->nama_lengkap }}')"
                                        title="Reset Password">
                                    <i class="fa-solid fa-key"></i>
                                </button>
                                
                                {{-- 3. TOMBOL EDIT (Isi data ke Modal via JS Sederhana) --}}
                                <button type="button" class="btn btn-sm btn-light text-info"
                                        onclick="bukaModalEdit(this)"
                                        data-id="{{ $user->id }}"
                                        data-nama="{{ $user->nama_lengkap }}"
                                        data-username="{{ $user->username }}"
                                        data-email="{{ $user->email }}"
                                        data-hp="{{ $user->nomor_hp }}"
                                        data-jabatan="{{ $user->id_jabatan }}"
                                        data-divisi="{{ $user->id_divisi }}"
                                        title="Edit User">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                {{-- 4. TOMBOL HAPUS/NONAKTIF --}}
                                @if($user->status_aktif == 1)
                                    <button type="button" class="btn btn-sm btn-light text-danger" 
                                            onclick="konfirmasiAksi('{{ $user->id }}', '{{ $user->nama_lengkap }}', 'nonaktif')"
                                            title="Non-aktifkan">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                    {{-- Form Hidden untuk Delete --}}
                                    <form id="form-nonaktif-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                @else
                                    <button type="button" class="btn btn-sm btn-light text-success" 
                                            onclick="konfirmasiAksi('{{ $user->id }}', '{{ $user->nama_lengkap }}', 'aktif')"
                                            title="Aktifkan Kembali">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                    {{-- Form Hidden untuk Activate --}}
                                    <form id="form-aktif-{{ $user->id }}" action="{{ route('users.activate', $user->id) }}" method="POST" style="display: none;">
                                        @csrf @method('PUT')
                                    </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data anggota.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4 my-pagination">
            {{ $users->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Modal Tambah Anggota -->
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Tambah Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="small fw-bold text-muted">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap', 'create') is-invalid @enderror" 
                                value="{{ old('nama_lengkap') }}" required>
                            @error('nama_lengkap', 'create')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted">Username</label>
                            <input type="text" name="username" class="form-control @error('username', 'create') is-invalid @enderror" 
                                value="{{ old('username') }}" required>
                            @error('username', 'create')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- DROPDOWN JABATAN (Dari Database) -->
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Jabatan</label>
                            <select name="id_jabatan" class="form-select @error('id_jabatan', 'create') is-invalid @enderror" required>
                                <option value="">Pilih Jabatan...</option>
                                @foreach($jabatan as $j)
                                    <option value="{{ $j->id }}" {{ old('id_jabatan') == $j->id ? 'selected' : '' }}>{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                            @error('id_jabatan', 'create')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="small fw-bold text-muted">Divisi</label>
                            <select name="id_divisi" class="form-select">
                                <option value="">- Tidak Ada -</option>
                                @foreach($divisi as $d) 
                                    <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option> 
                                @endforeach
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="small fw-bold text-muted">Email</label>
                            <input type="email" name="email" class="form-control @error('email', 'create') is-invalid @enderror" 
                                value="{{ old('email') }}">
                            @error('email', 'create')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted">No. HP</label>
                            <input type="text" name="nomor_hp" class="form-control @error('nomor_hp', 'create') is-invalid @enderror" 
                                value="{{ old('nomor_hp') }}">
                            @error('nomor_hp', 'create')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Password Awal</label>
                            <input type="text" name="password" class="form-control @error('password', 'create') is-invalid @enderror" 
                                value="{{ old('password', 'lem_spsi_' . date('Y')) }}" required minlength="6">
                            @error('password', 'create')
                                <span class="text-danger small mt-1 d-block">{{ $message }}</span>
                            @enderror
                            <small class="text-muted">Minimal 6 karakter</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 2. Modal QR -->
<div class="modal fade" id="modalQr" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content rounded-4 border-0 bg-transparent shadow-none">
            <div class="qr-card mb-3">
                <div class="qr-header">
                    <div class="icon-box"><i class="fa-solid fa-user-shield"></i></div>
                    <h5 class="fw-bold mb-0 text-center" id="qrName">Nama</h5>
                    <p class="small text-uppercase text-indigo-300 mb-0" id="qrJabatan">JABATAN</p>
                </div>
                <div class="qr-body">
                    <div class="qr-frame">
                        <div id="qrContainer"></div>
                    </div>
                    <p class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.65rem;">ID ANGGOTA</p>
                    <p class="font-monospace fw-bold text-dark fs-5 mb-0" id="qrIdDisplay">DPC-XXXX</p>
                </div>
                <div class="qr-footer">
                    <p class="mb-0 text-uppercase fw-medium" style="font-size: 0.65rem; color: #c7d2fe;">DEWAN PIMPINAN CABANG</p>
                    <p class="mb-0 text-uppercase fw-bold text-white" style="font-size: 0.7rem;">FSP LEM SPSI KARAWANG</p>
                </div>
            </div>
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-light py-3 fw-bold rounded-4 shadow-sm" data-bs-dismiss="modal">Tutup</button>
                <form id="formGenerateQr" action="" method="POST" class="d-grid">
                    @csrf 
                    <button type="submit" class="btn btn-sm btn-link text-white text-decoration-none opacity-50">
                        <i class="fa-solid fa-arrows-rotate me-1"></i> Regenerate QR
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 3. Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Edit Anggota</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Nama</label>
                            <input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted">Username</label>
                            <input type="text" name="username" id="edit_username" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="small fw-bold text-muted">No. HP</label>
                            <input type="text" name="nomor_hp" id="edit_hp" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>
                        
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Jabatan</label>
                            <select name="id_jabatan" id="edit_jabatan" class="form-select" required>
                                @foreach($jabatan as $j) 
                                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option> 
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <label class="small fw-bold text-muted">Divisi</label>
                            <select name="id_divisi" id="edit_divisi" class="form-select">
                                <option value="">- Tidak Ada -</option>
                                @foreach($divisi as $d) 
                                    <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option> 
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 4. Modal Reset Password -->
<div class="modal fade" id="modalReset" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title fw-bold">Reset Password: <span id="resetName"></span></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formReset" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="user_name" id="resetUserName">
                <div class="modal-body">
                    <input type="password" name="new_password" class="form-control @error('new_password', 'reset') is-invalid @enderror" 
                        placeholder="Password Baru" 
                        value="{{ old('new_password') }}" required minlength="6">
                    @error('new_password', 'reset')
                        <span class="text-danger small mt-2 d-block fw-medium">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>{{ $message }}
                        </span>
                    @enderror
                    <small class="text-muted mt-1 d-block">Password minimal 6 karakter</small>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="btn btn-warning w-100 text-white">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JAVASCRIPT: HANYA UNTUK UI INTERACTION (MODAL) --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cek error untuk form Tambah Anggota
        @if ($errors->hasBag('create') && $errors->create->any())
            const modalCreate = new bootstrap.Modal(document.getElementById('modalCreate'));
            modalCreate.show();
            
            // Scroll ke field pertama yang error
            const firstError = document.querySelector('#modalCreate .is-invalid');
            if (firstError) {
                setTimeout(() => {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }, 300);
            }
        @endif

        // Cek error untuk form Reset Password
        @if ($errors->hasBag('reset') && $errors->reset->any())
            const userId = '{{ session('reset_user_id') }}';
            const userName = '{{ session('reset_user_name') }}';
            
            if(userId && userName) {
                // Set ulang action form dan nama user
                document.getElementById('formReset').action = `/users/${userId}/reset-password`;
                document.getElementById('resetName').innerText = userName;
                document.getElementById('resetUserName').value = userName;
                
                // Buka modal
                const modalReset = new bootstrap.Modal(document.getElementById('modalReset'));
                modalReset.show();
                
                // Fokus ke input password
                setTimeout(() => {
                    const passwordInput = document.querySelector('#modalReset input[name="new_password"]');
                    if (passwordInput) {
                        passwordInput.focus();
                    }
                }, 300);
            }
        @endif
    });

    // 1. Fungsi Buka Modal QR
    function bukaModalQr(nama, jabatan, qrString, id) {
        document.getElementById('qrName').innerText = nama;
        document.getElementById('qrJabatan').innerText = jabatan;
        
        const year = new Date().getFullYear();
        const idFormatted = id.toString().padStart(3, '0'); 
        document.getElementById('qrIdDisplay').innerText = `DPC-${year}-${idFormatted}`;

        // Set Action URL form
        document.getElementById('formGenerateQr').action = `/users/${id}/generate-qr`;
        
        const container = document.getElementById('qrContainer');
        if(qrString && qrString !== 'null' && qrString !== '') {
            // Render QR Image
            const url = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${encodeURIComponent(qrString)}`;
            container.innerHTML = `<img src="${url}" alt="QR Code" class="img-fluid" style="width: 140px; height: 140px;">`;
        } else {
            container.innerHTML = '<span class="text-danger fw-bold small">Belum ada QR</span>';
        }
        
        new bootstrap.Modal(document.getElementById('modalQr')).show();
    }

    // 2. Fungsi Buka Modal Reset
    function bukaModalReset(id, nama) {
        document.getElementById('resetName').innerText = nama;
        document.getElementById('resetUserName').value = nama;
        document.getElementById('formReset').action = `/users/${id}/reset-password`;
        
        // Reset error messages
        const errorEl = document.querySelector('#modalReset .text-danger');
        if (errorEl) errorEl.remove();
        
        const input = document.querySelector('#modalReset input[name="new_password"]');
        if (input) {
            input.value = '';
            input.classList.remove('is-invalid');
        }
        
        new bootstrap.Modal(document.getElementById('modalReset')).show();
    }

    // 3. Fungsi Buka Modal Edit
    function bukaModalEdit(element) {
        // Ambil data langsung dari atribut HTML tombol (Server-Side Rendered Data)
        const id = element.getAttribute('data-id');
        const nama = element.getAttribute('data-nama');
        const username = element.getAttribute('data-username');
        const email = element.getAttribute('data-email');
        const hp = element.getAttribute('data-hp');
        const jabatan = element.getAttribute('data-jabatan');
        const divisi = element.getAttribute('data-divisi');

        // Isi form
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = (email === 'null') ? '' : email;
        document.getElementById('edit_hp').value = (hp === 'null') ? '' : hp;
        document.getElementById('edit_jabatan').value = jabatan;
        document.getElementById('edit_divisi').value = divisi;

        // Set action form update
        document.getElementById('formEdit').action = `/users/${id}`;

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    // 4. Fungsi Konfirmasi Aksi (Nonaktif/Aktif)
    function konfirmasiAksi(id, nama, jenis) {
        let judul = (jenis === 'nonaktif') ? 'Non-aktifkan User?' : 'Aktifkan Kembali?';
        let warna = (jenis === 'nonaktif') ? '#d33' : '#10b981';
        let formId = (jenis === 'nonaktif') ? `form-nonaktif-${id}` : `form-aktif-${id}`;

        Swal.fire({
            title: judul,
            text: nama,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: warna,
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endsection