<!-- MODAL TAMBAH PROGJA -->
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Program Kerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('progja.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    <!-- 1. Nama Program -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Nama Program</label>
                        <input type="text" name="nama_program" class="form-control" required placeholder="Contoh: Diklat Paralegal">
                    </div>

                    <!-- 2. Target (Output) -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Target (Output)</label>
                        <textarea name="target" class="form-control" rows="2" required placeholder="Contoh: Terlatihnya 50 anggota baru..."></textarea>
                    </div>

                    <!-- 3. Action Plan -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Action (Langkah Kerja)</label>
                        <textarea name="action" class="form-control" rows="2" required placeholder="Contoh: Menyusun silabus, menghubungi pemateri..."></textarea>
                    </div>
                    
                    <!-- 4. Divisi Pelaksana (Hanya BPH) -->
                    @if($levelAkses === 'BPH')
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Divisi Pelaksana</label>
                        <select name="id_divisi" class="form-select">
                            @foreach($divisiList as $d)
                                <option value="{{ $d->id }}" {{ (isset($filterDivisi) && $filterDivisi == $d->id) ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- 5. Target Selesai & Anggaran -->
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Target Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Estimasi Anggaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">Rp</span>
                                <input type="number" name="anggaran" class="form-control border-start-0" required placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT PROGRESS -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h6 class="modal-title fw-bold" id="editTitle">Update Progress</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Status</label>
                        <select name="status_proker" id="editStatus" class="form-select">
                            <option value="RENCANA">Rencana</option>
                            <option value="BERJALAN">Sedang Berjalan</option>
                            <option value="SELESAI">Selesai</option>
                            <option value="TERKENDALA">Terkendala</option> <!-- INI YANG DIPERBAIKI -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Persentase (%)</label>
                        <input type="number" name="persen_progress" id="editPersen" class="form-control" min="0" max="100">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL INPUT EVALUASI (HANYA BPH) --}}
@if($levelAkses === 'BPH')
<div class="modal fade" id="modalEvaluasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Input Evaluasi Kinerja</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('progja.store_evaluasi') }}" method="POST"> 
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info small d-flex align-items-center gap-2 border-0 bg-info-subtle text-info-emphasis mb-3">
                        <i class="fa-solid fa-circle-info"></i>
                        Evaluasi ini akan tampil di dashboard divisi terkait.
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Tujukan Untuk Divisi</label>
                        <select name="id_divisi" class="form-select" required>
                            @foreach($divisiList as $d)
                                <option value="{{ $d->id }}" {{ (isset($filterDivisi) && $filterDivisi == $d->id) ? 'selected' : '' }}>
                                    {{ $d->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Isi Catatan / Evaluasi</label>
                        <textarea name="isi_evaluasi" class="form-control" rows="4" placeholder="Contoh: Program rekrutmen perlu ditingkatkan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim Evaluasi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif