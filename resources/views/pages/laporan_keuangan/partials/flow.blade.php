@php
    // KITA HITUNG DULU TOTALNYA DI SINI (AGAR SAAT MODE BACA, ANGKA TOTAL MUNCUL BENAR TANPA JS)
    $incCos = $data['incomeCos'] ?? [];
    $incNon = $data['incomeNonCos'] ?? [];
    $exp    = $data['expenses'] ?? [];

    $totalCos = ($incCos['kiic']??0) + ($incCos['kim']??0) + ($incCos['kisc']??0) + ($incCos['luar']??0);
    $totalNon = ($incNon['adminBank']??0) + ($incNon['donasi']??0);
    $totalIncome = $totalCos + $totalNon;

    $totalExpense = 
        ($exp['operasional']??0) + ($exp['bidang1']??0) + ($exp['bidang2']??0) + 
        ($exp['bidang3']??0) + ($exp['bidang4']??0) + ($exp['bidang5']??0) + 
        ($exp['sekretariat']??0) + ($exp['insentif']??0);

    $surplus = $totalIncome - $totalExpense;
@endphp

<thead class="table-dark">
    <tr>
        <th width="5%" class="text-center">No</th>
        <th>Keterangan</th>
        <th width="25%" class="text-center">Jumlah (Rp)</th>
    </tr>
</thead>
<tbody>
    <!-- === BAGIAN 1: PEMASUKAN COS === -->
    <tr>
        <td colspan="3" class="fw-bold bg-light text-primary">PEMASUKAN COS</td>
    </tr>
    <tr>
        <td class="text-center">1</td>
        <td>Zona KIIC</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($incCos['kiic'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-income" 
                       name="incomeCos[kiic]" value="{{ $incCos['kiic'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">2</td>
        <td>Zona KIM</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($incCos['kim'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-income" 
                       name="incomeCos[kim]" value="{{ $incCos['kim'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">3</td>
        <td>Zona KISC</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($incCos['kisc'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-income" 
                       name="incomeCos[kisc]" value="{{ $incCos['kisc'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">4</td>
        <td>Zona Luar</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($incCos['luar'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-income" 
                       name="incomeCos[luar]" value="{{ $incCos['luar'] ?? 0 }}">
            @endif
        </td>
    </tr>

    <!-- === BAGIAN 2: PEMASUKAN NON COS === -->
    <tr>
        <td colspan="3" class="fw-bold bg-light text-primary">PEMASUKAN NON COS</td>
    </tr>
    <tr>
        <td class="text-center">1</td>
        <td>Administrasi Bank</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($incNon['adminBank'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-income" 
                       name="incomeNonCos[adminBank]" value="{{ $incNon['adminBank'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">2</td>
        <td>Dana Konsolidasi / Donasi</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($incNon['donasi'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-income" 
                       name="incomeNonCos[donasi]" value="{{ $incNon['donasi'] ?? 0 }}">
            @endif
        </td>
    </tr>

    <!-- TOTAL PEMASUKAN -->
    <tr class="fw-bold" style="background-color: #d1fae5;">
        <td colspan="2" class="text-end text-success">TOTAL PENDAPATAN</td>
        <td class="text-end">
            @if($readOnly)
                <span class="text-success">{{ number_format($totalIncome, 0, ',', '.') }}</span>
            @else
                <input type="number" id="totalIncomeDisplay" class="form-control form-control-sm text-end fw-bold text-success" 
                       readonly value="{{ $totalIncome }}">
            @endif
        </td>
    </tr>

    <!-- SPACER -->
    <tr><td colspan="3" class="border-0 bg-white"></td></tr>

    <!-- === BAGIAN 3: PENGELUARAN === -->
    <tr>
        <td colspan="3" class="fw-bold bg-light text-danger">PENGELUARAN ORGANISASI</td>
    </tr>
    <tr>
        <td class="text-center">1</td>
        <td>Beban Operasional BPH</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($exp['operasional'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-expense" 
                       name="expenses[operasional]" value="{{ $exp['operasional'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">2</td>
        <td>Bidang I (Organisasi)</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($exp['bidang1'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-expense" 
                       name="expenses[bidang1]" value="{{ $exp['bidang1'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">3</td>
        <td>Bidang II (Advokasi)</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($exp['bidang2'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-expense" 
                       name="expenses[bidang2]" value="{{ $exp['bidang2'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">4</td>
        <td>Bidang III (PSDM)</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($exp['bidang3'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-expense" 
                       name="expenses[bidang3]" value="{{ $exp['bidang3'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">5</td>
        <td>Bidang IV (Kesejahteraan)</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($exp['bidang4'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-expense" 
                       name="expenses[bidang4]" value="{{ $exp['bidang4'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">6</td>
        <td>Bidang V (Publikasi)</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($exp['bidang5'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-expense" 
                       name="expenses[bidang5]" value="{{ $exp['bidang5'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">7</td>
        <td>Kesekretariatan</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($exp['sekretariat'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-expense" 
                       name="expenses[sekretariat]" value="{{ $exp['sekretariat'] ?? 0 }}">
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">8</td>
        <td>Setoran Perangkat & Insentif</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($exp['insentif'] ?? 0, 0, ',', '.') }}
            @else
                <input type="number" class="form-control form-control-sm text-end input-money calc-expense" 
                       name="expenses[insentif]" value="{{ $exp['insentif'] ?? 0 }}">
            @endif
        </td>
    </tr>

    <!-- TOTAL PENGELUARAN -->
    <tr class="fw-bold" style="background-color: #fee2e2;">
        <td colspan="2" class="text-end text-danger">TOTAL PENGELUARAN</td>
        <td class="text-end">
            @if($readOnly)
                <span class="text-danger">{{ number_format($totalExpense, 0, ',', '.') }}</span>
            @else
                <input type="number" id="totalExpenseDisplay" class="form-control form-control-sm text-end fw-bold text-danger" 
                       readonly value="{{ $totalExpense }}">
            @endif
        </td>
    </tr>

    <!-- HASIL AKHIR -->
    <tr><td colspan="3" class="border-0 bg-white"></td></tr>
    
    <tr class="fw-bold bg-warning bg-opacity-25">
        <td colspan="2" class="text-end">SURPLUS / MINUS</td>
        <td class="text-end">
            @if($readOnly)
                {{ number_format($surplus, 0, ',', '.') }}
            @else
                <input type="number" id="surplusDisplay" class="form-control form-control-sm text-end fw-bold" 
                       readonly value="{{ $surplus }}">
            @endif
        </td>
    </tr>
</tbody>

<!-- Kalkulasi JS (Hanya Load Jika Bukan ReadOnly) -->
@if(!$readOnly)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const inputs = document.querySelectorAll('.input-money');
        function calculate() {
            let totalIncome = 0;
            let totalExpense = 0;
            document.querySelectorAll('.calc-income').forEach(el => totalIncome += parseFloat(el.value || 0));
            document.querySelectorAll('.calc-expense').forEach(el => totalExpense += parseFloat(el.value || 0));
            
            // Set Value ke Input Readonly
            const elInc = document.getElementById('totalIncomeDisplay');
            const elExp = document.getElementById('totalExpenseDisplay');
            const elSur = document.getElementById('surplusDisplay');

            if(elInc) elInc.value = totalIncome;
            if(elExp) elExp.value = totalExpense;
            if(elSur) elSur.value = totalIncome - totalExpense;
        }
        
        // Pasang Event Listener ke Input
        inputs.forEach(input => input.addEventListener('input', calculate));
        
        // Hitung pertama kali saat modal dibuka
        calculate(); 
    });
</script>
@endif