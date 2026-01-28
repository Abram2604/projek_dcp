{{-- 
    MAPPING SESUAI PDF PAGE 2:
    1. ASET / DANA (UANG)
    2. PENGELUARAN DAN MODAL
--}}

<thead>
    <!-- BAGIAN ASET -->
    <tr class="table-dark">
        <th colspan="2" class="text-start ps-3">ASET</th>
    </tr>
</thead>
<tbody>
    <tr class="fw-bold bg-light">
        <td colspan="2" class="text-decoration-underline">DANA ( UANG )</td>
    </tr>
    
    <!-- Rincian Aset -->
    <tr>
        <td width="70%">Rekening BNI an DPC FSP LEM SPSI Kab Karawang</td>
        <td width="30%">
            <input type="number" class="form-control form-control-sm text-end input-money calc-asset" 
                   name="assets[bni]" value="{{ $data['assets']['bni'] ?? 0 }}">
        </td>
    </tr>
    <tr>
        <td>Kas</td>
        <td>
            <input type="number" class="form-control form-control-sm text-end input-money calc-asset" 
                   name="assets[kas]" value="{{ $data['assets']['kas'] ?? 0 }}">
        </td>
    </tr>
    <tr>
        <td>Advance Kesekretariatan</td>
        <td>
            <input type="number" class="form-control form-control-sm text-end input-money calc-asset" 
                   name="assets[advSekretariat]" value="{{ $data['assets']['advSekretariat'] ?? 0 }}">
        </td>
    </tr>
    <tr>
        <td>Advance Operasional BPH & Bidang</td>
        <td>
            <input type="number" class="form-control form-control-sm text-end input-money calc-asset" 
                   name="assets[advBph]" value="{{ $data['assets']['advBph'] ?? 0 }}">
        </td>
    </tr>
    <tr>
        <td>Advance Proposal / Lainnya</td>
        <td>
            <input type="number" class="form-control form-control-sm text-end input-money calc-asset" 
                   name="assets[advLain]" value="{{ $data['assets']['advLain'] ?? 0 }}">
        </td>
    </tr>

    <!-- TOTAL BALANCE -->
    <tr class="fw-bold" style="background-color: #d1fae5;">
        <td class="text-center text-uppercase">Balance (Total Aset)</td>
        <td>
            <input type="number" id="totalAssetDisplay" class="form-control form-control-sm text-end fw-bold bg-transparent border-0" readonly value="0">
        </td>
    </tr>
</tbody>

<!-- SPACER -->
<tbody><tr><td colspan="2" class="border-0 p-3"></td></tr></tbody>

<thead>
    <!-- BAGIAN KEWAJIBAN & MODAL -->
    <tr class="table-dark">
        <th colspan="2" class="text-start ps-3">PENGELUARAN DAN MODAL</th>
    </tr>
</thead>
<tbody>
    
    <!-- PENGELUARAN (Diambil Otomatis/Manual dari Flow) -->
    <tr class="fw-bold bg-light">
        <td colspan="2" class="text-decoration-underline">PENGELUARAN</td>
    </tr>
    <tr>
        <td>Jumlah Pengeluaran (Total Beban Organisasi)</td>
        <td>
            <!-- Value ini idealnya ambil dari total Expenses, tapi kita buat input agar user bisa koreksi jika perlu -->
            @php 
                $totalExp = array_sum($data['expenses'] ?? []);
            @endphp
            <input type="number" class="form-control form-control-sm text-end input-money calc-liability" 
                   id="inputTotalExpense" value="{{ $totalExp }}" readonly style="background-color: #f0f0f0;">
            <small class="text-muted fst-italic">*Otomatis dari Laporan Dana Keluar</small>
        </td>
    </tr>

    <!-- MODAL -->
    <tr class="fw-bold bg-light">
        <td colspan="2" class="text-decoration-underline">MODAL</td>
    </tr>
    <tr>
        <td>Simpanan / Saldo Awal</td>
        <td>
            <input type="number" class="form-control form-control-sm text-end input-money calc-liability" 
                   name="saldoAwal" value="{{ $data['saldoAwal'] ?? 0 }}">
        </td>
    </tr>
    <tr>
        <td>Pemasukan (COS & Non COS)</td>
        <td>
            @php 
                $totalInc = array_sum($data['incomeCos'] ?? []) + array_sum($data['incomeNonCos'] ?? []);
            @endphp
            <input type="number" class="form-control form-control-sm text-end input-money calc-liability" 
                   id="inputTotalIncome" value="{{ $totalInc }}" readonly style="background-color: #f0f0f0;">
            <small class="text-muted fst-italic">*Otomatis dari Laporan Dana Masuk</small>
        </td>
    </tr>

    <!-- TOTAL SALDO MODAL -->
    <tr class="fw-bold" style="background-color: #d1fae5;">
        <td class="text-center text-uppercase">Saldo Modal (Total Pasiva)</td>
        <td>
            <input type="number" id="totalLiabilityDisplay" class="form-control form-control-sm text-end fw-bold bg-transparent border-0" readonly value="0">
        </td>
    </tr>
</tbody>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        function calculatePosition() {
            let totalAsset = 0;
            document.querySelectorAll('.calc-asset').forEach(el => totalAsset += parseFloat(el.value || 0));
            document.getElementById('totalAssetDisplay').value = totalAsset;
            
            let expense = parseFloat(document.getElementById('inputTotalExpense').value || 0);
            let saldoAwal = parseFloat(document.querySelector('input[name="saldoAwal"]').value || 0);
            let income = parseFloat(document.getElementById('inputTotalIncome').value || 0);
            let totalPasiva = (saldoAwal + income) - expense; // Ini rumus saldo akhir sebenarnya
            document.getElementById('totalLiabilityDisplay').value = totalPasiva; 
        }

        // Trigger saat input berubah
        document.querySelectorAll('.input-money').forEach(input => {
            input.addEventListener('input', calculatePosition);
        });
        
        calculatePosition();
    });
</script>