@extends('layouts.app')
@section('title', 'Manajemen User')
@section('header_title', 'Data Anggota')
@section('header_subtitle', 'Kelola akun dan data anggota DPC.')

@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <div class="input-group" style="width: 250px;">
            <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
            <input type="text" class="form-control bg-light border-start-0" placeholder="Cari anggota...">
        </div>
        <button class="btn btn-primary rounded-pill px-4">
            <i class="fa-solid fa-user-plus me-2"></i> Tambah Anggota
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Nama Anggota</th>
                        <th>Jabatan / Divisi</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=Budi" class="rounded-circle me-3" width="35">
                                <span class="fw-bold text-dark">Budi Santoso</span>
                            </div>
                        </td>
                        <td>Organisasi</td>
                        <td><span class="badge bg-success">Aktif</span></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-light text-primary" title="Edit"><i class="fa-solid fa-pen-to-square"></i></button>
                            <button class="btn btn-sm btn-light text-danger" title="Hapus"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection