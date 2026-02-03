<thead class="table-dark">
    <tr>
        <th colspan="2" class="text-start ps-3">ASET</th>
    </tr>
</thead>
<tbody>
    <tr class="fw-bold bg-light">
        <td colspan="2" class="text-decoration-underline">DANA ( UANG )</td>
    </tr>
    
    @php
    $assetItems = [
        'Rekening BNI a.n. DPC FSP LEM SPSI Kab. Karawang' => 'bni',
        'Kas' => 'kas',
        'Advance Kesekretariatan' => 'advSekretariat',
        'Advance Operasional BPH & Bidang' => 'advBph',
        'Advance Proposal' => 'advProposal'
    ];
    @endphp
    
    @foreach($assetItems as $label => $key)
    <tr>
        <td width="70%">{{ $label }}</td>
        <td width="30%">
            @if($readOnly)
                <div class="text-end fw-bold">{{ number_format($data['assets'][$key] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pos-asset" 
                       name="assets[{{ $key }}]" 
                       value="{{ $data['assets'][$key] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    @endforeach

    <tr class="fw-bold bg-success text-white">
        <td class="text-center">BALANCE (TOTAL ASET)</td>
        <td class="text-end">
            @if($readOnly)
                <span class="fw-bold">{{ number_format($data['total_aset'] ?? 0, 0, ',', '.') }}</span>
            @else
                <span id="total_asset_display" class="fw-bold">0</span>
            @endif
        </td>
    </tr>
</tbody>

<!-- SPACER -->
<tbody>
    <tr>
        <td colspan="2" class="border-0 p-3"></td>
    </tr>
</tbody>

<thead class="table-dark">
    <tr>
        <th colspan="2" class="text-start ps-3">PENGELUARAN DAN MODAL</th>
    </tr>
</thead>
<tbody>
    <!-- PENGELUARAN SECTION -->
    <tr class="fw-bold bg-light">
        <td colspan="2" class="text-decoration-underline">PENGELUARAN</td>
    </tr>
    
    @php
    $expenseItems = [
        'Operasional Organisasi' => 'pos_ops',
        'Event Organisasi' => 'pos_evt',
        'Kesekretariatan' => 'pos_sekretariat',
        'Setoran Perangkat & Insentif Pengurus' => 'pos_insentif'
    ];
    @endphp
    
    @foreach($expenseItems as $label => $key)
    <tr>
        <td>{{ $label }}</td>
        <td>
            @if($readOnly)
                <div class="text-end text-danger fw-bold">{{ number_format($data['liabilities'][$key] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pos-expense" 
                       name="liabilities[{{ $key }}]" 
                       value="{{ $data['liabilities'][$key] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    @endforeach
    
    <tr class="fw-bold bg-light">
        <td class="text-end">Jumlah Pengeluaran</td>
        <td class="text-end">
            @if($readOnly)
                <span class="text-danger fw-bold">{{ number_format($data['total_pengeluaran'] ?? 0, 0, ',', '.') }}</span>
            @else
                <span id="sum_pos_exp" class="text-danger fw-bold">0</span>
            @endif
        </td>
    </tr>

    <!-- MODAL SECTION -->
    <tr class="fw-bold bg-light">
        <td colspan="2" class="text-decoration-underline">MODAL</td>
    </tr>
    
    @php
    $modalItems = [
        'Simpanan / Saldo Awal' => 'pos_saldo_awal',
        'Pemasukan COS' => 'pos_inc_cos',
        'Pemasukan Non COS' => 'pos_inc_non_cos'
    ];
    @endphp
    
    @foreach($modalItems as $label => $key)
    <tr>
        <td>{{ $label }}</td>
        <td>
            @if($readOnly)
                <div class="text-end text-success fw-bold">{{ number_format($data['liabilities'][$key] ?? 0, 0, ',', '.') }}</div>
            @else
                <input type="number" 
                       class="form-control form-control-sm text-end input-money calc-pos-modal" 
                       name="liabilities[{{ $key }}]" 
                       value="{{ $data['liabilities'][$key] ?? 0 }}"
                       min="0">
            @endif
        </td>
    </tr>
    @endforeach
    
    <tr class="fw-bold bg-light">
        <td class="text-end">Jumlah Modal</td>
        <td class="text-end">
            @if($readOnly)
                <span class="text-success fw-bold">{{ number_format($data['total_modal'] ?? 0, 0, ',', '.') }}</span>
            @else
                <span id="sum_pos_modal" class="text-success fw-bold">0</span>
            @endif
        </td>
    </tr>

    <tr class="fw-bold bg-danger text-white">
        <td class="text-center">SALDO MODAL (BALANCE)</td>
        <td class="text-end">
            @if($readOnly)
                <span class="fw-bold">{{ number_format($data['saldo_modal'] ?? 0, 0, ',', '.') }}</span>
            @else
                <span id="total_liability_display" class="fw-bold">0</span>
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

    // Ambil semua elemen kalkulasi
    const assetInputs = document.querySelectorAll('.calc-pos-asset');
    const expenseInputs = document.querySelectorAll('.calc-pos-expense');
    const modalInputs = document.querySelectorAll('.calc-pos-modal');
    
    const totalAssetDisplay = document.getElementById('total_asset_display');
    const sumExpenseDisplay = document.getElementById('sum_pos_exp');
    const sumModalDisplay = document.getElementById('sum_pos_modal');
    const saldoModalDisplay = document.getElementById('total_liability_display');

    // Fungsi kalkulasi semua total
    const calculateTotals = () => {
        // Hitung Total Aset
        let totalAsset = 0;
        assetInputs.forEach(input => {
            totalAsset += parseFloat(input.value) || 0;
        });
        totalAssetDisplay.textContent = formatRupiah(totalAsset);
        
        // Hitung Total Pengeluaran
        let totalExpense = 0;
        expenseInputs.forEach(input => {
            totalExpense += parseFloat(input.value) || 0;
        });
        sumExpenseDisplay.textContent = formatRupiah(totalExpense);
        
        // Hitung Total Modal
        let totalModal = 0;
        modalInputs.forEach(input => {
            totalModal += parseFloat(input.value) || 0;
        });
        sumModalDisplay.textContent = formatRupiah(totalModal);
        
        // Hitung Saldo Modal (Modal - Pengeluaran)
        const saldoModal = totalModal - totalExpense;
        saldoModalDisplay.textContent = formatRupiah(saldoModal);
        
        // Update warna berdasarkan nilai saldo
        if (saldoModalDisplay) {
            saldoModalDisplay.className = saldoModal >= 0 
                ? 'fw-bold text-white' 
                : 'fw-bold text-warning';
        }
    };

    // Pasang event listener ke semua input
    [...assetInputs, ...expenseInputs, ...modalInputs].forEach(input => {
        input.addEventListener('input', calculateTotals);
        input.addEventListener('change', calculateTotals); // Handle paste
    });

    // Hitung awal saat halaman dimuat
    calculateTotals();
    
    // Auto-format input saat blur (opsional)
    [...assetInputs, ...expenseInputs, ...modalInputs].forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(0);
            }
        });
    });
});
</script>
@endif