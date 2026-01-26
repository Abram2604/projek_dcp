@extends('layouts.app')
@section('title', 'Laporan Harian')
@section('header_title', 'Laporan Kegiatan')
@section('header_subtitle', 'Pencatatan aktivitas rutin dan laporan penggunaan dana.')

@section('content')
@if(session('success'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
@endif

@if($errors->has('submit'))
    <div class="alert alert-danger d-flex align-items-center" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first('submit') }}
    </div>
@endif

@php
    $saldoList = $ringkasanSaldo ?? [];
@endphp

<div class="row g-4 mb-4">
    @forelse($saldoList as $saldo)
        @php
            $saldoAwal = (float) $saldo->saldo_awal;
            $terpakai = (float) $saldo->total_pengeluaran;
            $sisa = (float) $saldo->sisa_saldo;
            $persen = $saldoAwal > 0 ? min(100, ($terpakai / $saldoAwal) * 100) : 0;
        @endphp
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body position-relative">
                    <div class="position-absolute top-0 end-0 p-3 text-muted opacity-25">
                        <i class="fa-solid fa-wallet fa-2x"></i>
                    </div>
                    <small class="text-muted fw-bold text-uppercase">{{ $saldo->nama_divisi }}</small>
                    <h5 class="fw-bold mb-1">Rp {{ number_format($sisa, 0, ',', '.') }}</h5>
                    <div class="small text-muted mb-3">Sisa saldo</div>
                    <div class="d-flex justify-content-between small">
                        <span class="text-muted">Awal</span>
                        <span class="fw-bold">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-2">
                        <span class="text-muted">Terpakai</span>
                        <span class="text-danger fw-bold">- Rp {{ number_format($terpakai, 0, ',', '.') }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $persen }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning mb-0">Belum ada data saldo bulan ini.</div>
        </div>
    @endforelse
</div>

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <div>
        <h5 class="fw-bold mb-1">Laporan Kegiatan Harian</h5>
        <p class="text-muted small mb-0">Rekap aktivitas harian dan rincian pengeluaran.</p>
    </div>
    <div class="d-flex gap-2">
        @if($isBPH)
            <div class="dropdown">
                <button class="btn btn-success rounded-pill px-4 dropdown-toggle text-white" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-file-excel me-1 text-white"></i> Export Excel
                </button>
                <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="exportDropdown" style="min-width: 250px;">
                    <li>
                        @php $selectedExportDivisi = request('divisi', 'all'); @endphp
                        <form method="GET" action="{{ route('laporan.export_excel') }}">
                            <div class="mb-2">
                                <label class="form-label small">Dipilih untuk Export</label>
                                <select name="divisi" class="form-select form-select-sm">
                                    <option value="all" {{ $selectedExportDivisi === 'all' ? 'selected' : '' }}>Semua Divisi</option>
                                    @foreach($divisiList as $divisi)
                                        <option value="{{ $divisi->id }}" {{ (string) $selectedExportDivisi === (string) $divisi->id ? 'selected' : '' }}>
                                            {{ $divisi->nama_divisi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Bulan</label>
                                <select name="bulan" class="form-select form-select-sm">
                                    @for($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create(null, $i, 1)->isoFormat('MMMM') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Tahun</label>
                                <select name="tahun" class="form-select form-select-sm">
                                    @for($i = date('Y'); $i >= date('Y') - 2; $i--)
                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm w-100 text-white">
                                <i class="fa-solid fa-download me-1 text-white"></i> Download
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        @endif
        <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="collapse" data-bs-target="#laporanForm">
            <i class="fa-solid fa-plus me-1"></i> Buat Laporan Baru
        </button>
    </div>
 </div>

<div class="collapse {{ old('judul_kegiatan') || $errors->any() ? 'show' : '' }}" id="laporanForm">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h6 class="m-0 fw-bold">Form Laporan Harian</h6>
            <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#laporanForm">Tutup</button>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('laporan.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf

                @if($isBPH)
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold">Divisi</label>
                        <select name="id_divisi" class="form-select @error('id_divisi') is-invalid @enderror">
                            <option value="">Pilih divisi</option>
                            @foreach($divisiList as $divisi)
                                <option value="{{ $divisi->id }}" {{ old('id_divisi') == $divisi->id ? 'selected' : '' }}>
                                    {{ $divisi->nama_divisi }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_divisi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="col-12 col-md-8">
                    <label class="form-label fw-bold">Judul Kegiatan</label>
                    <input type="text" name="judul_kegiatan" class="form-control @error('judul_kegiatan') is-invalid @enderror" value="{{ old('judul_kegiatan') }}" placeholder="Contoh: Operasional Kesekretariatan">
                    @error('judul_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold">Tanggal</label>
                    <input type="date" name="tanggal_laporan" class="form-control @error('tanggal_laporan') is-invalid @enderror" value="{{ old('tanggal_laporan', date('Y-m-d')) }}">
                    @error('tanggal_laporan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-8">
                    <label class="form-label fw-bold">Program Kerja (Opsional)</label>
                    <select name="id_program_kerja" class="form-select @error('id_program_kerja') is-invalid @enderror">
                        <option value="">Tanpa program kerja</option>
                        @foreach($programKerja as $program)
                            <option value="{{ $program->id }}" {{ old('id_program_kerja') == $program->id ? 'selected' : '' }}>
                                {{ $program->nama_program }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_program_kerja')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Deskripsi / Hasil Kegiatan</label>
                    <textarea name="isi_laporan" rows="3" class="form-control @error('isi_laporan') is-invalid @enderror" placeholder="Tuliskan detail kegiatan...">{{ old('isi_laporan') }}</textarea>
                    @error('isi_laporan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="includeFinancial" name="include_financial" value="1" {{ old('include_financial') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="includeFinancial">Sertakan laporan pengeluaran dana</label>
                    </div>
                </div>

                @php
                    $oldDescriptions = old('item_description', []);
                    $oldVolumes = old('item_volume', []);
                    $oldUnits = old('item_unit', []);
                    $oldAmounts = old('item_amount', []);
                    $oldItems = [];
                    $totalOld = 0;
                    $maxOld = max(count($oldDescriptions), count($oldVolumes), count($oldUnits), count($oldAmounts));
                    for ($i = 0; $i < $maxOld; $i++) {
                        $desc = trim((string) ($oldDescriptions[$i] ?? ''));
                        $amount = (float) ($oldAmounts[$i] ?? 0);
                        $volume = (float) ($oldVolumes[$i] ?? 1);
                        $unit = trim((string) ($oldUnits[$i] ?? 'Unit'));
                        if ($desc === '' || $amount <= 0) {
                            continue;
                        }
                        $lineTotal = $volume * $amount;
                        $totalOld += $lineTotal;
                        $oldItems[] = [
                            'description' => $desc,
                            'volume' => $volume,
                            'unit' => $unit !== '' ? $unit : 'Unit',
                            'amount' => $amount,
                            'total' => $lineTotal,
                        ];
                    }
                @endphp

                <div class="col-12">
                    <div id="pengeluaranSection" class="border rounded-3 p-3 bg-light {{ old('include_financial') ? '' : 'd-none' }}">
                        <h6 class="fw-bold text-uppercase text-muted">Rincian Pengeluaran</h6>
                        <div class="row g-2 align-items-end mb-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label small">Uraian</label>
                                <input type="text" id="newItemDescription" class="form-control form-control-sm" placeholder="Makan siang, BBM, dll">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small">Volume</label>
                                <input type="number" id="newItemVolume" class="form-control form-control-sm" value="1" min="1">
                            </div>
                            <div class="col-6 col-md-2">
                                <label class="form-label small">Satuan</label>
                                <input type="text" id="newItemUnit" class="form-control form-control-sm" value="Orang">
                            </div>
                            <div class="col-8 col-md-3">
                                <label class="form-label small">Jumlah (Rp)</label>
                                <input type="number" id="newItemAmount" class="form-control form-control-sm" placeholder="0" min="0">
                            </div>
                            <div class="col-4 col-md-1">
                                <button type="button" id="addExpenseItem" class="btn btn-primary btn-sm w-100">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        @error('item_description')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead class="table-light text-uppercase small">
                                    <tr>
                                        <th>Uraian</th>
                                        <th>Vol</th>
                                        <th>Satuan</th>
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-end">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="expenseItemsBody">
                                    <tr id="expenseEmptyRow" {{ count($oldItems) > 0 ? 'class=d-none' : '' }}>
                                        <td colspan="6" class="text-center text-muted small">Belum ada item pengeluaran.</td>
                                    </tr>
                                    @foreach($oldItems as $oldItem)
                                        <tr data-total="{{ $oldItem['total'] }}">
                                            <td>
                                                {{ $oldItem['description'] }}
                                                <input type="hidden" name="item_description[]" value="{{ $oldItem['description'] }}">
                                            </td>
                                            <td>
                                                {{ $oldItem['volume'] }}
                                                <input type="hidden" name="item_volume[]" value="{{ $oldItem['volume'] }}">
                                            </td>
                                            <td>
                                                {{ $oldItem['unit'] }}
                                                <input type="hidden" name="item_unit[]" value="{{ $oldItem['unit'] }}">
                                            </td>
                                            <td class="text-end">
                                                Rp {{ number_format($oldItem['amount'], 0, ',', '.') }}
                                                <input type="hidden" name="item_amount[]" value="{{ $oldItem['amount'] }}">
                                            </td>
                                            <td class="text-end">Rp {{ number_format($oldItem['total'], 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-light text-danger remove-expense">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light fw-bold">
                                        <td colspan="4" class="text-end">Total Pengeluaran</td>
                                        <td class="text-end" id="expenseTotal">Rp {{ number_format($totalOld, 0, ',', '.') }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold">Upload Dokumentasi (Foto/PDF)</label>
                    <input type="file" name="lampiran" class="form-control @error('lampiran') is-invalid @enderror">
                    @error('lampiran')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <button class="btn btn-primary w-100" type="submit">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <form method="GET" action="{{ route('laporan.index') }}" class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label small">Cari laporan</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Judul, isi, atau nama pelapor" value="{{ $filters['q'] }}">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Tanggal mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] }}">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label small">Tanggal akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] }}">
            </div>
            @if($isBPH)
                <div class="col-12 col-md-3">
                    <label class="form-label small">Divisi</label>
                    <select name="divisi" class="form-select">
                        <option value="">Semua divisi</option>
                        @foreach($divisiList as $divisi)
                            <option value="{{ $divisi->id }}" {{ $filters['divisi'] == $divisi->id ? 'selected' : '' }}>
                                {{ $divisi->nama_divisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-12 col-md-1 d-grid">
                <button type="submit" class="btn btn-outline-primary">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        @forelse($laporanList as $lap)
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary text-uppercase">{{ $lap->kode_divisi ?? 'DIV' }}</span>
                        @if($lap->status_laporan === 'DISUBMIT')
                            <span class="badge bg-success bg-opacity-10 text-success">Disubmit</span>
                        @else
                            <span class="badge bg-warning bg-opacity-10 text-warning">Draft</span>
                        @endif
                    </div>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($lap->tanggal_laporan)->format('d M Y') }}</small>
                </div>
                <h6 class="fw-bold mb-1">{{ $lap->judul_kegiatan }}</h6>
                <p class="text-muted small mb-2">{{ Str::limit($lap->isi_laporan, 120) }}</p>
                <div class="d-flex flex-wrap gap-3 align-items-center small">
                    @if($isBPH)
                        <span class="text-muted"><i class="fa-solid fa-user me-1"></i> {{ $lap->nama_lengkap }}</span>
                    @endif
                    @if($lap->nama_program)
                        <span class="text-muted"><i class="fa-solid fa-flag me-1"></i> {{ $lap->nama_program }}</span>
                    @endif
                    @if($lap->url_lampiran)
                        <span class="text-primary"><i class="fa-solid fa-paperclip me-1"></i> Dokumen</span>
                    @endif
                    @if($lap->total_pengeluaran > 0)
                        <span class="text-danger fw-bold"><i class="fa-solid fa-arrow-trend-down me-1"></i> Rp {{ number_format($lap->total_pengeluaran, 0, ',', '.') }}</span>
                    @endif
                </div>
                <div class="mt-3">
                    <a href="{{ route('laporan.show', $lap->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fa-solid fa-eye me-1"></i> Detail
                    </a>
                </div>
            </div>
        @empty
            <div class="p-4 text-center text-muted">Belum ada laporan untuk filter ini.</div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const includeFinancial = document.getElementById('includeFinancial');
        const section = document.getElementById('pengeluaranSection');
        const addButton = document.getElementById('addExpenseItem');
        const tbody = document.getElementById('expenseItemsBody');
        const emptyRow = document.getElementById('expenseEmptyRow');
        const totalEl = document.getElementById('expenseTotal');
        const inputDesc = document.getElementById('newItemDescription');
        const inputVolume = document.getElementById('newItemVolume');
        const inputUnit = document.getElementById('newItemUnit');
        const inputAmount = document.getElementById('newItemAmount');

        function formatRupiah(value) {
            return new Intl.NumberFormat('id-ID').format(value);
        }

        function recalcTotal() {
            let total = 0;
            tbody.querySelectorAll('tr[data-total]').forEach((row) => {
                total += Number(row.dataset.total || 0);
            });
            totalEl.textContent = 'Rp ' + formatRupiah(total);
            const hasItems = tbody.querySelectorAll('tr[data-total]').length > 0;
            if (emptyRow) {
                emptyRow.classList.toggle('d-none', hasItems);
            }
        }

        function removeRow(event) {
            const btn = event.currentTarget;
            const row = btn.closest('tr');
            if (row) {
                row.remove();
                recalcTotal();
            }
        }

        function addRow(item) {
            const row = document.createElement('tr');
            row.dataset.total = item.total.toString();
            const descTd = document.createElement('td');
            descTd.textContent = item.description;
            const descInput = document.createElement('input');
            descInput.type = 'hidden';
            descInput.name = 'item_description[]';
            descInput.value = item.description;
            descTd.appendChild(descInput);

            const volumeTd = document.createElement('td');
            volumeTd.textContent = item.volume;
            const volumeInput = document.createElement('input');
            volumeInput.type = 'hidden';
            volumeInput.name = 'item_volume[]';
            volumeInput.value = item.volume;
            volumeTd.appendChild(volumeInput);

            const unitTd = document.createElement('td');
            unitTd.textContent = item.unit;
            const unitInput = document.createElement('input');
            unitInput.type = 'hidden';
            unitInput.name = 'item_unit[]';
            unitInput.value = item.unit;
            unitTd.appendChild(unitInput);

            const amountTd = document.createElement('td');
            amountTd.className = 'text-end';
            amountTd.textContent = 'Rp ' + formatRupiah(item.amount);
            const amountInput = document.createElement('input');
            amountInput.type = 'hidden';
            amountInput.name = 'item_amount[]';
            amountInput.value = item.amount;
            amountTd.appendChild(amountInput);

            const totalTd = document.createElement('td');
            totalTd.className = 'text-end';
            totalTd.textContent = 'Rp ' + formatRupiah(item.total);

            const actionTd = document.createElement('td');
            actionTd.className = 'text-end';
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-light text-danger remove-expense';
            removeBtn.innerHTML = '<i class="fa-solid fa-trash"></i>';
            actionTd.appendChild(removeBtn);

            row.appendChild(descTd);
            row.appendChild(volumeTd);
            row.appendChild(unitTd);
            row.appendChild(amountTd);
            row.appendChild(totalTd);
            row.appendChild(actionTd);

            removeBtn.addEventListener('click', removeRow);
            tbody.appendChild(row);
            recalcTotal();
        }

        if (includeFinancial && section) {
            includeFinancial.addEventListener('change', function () {
                section.classList.toggle('d-none', !includeFinancial.checked);
            });
        }

        if (addButton) {
            addButton.addEventListener('click', function () {
                const description = (inputDesc.value || '').trim();
                const volume = Number(inputVolume.value || 1);
                const unit = (inputUnit.value || 'Unit').trim();
                const amount = Number(inputAmount.value || 0);
                if (!description || amount <= 0) {
                    return;
                }
                const total = volume * amount;
                addRow({ description, volume, unit: unit || 'Unit', amount, total });
                inputDesc.value = '';
                inputVolume.value = 1;
                inputUnit.value = 'Orang';
                inputAmount.value = '';
            });
        }

        document.querySelectorAll('.remove-expense').forEach((btn) => {
            btn.addEventListener('click', removeRow);
        });

        recalcTotal();
    });
</script>
@endsection
