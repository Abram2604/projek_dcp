@php
    $totalIncome = array_sum($data['incomeCos'] ?? []) + array_sum($data['incomeNonCos'] ?? []);
    $totalExpense = array_sum($data['expenses'] ?? []);
    $saldoAwal = $data['saldoAwal'] ?? 0;
    $saldoAkhir = ($saldoAwal + $totalIncome) - $totalExpense;
@endphp

<thead class="table-dark">
    <tr>
        <th colspan="3" class="text-center text-uppercase">Laporan Keuangan Organisasi</th>
    </tr>
</thead>
<tbody>
    <!-- SALDO AWAL -->
    <tr>
        <td width="5%" class="text-center fw-bold">1</td>
        <td class="fw-bold">SALDO AWAL</td>
        <td width="30%">
            <!-- Readonly karena diedit di tab Posisi/Flow -->
            <input type="number" class="form-control form-control-sm text-end fw-bold" 
                   value="{{ $saldoAwal }}" readonly style="background-color: #f8f9fa;">
        </td>
    </tr>

    <!-- PEMASUKAN -->
    <tr>
        <td class="text-center fw-bold">2</td>
        <td class="fw-bold">PEMASUKAN</td>
        <td>
            <input type="number" class="form-control form-control-sm text-end fw-bold text-success" 
                   value="{{ $totalIncome }}" readonly style="background-color: #f8f9fa;">
        </td>
    </tr>

    <!-- PENGELUARAN -->
    <tr>
        <td class="text-center fw-bold">3</td>
        <td class="fw-bold">PENGELUARAN</td>
        <td>
            <input type="number" class="form-control form-control-sm text-end fw-bold text-danger" 
                   value="{{ $totalExpense }}" readonly style="background-color: #f8f9fa;">
        </td>
    </tr>

    <!-- SALDO AKHIR -->
    <tr class="table-primary border-primary">
        <td class="text-center fw-bold bg-primary text-white">4</td>
        <td class="fw-bold bg-primary text-white">SALDO AKHIR</td>
        <td class="bg-primary">
            <input type="number" class="form-control form-control-sm text-end fw-bold bg-primary text-white border-0" 
                   value="{{ $saldoAkhir }}" readonly>
        </td>
    </tr>
</tbody>