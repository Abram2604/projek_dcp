@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Pengaturan Tanda Tangan Laporan</h4>

    <form action="{{ route('ttd.update') }}" method="POST">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Kota Surat</label>
                    <input type="text" name="kota_surat" class="form-control"
                           value="{{ $ttd->kota_surat ?? 'Karawang' }}">
                </div>

                <hr>

                <div class="mb-3">
                    <label class="form-label">Nama Ketua</label>
                    <input type="text" name="ketua_nama" class="form-control"
                           value="{{ $ttd->ketua_nama ?? '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Sekretaris</label>
                    <input type="text" name="sekretaris_nama" class="form-control"
                           value="{{ $ttd->sekretaris_nama ?? '' }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Bendahara</label>
                    <input type="text" name="bendahara_nama" class="form-control"
                           value="{{ $ttd->bendahara_nama ?? '' }}">
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('laporan_keuangan.index') }}" class="btn btn-secondary">
                        ← Kembali
                    </a>

                    <button type="submit" class="btn btn-primary">
                        💾 Simpan Pengaturan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
