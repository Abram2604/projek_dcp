    @extends('layouts.app')
    @section('header_title', 'Laporan Keuangan')
    @section('header_subtitle', 'Laporan arus kas, posisi keuangan, dan rekapitulasi.')

    @section('content')

    <div class="container-fluid" id="appKeuangan">
        
        {{-- Notifikasi --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4">
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </div>
        @endif

        <!-- CONTROL BAR -->
        <div class="card border-0 shadow-sm mb-4 no-print">
            <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-3">
                
                <!-- Menu Tipe Laporan -->
                <div class="btn-group bg-light p-1 rounded-3">
                    <a href="?type=flow&bulan={{$bulan}}&tahun={{$tahun}}" 
                    class="btn btn-sm {{ $type == 'flow' ? 'btn-white shadow-sm fw-bold' : 'text-muted' }}">
                    Dana Masuk/Keluar
                    </a>
                    <a href="?type=position&bulan={{$bulan}}&tahun={{$tahun}}" 
                    class="btn btn-sm {{ $type == 'position' ? 'btn-white shadow-sm fw-bold' : 'text-muted' }}">
                    Posisi Keuangan
                    </a>
                    <a href="?type=summary&bulan={{$bulan}}&tahun={{$tahun}}" 
                    class="btn btn-sm {{ $type == 'summary' ? 'btn-white shadow-sm fw-bold' : 'text-muted' }}">
                    Laporan Organisasi
                    </a>
                </div>

                <!-- Navigasi Bulan -->
                <div class="d-flex align-items-center gap-3 bg-white border rounded-pill px-3 py-1">
                    <a href="?bulan={{ $bulan == 1 ? 12 : $bulan-1 }}&tahun={{ $bulan == 1 ? $tahun-1 : $tahun }}&type={{$type}}" class="text-indigo"><i class="fa-solid fa-chevron-left"></i></a>
                    <span class="fw-bold text-uppercase small" style="min-width: 120px; text-align: center;">
                        {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}
                    </span>
                    <a href="?bulan={{ $bulan == 12 ? 1 : $bulan+1 }}&tahun={{ $bulan == 12 ? $tahun+1 : $tahun }}&type={{$type}}" class="text-indigo"><i class="fa-solid fa-chevron-right"></i></a>
                </div>

                <!-- Tombol Aksi -->
                <div class="d-flex gap-2">
                    
                    {{-- TOMBOL EDIT (POP-UP) --}}
                    <button type="button" class="btn btn-primary btn-sm rounded-3 fw-bold text-white px-3" data-bs-toggle="modal" data-bs-target="#modalEditData">
                        <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                    </button>

                    {{-- TOMBOL ATUR TTD (POP-UP) --}}
                    <button type="button" class="btn btn-outline-primary btn-sm rounded-3 fw-bold px-3" data-bs-toggle="modal" data-bs-target="#modalTtd">
                        <i class="fas fa-signature me-1"></i> Atur TTD
                    </button>
                    
                    {{-- TOMBOL PDF --}}
                    <a href="{{ route('laporan_keuangan.pdf', ['bulan' => $bulan, 'tahun' => $tahun, 'type' => $type]) }}" target="_blank" class="btn btn-danger btn-sm rounded-3 fw-bold text-white px-3">
                        <i class="fa-solid fa-file-pdf me-1"></i> PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- AREA LAPORAN (READ ONLY / TAMPILAN SAJA) -->
        <div class="card border-0 shadow-sm p-4 p-md-5">
            <div class="text-center mb-4">
                <h4 class="fw-bold text-uppercase text-decoration-underline mb-1">
                    @if($type == 'flow') LAPORAN DANA MASUK DAN KELUAR 
                    @elseif($type == 'position') LAPORAN POSISI KEUANGAN 
                    @else LAPORAN KEUANGAN ORGANISASI @endif
                </h4>
                <p class="fw-bold text-uppercase text-muted small">BULAN {{ \Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }}</p>
            </div>

            {{-- Sertakan file partial untuk tampilan statis --}}
            {{-- Kita gunakan variabel $isPdf = false agar partial merender text biasa --}}
            <div class="table-responsive">
                <table class="table table-bordered align-middle small table-striped-columns">
                    @if($type == 'flow')
                        @include('pages.laporan_keuangan.partials.flow', ['readOnly' => true])
                    @elseif($type == 'position')
                        @include('pages.laporan_keuangan.partials.position', ['readOnly' => true])
                    @else
                        @include('pages.laporan_keuangan.partials.summary', ['readOnly' => true])
                    @endif
                </table>
            </div>

            {{-- Footer Tanda Tangan (Visual) --}}
            <div class="row mt-5 pt-4 text-center">
                <div class="col-4">
                    <p class="mb-5 fw-bold">Mengetahui,<br>KETUA</p>
                    <div class="d-flex justify-content-center" style="height: 60px;">
                        @if(!empty($ttd->path_ttd_ketua))
                            <img src="{{ asset('storage/'.$ttd->path_ttd_ketua) }}" style="max-height: 60px;">
                        @endif
                    </div>
                    <p class="fw-bold text-decoration-underline">{{ $ttd->ketua_nama ?? '....................' }}</p>
                </div>
                <div class="col-4">
                    <p class="mb-5 fw-bold"><br>SEKRETARIS</p>
                    <div class="d-flex justify-content-center" style="height: 60px;">
                        @if(!empty($ttd->path_ttd_sekretaris))
                            <img src="{{ asset('storage/'.$ttd->path_ttd_sekretaris) }}" style="max-height: 60px;">
                        @endif
                    </div>
                    <p class="fw-bold text-decoration-underline">{{ $ttd->sekretaris_nama ?? '....................' }}</p>
                </div>
                <div class="col-4">
                    <p class="mb-5 fw-bold">{{ $ttd->kota_surat ?? 'Karawang' }}, {{ date('d F Y') }}<br>BENDAHARA</p>
                    <div class="d-flex justify-content-center" style="height: 60px;">
                        @if(!empty($ttd->path_ttd_bendahara))
                            <img src="{{ asset('storage/'.$ttd->path_ttd_bendahara) }}" style="max-height: 60px;">
                        @endif
                    </div>
                    <p class="fw-bold text-decoration-underline">{{ $ttd->bendahara_nama ?? '....................' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL EDIT DATA (POP-UP) ================= -->
    <div class="modal fade" id="modalEditData" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Data Keuangan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <form id="formEditKeuangan" action="{{ route('laporan_keuangan.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="bulan" value="{{$bulan}}">
                        <input type="hidden" name="tahun" value="{{$tahun}}">
                        
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle small table-striped-columns" style="min-width: 700px;">
                                    @if($type == 'flow')
                                        @include('pages.laporan_keuangan.partials.flow', ['readOnly' => true])
                                    @elseif($type == 'position')
                                        @include('pages.laporan_keuangan.partials.position', ['readOnly' => true])
                                    @else
                                        @include('pages.laporan_keuangan.partials.summary', ['readOnly' => true])
                                    @endif
                                </table>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" onclick="document.getElementById('formEditKeuangan').submit()" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= MODAL ATUR TTD ================= -->
    <div class="modal fade" id="modalTtd" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Atur Tanda Tangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('laporan_keuangan.update_ttd') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- 1. PENGURUS DPC -->
                        <h6 class="text-primary fw-bold mb-3 mt-2">Pengurus DPC</h6>
                        
                        <!-- Ketua -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nama Ketua</label>
                            <input type="text" name="ketua_nama" class="form-control" value="{{ $ttd->ketua_nama ?? '' }}">
                            <div class="mt-2">
                                <label class="form-label small text-muted">Upload TTD Ketua (Opsional)</label>
                                <input type="file" name="ttd_ketua" class="form-control form-control-sm" accept="image/png, image/jpeg">
                            </div>
                        </div>

                        <!-- Sekretaris -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nama Sekretaris</label>
                            <input type="text" name="sekretaris_nama" class="form-control" value="{{ $ttd->sekretaris_nama ?? '' }}">
                            <div class="mt-2">
                                <label class="form-label small text-muted">Upload TTD Sekretaris (Opsional)</label>
                                <input type="file" name="ttd_sekretaris" class="form-control form-control-sm" accept="image/png, image/jpeg">
                            </div>
                        </div>

                        <!-- Bendahara -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Nama Bendahara</label>
                            <input type="text" name="bendahara_nama" class="form-control" value="{{ $ttd->bendahara_nama ?? '' }}">
                            <div class="mt-2">
                                <label class="form-label small text-muted">Upload TTD Bendahara (Opsional)</label>
                                <input type="file" name="ttd_bendahara" class="form-control form-control-sm" accept="image/png, image/jpeg">
                            </div>
                        </div>

                        <hr>

                        <!-- Kota Surat -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted">Kota Surat</label>
                            <input type="text" name="kota_surat" class="form-control" value="{{ $ttd->kota_surat ?? 'Karawang' }}">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT HITUNG DI MODAL --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Kalkulasi real-time hanya berjalan jika input ada (di dalam modal)
            const inputs = document.querySelectorAll('.input-money');
            
            function calculate() {
                // Hitung Flow
                let totalIncome = 0, totalExpense = 0;
                document.querySelectorAll('.calc-income').forEach(el => totalIncome += parseFloat(el.value || 0));
                document.querySelectorAll('.calc-expense').forEach(el => totalExpense += parseFloat(el.value || 0));
                
                const elTotalInc = document.getElementById('totalIncomeDisplay');
                const elTotalExp = document.getElementById('totalExpenseDisplay');
                const elSurplus = document.getElementById('surplusDisplay');

                if(elTotalInc) elTotalInc.value = totalIncome;
                if(elTotalExp) elTotalExp.value = totalExpense;
                if(elSurplus) elSurplus.value = totalIncome - totalExpense;

                // Hitung Position (Aset)
                let totalAsset = 0;
                document.querySelectorAll('.calc-asset').forEach(el => totalAsset += parseFloat(el.value || 0));
                const elTotalAsset = document.getElementById('totalAssetDisplay');
                if(elTotalAsset) elTotalAsset.value = totalAsset;
            }

            inputs.forEach(input => input.addEventListener('input', calculate));
            calculate(); // Run once
        });
    </script>
    @endsection