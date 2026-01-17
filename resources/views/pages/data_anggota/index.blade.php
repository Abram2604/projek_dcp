@extends('layouts.app')

@section('title', 'Database PUK')
@section('header_title', 'Database Anggota (PUK)')
@section('header_subtitle', 'Manajemen verifikasi data unit kerja DPC FSP LEM SPSI Karawang.')

@section('content')

@if(session('success'))
    <div class="alert alert-success d-flex align-items-center rounded-3 mb-4 no-print" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

<style>
    /* Print Style: Layout Presisi */
    @media print {
        @page { size: landscape; margin: 0.5cm; }
        body { -webkit-print-color-adjust: exact; font-family: 'Arial', sans-serif; }
        body * { visibility: hidden; }
        #printable-area, #printable-area * { visibility: visible; }
        #printable-area { position: absolute; left: 0; top: 0; width: 100%; }
        .no-print { display: none !important; }
        .table-print { width: 100%; border-collapse: collapse; font-size: 9px; margin-bottom: 20px; }
        .table-print th, .table-print td { border: 1px solid #000; padding: 4px; }
        .table-print th { background-color: #f0f0f0 !important; text-align: center; font-weight: bold; text-transform: uppercase; }
        .print-header { text-align: center; margin-bottom: 15px; text-transform: uppercase; font-weight: bold; font-size: 14px; text-decoration: underline; }
        .sig-table { width: 100%; border: none !important; margin-top: 30px; page-break-inside: avoid; }
        .sig-table td { border: none !important; padding: 0; vertical-align: top; text-align: center; }
        .sig-space { height: 70px; }
        .fw-bold { font-weight: bold; }
        .text-underline { text-decoration: underline; }
    }
</style>

<!-- TOOLS HEADER -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3 no-print">
    <form action="{{ route('data_anggota.index') }}" method="GET" class="flex-grow-1 w-100" style="max-width: 400px;">
        <div class="input-group shadow-sm">
            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari PUK, Federasi, Pengurus..." value="{{ $search }}">
        </div>
    </form>

    <div class="d-flex flex-wrap gap-2">
        @if($canEdit)
            <button class="btn btn-outline-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTtd">
                <i class="fa-solid fa-pen-nib me-2"></i> Atur TTD
            </button>
        @endif
        
        <button onclick="window.print()" class="btn btn-white border text-danger fw-bold shadow-sm">
            <i class="fa-solid fa-print me-2"></i> Cetak PDF
        </button>
        
        <button onclick="exportToExcel()" class="btn btn-white border text-success fw-bold shadow-sm">
            <i class="fa-solid fa-file-excel me-2"></i> Export Excel
        </button>
        
        @if($canEdit)
            <button class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="fa-solid fa-plus me-2"></i> Tambah PUK
            </button>
        @endif
    </div>
</div>

<!-- MAIN TABLE CONTAINER -->
<div class="card border-0 shadow-sm rounded-0 p-3" id="printable-area">
    
    <!-- JUDUL LAPORAN (PRINT ONLY) -->
    <div class="d-none d-print-block print-header">
        DAFTAR NAMA ANGGOTA DPC FSP LEM SPSI KARAWANG TAHUN {{ date('Y') }}
    </div>

    <div class="table-responsive">
        <table class="table-print table table-bordered align-middle mb-0" id="tableData">
            <thead>
                <tr class="bg-light fw-bold text-center align-middle" style="font-size: 11px;">
                    <th rowspan="2" width="30">NO</th>
                    <th rowspan="2">NAMA FEDERASI</th>
                    <th rowspan="2">NOMOR BUKTI<br>PENCATATAN<br>FEDERASI</th>
                    <th rowspan="2">SERIKAT PEKERJA/<br>SERIKAT BURUH</th>
                    <th rowspan="2">NOMOR BUKTI<br>PENCATATAN PUK</th>
                    <th rowspan="2" width="60">JML<br>ANGGOTA<br>SP/SB</th>
                    <th rowspan="2" width="60">JML HASIL<br>VERIFIKASI</th>
                    <th rowspan="2" width="60">TOTAL<br>ANGGOTA<br>SP/SB</th>
                    <th rowspan="2">NAMA AFILIASI<br>KONFEDERASI</th>
                    <th colspan="2">NAMA PENGURUS</th>
                    <th rowspan="2" class="no-print" width="80">AKSI</th>
                </tr>
                <tr class="bg-light fw-bold text-center" style="font-size: 11px;">
                    <th>KETUA</th>
                    <th>SEKRETARIS</th>
                </tr>
            </thead>

                <!-- DATA DARI DATABASE -->
                @forelse($dataPuk as $index => $p)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $p->nama_federasi }}</td>
                    <td>{{ $p->no_pencatatan_federasi }}</td>
                    <td>{{ $p->nama_perusahaan }}</td>
                    <td>{{ $p->no_pencatatan }}</td>
                    <td class="text-center fw-bold">{{ $p->jumlah_anggota }}</td>
                    <td class="text-center">{{ $p->hasil_verifikasi > 0 ? $p->hasil_verifikasi : '' }}</td>
                    
                    {{-- TOTAL ANGGOTA (MANUAL) --}}
                    <td class="text-center bg-light fw-bold text-primary">
                        {{ $p->manual_total_anggota ? number_format($p->manual_total_anggota, 0, ',', '.') : '' }}
                    </td> 
                    
                    <td class="text-center">{{ $p->afiliasi }}</td>
                    <td>{{ $p->nama_ketua }}</td>
                    <td>{{ $p->nama_sekretaris }}</td>
                    
                    {{-- TOMBOL AKSI (NON-PRINT) --}}
                    <td class="text-center no-print">
                        @if($canEdit)
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm btn-light text-primary" 
                                        onclick='editPuk(@json($p))' title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <form action="{{ route('data_anggota.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light text-danger" title="Hapus">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center py-4 text-muted no-print">Data belum tersedia.</td>
                </tr>
                @endforelse

                <!-- FOOTER TOTAL -->
                <tr style="font-weight: bold; background-color: #f8f9fa;">
                    <td colspan="5" class="text-end px-3">JUMLAH TOTAL ANGGOTA SP/SB TERDAFTAR</td>
                    <td class="text-center">{{ number_format($totalAnggota, 0, ',', '.') }}</td>
                    <td></td>
                    <td class="text-center">{{ number_format($totalAnggota, 0, ',', '.') }}</td>
                    <td colspan="3" class="text-center">KSPSI</td>
                    <td class="no-print"></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- TANDA TANGAN -->
    <div class="d-none d-print-block">
        <table class="sig-table">
            <tr>
                <td width="40%">
                    <p>Mengetahui,</p>
                    <p class="fw-bold">KEPALA DINAS TENAGA KERJA<br>KABUPATEN KARAWANG</p>
                    <div class="sig-space"></div>
                    <p class="fw-bold text-underline">{{ $ttd->kadis_nama }}</p>
                    <p>Pembina TK. I / NIP : {{ $ttd->kadis_nip }}</p>
                </td>
                <td width="10%"></td>
                <td width="50%">
                    <p>{{ $ttd->kota_surat }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</p>
                    <p class="fw-bold">DPC FSP LEM SPSI KARAWANG</p>
                    <div class="sig-space"></div>
                    <table style="width: 100%; border: none;">
                        <tr>
                            <td style="border: none; text-align: center; width: 50%;">
                                <p class="fw-bold">KETUA</p>
                                <br><br>
                                <p class="fw-bold text-underline">{{ $ttd->ketua_nama }}</p>
                            </td>
                            <td style="border: none; text-align: center; width: 50%;">
                                <p class="fw-bold">SEKRETARIS</p>
                                <br><br>
                                <p class="fw-bold text-underline">{{ $ttd->sekretaris_nama }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</div>

<!-- ================= MODALS ================= -->
@if($canEdit)
<!-- Modal TTD -->
<div class="modal fade" id="modalTtd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('data_anggota.ttd') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title fw-bold">Atur Tanda Tangan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <h6 class="text-primary fw-bold">1. Disnaker</h6>
                    <div class="mb-2"><label>Nama Kadis</label><input type="text" name="kadis_nama" class="form-control" value="{{ $ttd->kadis_nama }}"></div>
                    <div class="mb-3"><label>NIP</label><input type="text" name="kadis_nip" class="form-control" value="{{ $ttd->kadis_nip }}"></div>
                    <h6 class="text-primary fw-bold">2. DPC SPSI</h6>
                    <div class="mb-2"><label>Nama Ketua</label><input type="text" name="ketua_nama" class="form-control" value="{{ $ttd->ketua_nama }}"></div>
                    <div class="mb-2"><label>Nama Sekretaris</label><input type="text" name="sekretaris_nama" class="form-control" value="{{ $ttd->sekretaris_nama }}"></div>
                    <div class="mb-2"><label>Kota</label><input type="text" name="kota_surat" class="form-control" value="{{ $ttd->kota_surat }}"></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary w-100">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Add/Edit PUK -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Tambah Data PUK</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formPuk" action="{{ route('data_anggota.store') }}" method="POST">
                @csrf
                <div id="methodPut"></div> 
                <div class="modal-body p-4 bg-light">
                    <!-- Form Input -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="text-primary fw-bold">1. Data Federasi</h6>
                            <div class="row g-2">
                                <div class="col-md-6"><label class="small">Nama Federasi</label><input type="text" name="nama_federasi" id="inp_federasi" class="form-control" value="FSP LEM SPSI"></div>
                                <div class="col-md-6"><label class="small">No. Pencatatan</label><input type="text" name="no_pencatatan_federasi" id="inp_no_federasi" class="form-control"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <h6 class="text-primary fw-bold">2. Data PUK (Perusahaan)</h6>
                            <div class="row g-2">
                                <div class="col-md-6"><label class="small fw-bold">Nama Perusahaan (PUK) *</label><input type="text" name="nama_perusahaan" id="inp_perusahaan" class="form-control fw-bold" required></div>
                                <div class="col-md-6"><label class="small">No. Pencatatan PUK</label><input type="text" name="no_pencatatan" id="inp_no_puk" class="form-control"></div>
                                <div class="col-md-4"><label class="small fw-bold text-primary">Jml Anggota *</label><input type="number" name="jumlah_anggota" id="inp_jumlah" class="form-control fw-bold" required></div>
                                <div class="col-md-4"><label class="small">Hasil Verifikasi</label><input type="number" name="hasil_verifikasi" id="inp_verif" class="form-control"></div>
                                <div class="col-md-4"><label class="small">Afiliasi</label><select name="afiliasi" id="inp_afiliasi" class="form-select"><option value="KSPSI">KSPSI</option><option value="LAINNYA">Lainnya</option></select></div>
                                
                                {{-- INPUT TAMBAHAN: TOTAL ANGGOTA MANUAL --}}
                                <div class="col-md-12">
                                    <label class="small fw-bold text-muted">Total Anggota SP/SB (Opsional)</label>
                                    <input type="number" name="manual_total_anggota" id="inp_total_manual" class="form-control bg-light" placeholder="Isi hanya jika perlu (misal data Induk)">
                                    <small class="text-muted" style="font-size: 10px;">Biarkan kosong jika tidak ada data khusus.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="text-primary fw-bold">3. Pengurus PUK</h6>
                            <div class="row g-2">
                                <div class="col-md-6"><label class="small fw-bold">Nama Ketua PUK</label><input type="text" name="nama_ketua" id="inp_ketua" class="form-control"></div>
                                <div class="col-md-6"><label class="small fw-bold">Nama Sekretaris PUK</label><input type="text" name="nama_sekretaris" id="inp_sekre" class="form-control"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-white">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- JAVASCRIPT EXPORT & EDIT --}}
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
<script>
    // 1. EDIT PUK
    function editPuk(data) {
        document.getElementById('modalTitle').innerText = 'Edit Data PUK';
        document.getElementById('formPuk').action = `/data-anggota/${data.id}`;
        document.getElementById('methodPut').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        // Isi Form
        document.getElementById('inp_federasi').value = data.nama_federasi;
        document.getElementById('inp_no_federasi').value = data.no_pencatatan_federasi;
        document.getElementById('inp_perusahaan').value = data.nama_perusahaan;
        document.getElementById('inp_no_puk').value = data.no_pencatatan;
        document.getElementById('inp_jumlah').value = data.jumlah_anggota;
        document.getElementById('inp_verif').value = data.hasil_verifikasi;
        document.getElementById('inp_afiliasi').value = data.afiliasi;
        document.getElementById('inp_ketua').value = data.nama_ketua;
        document.getElementById('inp_sekre').value = data.nama_sekretaris;
        
        // Isi Total Manual
        document.getElementById('inp_total_manual').value = data.manual_total_anggota;

        new bootstrap.Modal(document.getElementById('modalAdd')).show();
    }

    // 2. RESET MODAL
    const modalAdd = document.getElementById('modalAdd');
    if(modalAdd) {
        modalAdd.addEventListener('hidden.bs.modal', event => {
            document.getElementById('modalTitle').innerText = 'Tambah Data PUK';
            document.getElementById('formPuk').action = "{{ route('data_anggota.store') }}";
            document.getElementById('methodPut').innerHTML = '';
            document.getElementById('formPuk').reset();
        });
    }

    // 3. EXPORT EXCEL
    function exportToExcel() {
        var wb = XLSX.utils.book_new();
        
        // Ambil Data Tabel
        var table = document.getElementById("tableData");
        var ws = XLSX.utils.table_to_sheet(table);

        // Hapus Kolom Aksi
        const range = XLSX.utils.decode_range(ws['!ref']);
        for (let R = range.s.r; R <= range.e.r; ++R) {
            let C = 11; // Kolom L (Aksi)
            let cellRef = XLSX.utils.encode_cell({r: R, c: C});
            delete ws[cellRef];
        }
        
        // Tambahkan Tanda Tangan
        let lastRow = range.e.r + 2; 

        function addCell(ws, row, col, value) {
            let ref = XLSX.utils.encode_cell({r: row, c: col});
            ws[ref] = { t: 's', v: value };
        }

        addCell(ws, lastRow, 1, "Mengetahui,");
        addCell(ws, lastRow + 1, 1, "KEPALA DINAS TENAGA KERJA KABUPATEN KARAWANG");
        addCell(ws, lastRow + 5, 1, "{{ $ttd->kadis_nama }}");
        addCell(ws, lastRow + 6, 1, "Pembina TK. I / NIP : {{ $ttd->kadis_nip }}");

        addCell(ws, lastRow, 8, "{{ $ttd->kota_surat }}, " + new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }));
        addCell(ws, lastRow + 1, 8, "DPC FSP LEM SPSI KARAWANG");
        addCell(ws, lastRow + 3, 8, "KETUA");
        addCell(ws, lastRow + 7, 8, "{{ $ttd->ketua_nama }}");
        addCell(ws, lastRow + 3, 10, "SEKRETARIS");
        addCell(ws, lastRow + 7, 10, "{{ $ttd->sekretaris_nama }}");

        ws['!ref'] = XLSX.utils.encode_range({
            s: { c: 0, r: 0 },
            e: { c: 11, r: lastRow + 8 }
        });

        XLSX.utils.book_append_sheet(wb, ws, "Data PUK");
        XLSX.writeFile(wb, "Data_Anggota_DPC_SPSI_{{ date('Y') }}.xlsx");
    }
</script>

@endsection