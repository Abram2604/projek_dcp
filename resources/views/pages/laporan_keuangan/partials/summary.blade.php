<thead class="table-dark">
    <tr>
        <th colspan="3" class="text-center text-uppercase">Laporan Keuangan Organisasi (Rekap)</th>
    </tr>
</thead>

<tbody>
    <!-- SALDO AWAL -->
    <tr class="fw-bold bg-light">
        <td width="5%" class="text-center">1</td>
        <td width="65%">SALDO AWAL</td>
        <td width="30%">
            @if($readOnly)
                <div class="text-end fw-bold">{{ number_format($data['saldoAwal'] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end fw-bold input-money calc-summary" 
                       name="saldoAwal" 
                       value="{{ $data['saldoAwal'] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>

    <!-- PEMASUKAN -->
    <tr class="fw-bold bg-light">
        <td class="text-center">2</td>
        <td colspan="2" class="text-decoration-underline">PEMASUKAN</td>
    </tr>
    
    <tr>
        <td></td>
        <td>Iuran Anggota (COS)</td>
        <td>
            @if($readOnly)
                <div class="text-end text-success fw-bold">{{ number_format($data['pemasukanCos'] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pemasukan" 
                       name="pemasukanCos" 
                       value="{{ $data['pemasukanCos'] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    
    <tr>
        <td></td>
        <td>Pemasukan Non COS</td>
        <td>
            @if($readOnly)
                <div class="text-end text-success fw-bold">{{ number_format($data['pemasukanNonCos'] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pemasukan" 
                       name="pemasukanNonCos" 
                       value="{{ $data['pemasukanNonCos'] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    
    <tr class="fw-bold bg-light">
        <td></td>
        <td class="text-end">JUMLAH PEMASUKAN</td>
        <td>
            @if($readOnly)
                <span class="text-success fw-bold">{{ number_format($data['totalPemasukan'] ?? 0, 0, ',', '.') }}</span>
            @else
                <span id="total_pemasukan_display" class="text-success fw-bold">0</span>
            @endif
        </td>
    </tr>

    <!-- PENGELUARAN -->
    <tr class="fw-bold bg-light">
        <td class="text-center">3</td>
        <td colspan="2" class="text-decoration-underline">PENGELUARAN</td>
    </tr>
    
    <tr>
        <td></td>
        <td>Operasional Organisasi</td>
        <td>
            @if($readOnly)
                <div class="text-end text-danger fw-bold">{{ number_format($data['pengeluaranOps'] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pengeluaran" 
                       name="pengeluaranOps" 
                       value="{{ $data['pengeluaranOps'] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    
    <tr>
        <td></td>
        <td>Event Organisasi</td>
        <td>
            @if($readOnly)
                <div class="text-end text-danger fw-bold">{{ number_format($data['pengeluaranEvent'] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pengeluaran" 
                       name="pengeluaranEvent" 
                       value="{{ $data['pengeluaranEvent'] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    
    <tr>
        <td></td>
        <td>Kesekretariatan</td>
        <td>
            @if($readOnly)
                <div class="text-end text-danger fw-bold">{{ number_format($data['pengeluaranSekretariat'] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pengeluaran" 
                       name="pengeluaranSekretariat" 
                       value="{{ $data['pengeluaranSekretariat'] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    
    <tr>
        <td></td>
        <td>Setoran Perangkat & Insentif Pengurus</td>
        <td>
            @if($readOnly)
                <div class="text-end text-danger fw-bold">{{ number_format($data['pengeluaranInsentif'] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pengeluaran" 
                       name="pengeluaranInsentif" 
                       value="{{ $data['pengeluaranInsentif'] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    
    <tr class="fw-bold bg-light">
        <td></td>
        <td class="text-end">JUMLAH PENGELUARAN</td>
        <td>
            @if($readOnly)
                <span class="text-danger fw-bold">{{ number_format($data['totalPengeluaran'] ?? 0, 0, ',', '.') }}</span>
            @else
                <span id="total_pengeluaran_display" class="text-danger fw-bold">0</span>
            @endif
        </td>
    </tr>

    <!-- SALDO AKHIR -->
    <tr class="fw-bold bg-success text-white">
        <td class="text-center">4</td>
        <td>SALDO AKHIR (Saldo Awal + Pemasukan - Pengeluaran)</td>
        <td>
            @if($readOnly)
                <span class="fw-bold">{{ number_format($data['saldoAkhir'] ?? 0, 0, ',', '.') }}</span>
            @else
                <span id="saldo_akhir_display" class="fw-bold">0</span>
            @endif
        </td>
    </tr>
</tbody>

{{-- JAVASCRIPT UNTUK PERHITUNGAN OTOMATIS (HANYA MODE EDIT) --}}
@if(!$readOnly)
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Helper: Format angka ke Rupiah tanpa simbol
    const formatRupiah = (num) => {
        return new Intl.NumberFormat('id-ID').format(Math.round(num));
    };

    // Ambil elemen input
    const saldoAwalInput = document.querySelector('input[name="saldoAwal"]');
    const pemasukanInputs = document.querySelectorAll('.calc-pemasukan');
    const pengeluaranInputs = document.querySelectorAll('.calc-pengeluaran');
    
    // Ambil elemen display
    const totalPemasukanDisplay = document.getElementById('total_pemasukan_display');
    const totalPengeluaranDisplay = document.getElementById('total_pengeluaran_display');
    const saldoAkhirDisplay = document.getElementById('saldo_akhir_display');

    // Fungsi kalkulasi semua total
    const calculateTotals = () => {
        // Hitung Total Pemasukan
        let totalPemasukan = 0;
        pemasukanInputs.forEach(input => {
            totalPemasukan += parseFloat(input.value) || 0;
        });
        totalPemasukanDisplay.textContent = formatRupiah(totalPemasukan);
        
        // Hitung Total Pengeluaran
        let totalPengeluaran = 0;
        pengeluaranInputs.forEach(input => {
            totalPengeluaran += parseFloat(input.value) || 0;
        });
        totalPengeluaranDisplay.textContent = formatRupiah(totalPengeluaran);
        
        // Hitung Saldo Akhir
        const saldoAwal = parseFloat(saldoAwalInput.value) || 0;
        const saldoAkhir = saldoAwal + totalPemasukan - totalPengeluaran;
        saldoAkhirDisplay.textContent = formatRupiah(saldoAkhir);
        
        // Update warna berdasarkan nilai saldo
        if (saldoAkhir >= 0) {
            saldoAkhirDisplay.className = 'fw-bold text-white';
        } else {
            saldoAkhirDisplay.className = 'fw-bold text-warning';
        }
    };

    // Pasang event listener ke semua input
    const allInputs = [saldoAwalInput, ...pemasukanInputs, ...pengeluaranInputs];
    allInputs.forEach(input => {
        input.addEventListener('input', calculateTotals);
        input.addEventListener('change', calculateTotals); // Handle paste
    });

    // Hitung awal saat halaman dimuat
    calculateTotals();
    
    // Auto-format input saat blur (opsional)
    allInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(0);
            }
        });
    });
});
</script>
@endif
