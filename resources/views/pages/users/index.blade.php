@extends('layouts.app')

@section('header_title', 'Manajemen User')
@section('header_subtitle', 'Kelola akun, hak akses, dan data personil.')

@section('content')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4">
        
        <!-- Header Tools -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 mb-4">
            <form action="{{ route('users.index') }}" method="GET" class="w-100" style="max-width: 400px;">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Cari nama atau username..." value="{{ request('search') }}">
                </div>
            </form>
            
            <button class="btn btn-primary px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#modalCreate">
                <i class="fa-solid fa-plus me-2"></i> Tambah Anggota
            </button>
        </div>

        <!-- Table -->
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
                                            '{{ $user->nama_jabatan }}',  {{-- Tambahkan parameter Jabatan --}}
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
                                
                                {{-- 3. TOMBOL EDIT (Data ditaruh di attribute agar aman) --}}
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

                                {{-- 4. TOMBOL NON-AKTIF / AKTIFKAN --}}
                                @if($user->status_aktif == 1)
                                    {{-- Tombol Matikan --}}
                                    <button type="button" class="btn btn-sm btn-light text-danger" 
                                            onclick="konfirmasiAksi('{{ $user->id }}', '{{ $user->nama_lengkap }}', 'nonaktif')"
                                            title="Non-aktifkan">
                                        <i class="fa-solid fa-ban"></i>
                                    </button>
                                    <form id="form-nonaktif-{{ $user->id }}" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: none;">
                                        @csrf @method('DELETE')
                                    </form>
                                @else
                                    {{-- Tombol Hidupkan Lagi --}}
                                    <button type="button" class="btn btn-sm btn-light text-success" 
                                            onclick="konfirmasiAksi('{{ $user->id }}', '{{ $user->nama_lengkap }}', 'aktif')"
                                            title="Aktifkan Kembali">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                    <form id="form-aktif-{{ $user->id }}" action="{{ route('users.activate', $user->id) }}" method="POST" style="display: none;">
                                        @csrf @method('PUT')
                                    </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Modal Create -->
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
                        <div class="col-6"><label class="small fw-bold text-muted">Nama Lengkap</label><input type="text" name="nama_lengkap" class="form-control" required></div>
                        <div class="col-6"><label class="small fw-bold text-muted">Username</label><input type="text" name="username" class="form-control" required></div>
                        <div class="col-12"><label class="small fw-bold text-muted">Jabatan</label>
                            <select name="id_jabatan" class="form-select" required>
                                <option value="">Pilih...</option>
                                @foreach($jabatan as $j) <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-12"><label class="small fw-bold text-muted">Divisi</label>
                            <select name="id_divisi" class="form-select">
                                <option value="">- Tidak Ada -</option>
                                @foreach($divisi as $d) <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-6"><label class="small fw-bold text-muted">Email</label><input type="email" name="email" class="form-control"></div>
                        <div class="col-6"><label class="small fw-bold text-muted">No. HP</label><input type="text" name="nomor_hp" class="form-control"></div>
                        <div class="col-12"><label class="small fw-bold text-muted">Password Awal</label><input type="text" name="password" class="form-control" value="lem_spsi_{{ date('Y') }}" required></div>
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
<!-- 2. Modal Lihat QR Code (Desain Baru) -->
<div class="modal fade" id="modalQr" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
        <div class="modal-content rounded-4 border-0 bg-transparent shadow-none">
            
            <!-- KARTU UTAMA -->
            <div class="qr-card mb-3">
                
                <!-- HEADER: Icon & Nama -->
                <div class="qr-header">
                    <div class="icon-box">
                        <!-- Icon Shield (Ganti dengan <img> logo jika mau) -->
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-center" id="qrName">Nama Anggota</h5>
                    <p class="small text-uppercase text-indigo-300 mb-0" style="color: #a5b4fc; letter-spacing: 2px; font-size: 0.75rem;" id="qrJabatan">JABATAN</p>
                </div>

                <!-- BODY: Gambar QR -->
                <div class="qr-body">
                    <div class="qr-frame">
                        <div id="qrContainer">
                            <!-- Gambar QR akan masuk sini -->
                        </div>
                    </div>
                    
                    <p class="text-uppercase fw-bold text-muted mb-1" style="font-size: 0.65rem; letter-spacing: 0.1em;">ID ANGGOTA</p>
                    <p class="font-monospace fw-bold text-dark fs-5 mb-0" id="qrIdDisplay">DPC-XXXX-XXX</p>
                </div>

                <!-- FOOTER: Info Organisasi -->
                <div class="qr-footer">
                    <p class="mb-0 text-uppercase fw-medium" style="font-size: 0.65rem; color: #c7d2fe;">DEWAN PIMPINAN CABANG</p>
                    <p class="mb-0 text-uppercase fw-bold text-white" style="font-size: 0.7rem;">FSP LEM SPSI KARAWANG</p>
                </div>
            </div>

            <!-- TOMBOL TUTUP & REGENERATE -->
            <div class="d-grid gap-2">
                <button type="button" class="btn btn-light py-3 fw-bold rounded-4 shadow-sm" data-bs-dismiss="modal">
                    Tutup
                </button>
                
                <!-- Tombol Regenerate (Opsional/Disembunyikan jika tidak perlu sering dipakai) -->
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
                        <div class="col-12"><label class="small fw-bold text-muted">Nama</label><input type="text" name="nama_lengkap" id="edit_nama" class="form-control" required></div>
                        <div class="col-6"><label class="small fw-bold text-muted">Username</label><input type="text" name="username" id="edit_username" class="form-control" required></div>
                        <div class="col-6"><label class="small fw-bold text-muted">No. HP</label><input type="text" name="nomor_hp" id="edit_hp" class="form-control"></div>
                        <div class="col-12"><label class="small fw-bold text-muted">Email</label><input type="email" name="email" id="edit_email" class="form-control"></div>
                        <div class="col-12"><label class="small fw-bold text-muted">Jabatan</label>
                            <select name="id_jabatan" id="edit_jabatan" class="form-select" required>
                                @foreach($jabatan as $j) <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option> @endforeach
                            </select>
                        </div>
                        <div class="col-12"><label class="small fw-bold text-muted">Divisi</label>
                            <select name="id_divisi" id="edit_divisi" class="form-select">
                                <option value="">- Tidak Ada -</option>
                                @foreach($divisi as $d) <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option> @endforeach
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
                <div class="modal-body">
                    <input type="password" name="new_password" class="form-control" placeholder="Password Baru" required>
                </div>
                <div class="modal-footer border-top-0">
                    <button class="btn btn-warning w-100 text-white">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JAVASCRIPT MANUAL (Global Scope) --}}
<script>
    // 1. Fungsi Buka Modal QR
    window.bukaModalQr = function(nama, jabatan, qrString, id) {
        // 1. Set Data Teks
        document.getElementById('qrName').innerText = nama;
        document.getElementById('qrJabatan').innerText = jabatan;
        
        // 2. Format ID Anggota (Contoh: DPC-2026-001)
        // Kita ambil tahun sekarang
        const year = new Date().getFullYear();
        // Pad ID dengan nol di depan (misal id 5 jadi 005)
        const idFormatted = id.toString().padStart(3, '0'); 
        document.getElementById('qrIdDisplay').innerText = `DPC-${year}-${idFormatted}`;

        // 3. Set Action Form Regenerate
        document.getElementById('formGenerateQr').action = `/users/${id}/generate-qr`;
        
        // 4. Render Gambar QR
        const container = document.getElementById('qrContainer');
        if(qrString && qrString !== 'null' && qrString !== '') {
            // Gunakan API QR Server
            const url = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${qrString}`;
            container.innerHTML = `<img src="${url}" alt="QR Code" class="img-fluid" style="width: 140px; height: 140px;">`;
        } else {
            container.innerHTML = '<span class="text-danger fw-bold small">Belum ada QR</span>';
        }
        
        // 5. Tampilkan Modal
        new bootstrap.Modal(document.getElementById('modalQr')).show();
    }

    // 2. Fungsi Buka Modal Reset
    window.bukaModalReset = function(id, nama) {
        document.getElementById('resetName').innerText = nama;
        document.getElementById('formReset').action = `/users/${id}/reset-password`;
        new bootstrap.Modal(document.getElementById('modalReset')).show();
    }

    // 3. Fungsi Buka Modal Edit (AMBIL DATA DARI ATRIBUT)
    window.bukaModalEdit = function(element) {
        // Ambil data dari atribut tombol yang diklik
        const id = element.getAttribute('data-id');
        const nama = element.getAttribute('data-nama');
        const username = element.getAttribute('data-username');
        const email = element.getAttribute('data-email');
        const hp = element.getAttribute('data-hp');
        const jabatan = element.getAttribute('data-jabatan');
        const divisi = element.getAttribute('data-divisi');

        // Masukkan ke dalam input form modal
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = (email === 'null') ? '' : email;
        document.getElementById('edit_hp').value = (hp === 'null') ? '' : hp;
        document.getElementById('edit_jabatan').value = jabatan;
        document.getElementById('edit_divisi').value = divisi;

        // Update URL Action Form
        document.getElementById('formEdit').action = `/users/${id}`;

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    // 4. Fungsi Konfirmasi Aksi (Nonaktif / Aktifkan)
    window.konfirmasiAksi = function(id, nama, jenis) {
        let judul, teks, warnaBtn, formId;

        if (jenis === 'nonaktif') {
            judul = 'Non-aktifkan User?';
            teks = `User "${nama}" tidak akan bisa login lagi.`;
            warnaBtn = '#d33';
            formId = `form-nonaktif-${id}`;
        } else {
            judul = 'Aktifkan Kembali?';
            teks = `User "${nama}" akan bisa login kembali.`;
            warnaBtn = '#10b981'; // Hijau
            formId = `form-aktif-${id}`;
        }

        Swal.fire({
            title: judul,
            text: teks,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: warnaBtn,
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