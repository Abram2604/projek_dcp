@php
    $incCos = $data['incomeCos'];
    $incNon = $data['incomeNonCos'];
    $exp    = $data['expenses'];
    $vols   = $data['volumes'];
    
@endphp

<thead class="table-dark">
    <tr>
        <th width="5%" class="text-center">No</th>
        <th>Keterangan</th>
        <th width="15%" class="text-center">Vol</th> 
        <th width="25%" class="text-center">Jumlah (Rp)</th>
    </tr>
</thead>
<tbody>
    <!-- === 1. PEMASUKAN COS === -->
    <tr><td colspan="4" class="fw-bold bg-light text-primary">PEMASUKAN COS</td></tr>
    @foreach(['Zona KIIC'=>'kiic', 'Zona KIM'=>'kim', 'Zona KISC'=>'kisc', 'Zona Luar'=>'luar'] as $lbl => $k)
    <tr>
        <td class="text-center">{{ $loop->iteration }}</td><td>{{ $lbl }}</td>
        <td class="text-center"><input type="number" class="form-control form-control-sm text-center input-vol-inc" name="volumes[inc_{{$k}}]" value="{{ $vols['inc_'.$k]??0 }}" {{$readOnly?'readonly':''}}></td>
        <td><input type="number" class="form-control form-control-sm text-end input-money calc-inc" name="incomeCos[{{$k}}]" value="{{ $incCos[$k]??0 }}" {{$readOnly?'readonly':''}}></td>
    </tr>
    @endforeach
    <tr class="fw-bold bg-light"><td colspan="3" class="text-end">Jumlah Pemasukan COS</td><td class="text-end"><span id="sum_inc_cos">0</span></td></tr>

    <!-- === 2. PEMASUKAN NON COS === -->
    <tr><td colspan="4" class="fw-bold bg-light text-primary">PEMASUKAN NON COS</td></tr>
    @foreach(['Administrasi Bank'=>'adminBank', 'Dana Konsolidasi / Donasi'=>'donasi'] as $lbl => $k)
    <tr>
        <td class="text-center">{{ $loop->iteration }}</td><td>{{ $lbl }}</td>
        <td class="text-center"><input type="number" class="form-control form-control-sm text-center input-vol-inc" name="volumes[inc_non_{{$k}}]" value="{{ $vols['inc_non_'.$k]??0 }}" {{$readOnly?'readonly':''}}></td>
        <td><input type="number" class="form-control form-control-sm text-end input-money calc-inc" name="incomeNonCos[{{$k}}]" value="{{ $incNon[$k]??0 }}" {{$readOnly?'readonly':''}}></td>
    </tr>
    @endforeach
    <tr class="fw-bold bg-light"><td colspan="3" class="text-end">Jumlah Pemasukan Non COS</td><td class="text-end"><span id="sum_inc_non">0</span></td></tr>

    <!-- TOTAL PENDAPATAN -->
    <tr class="fw-bold bg-success text-white">
        <td colspan="3" class="text-end">TOTAL PENDAPATAN ORGANISASI</td>
        <td class="text-end"><span id="total_income_display">0</span></td>
    </tr>

    <!-- === 3. PENGELUARAN OPS === -->
    <tr><td colspan="4" class="fw-bold bg-light text-danger">PENGELUARAN ( BEBAN ) ORGANISASI</td></tr>
    <tr class="table-secondary"><td colspan="4" class="fw-bold ps-4">Operasional Organisasi</td></tr>
    
    @foreach([
        'Ketua, Sekretaris, Bendahara' => 'ops_ketua',
        'Bidang I Organisasi' => 'ops_bidang1',
        'Bidang II Advokasi' => 'ops_bidang2',
        'Bidang III Pengembangan SDM' => 'ops_bidang3',
        'Bidang IV Kesejahteraan' => 'ops_bidang4',
        'Bidang V Publikasi & Hubungan Antar Lembaga' => 'ops_bidang5'
    ] as $lbl => $k)
    <tr>
        <td></td><td>{{ $lbl }}</td>
        <td class="text-center"><input type="number" class="form-control form-control-sm text-center input-vol-exp" name="volumes[v_{{$k}}]" value="{{ $vols['v_'.$k]??0 }}" {{$readOnly?'readonly':''}}></td>
        <td><input type="number" class="form-control form-control-sm text-end input-money calc-exp calc-ops" name="expenses[{{$k}}]" value="{{ $exp[$k]??0 }}" {{$readOnly?'readonly':''}}></td>
    </tr>
    @endforeach
    <tr class="fw-bold bg-light"><td colspan="3" class="text-end">Jumlah Operasional</td><td class="text-end"><span id="sum_ops">0</span></td></tr>

    <!-- === 4. PENGELUARAN EVENT (BARU) === -->
    <tr class="table-secondary"><td colspan="4" class="fw-bold ps-4">Event Organisasi</td></tr>
    @foreach([
        'Ketua, Sekretaris, Bendahara' => 'evt_ketua',
        'Bidang I Organisasi' => 'evt_bidang1',
        'Bidang II Advokasi' => 'evt_bidang2',
        'Bidang III Pengembangan SDM' => 'evt_bidang3',
        'Bidang IV Kesejahteraan' => 'evt_bidang4',
        'Bidang V Publikasi & Hubungan Antar Lembaga' => 'evt_bidang5'
    ] as $lbl => $k)
    <tr>
        <td></td><td>{{ $lbl }}</td>
        <td class="text-center"><input type="number" class="form-control form-control-sm text-center input-vol-exp" name="volumes[v_{{$k}}]" value="{{ $vols['v_'.$k]??0 }}" {{$readOnly?'readonly':''}}></td>
        <td><input type="number" class="form-control form-control-sm text-end input-money calc-exp calc-evt" name="expenses[{{$k}}]" value="{{ $exp[$k]??0 }}" {{$readOnly?'readonly':''}}></td>
    </tr>
    @endforeach
    <tr class="fw-bold bg-light"><td colspan="3" class="text-end">Jumlah Event</td><td class="text-end"><span id="sum_evt">0</span></td></tr>

    <!-- === 5. LAINNYA === -->
    <tr>
        <td></td><td>Kesekretariatan</td>
        <td class="text-center"><input type="number" class="form-control form-control-sm text-center input-vol-exp" name="volumes[v_sekretariat]" value="{{ $vols['v_sekretariat']??0 }}" {{$readOnly?'readonly':''}}></td>
        <td><input type="number" class="form-control form-control-sm text-end input-money calc-exp" name="expenses[sekretariat]" value="{{ $exp['sekretariat']??0 }}" {{$readOnly?'readonly':''}}></td>
    </tr>
    <tr>
        <td></td><td>Setoran Perangkat & Insentif Pengurus</td>
        <td class="text-center"><input type="number" class="form-control form-control-sm text-center input-vol-exp" name="volumes[v_insentif]" value="{{ $vols['v_insentif']??0 }}" {{$readOnly?'readonly':''}}></td>
        <td><input type="number" class="form-control form-control-sm text-end input-money calc-exp" name="expenses[insentif]" value="{{ $exp['insentif']??0 }}" {{$readOnly?'readonly':''}}></td>
    </tr>

    <!-- TOTAL PENGELUARAN -->
    <tr class="fw-bold bg-danger text-white">
        <td colspan="3" class="text-end">TOTAL PENGELUARAN ORGANISASI</td>
        <td class="text-end"><span id="total_expense_display">0</span></td>
    </tr>

    <!-- SURPLUS/MINUS -->
    <tr class="fw-bold bg-warning text-dark">
        <td colspan="3" class="text-end">SURPLUS / MINUS</td>
        <td class="text-end"><span id="surplus_display">0</span></td>
    </tr>
</tbody>

{{-- JAVASCRIPT LOGIKA HITUNG (HANYA MUNCUL DI MODE EDIT) --}}
@if(!$readOnly)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const moneyInputs = document.querySelectorAll('.input-money');
        const volIncInputs = document.querySelectorAll('.input-vol-income');
        const volExpInputs = document.querySelectorAll('.input-vol-expense');
        
        function calculateGrandTotals() {
            let totalIncome = 0;
            let totalExpense = 0;
            let totalVolIncome = 0;
            let totalVolExpense = 0;

            // 1. Hitung Uang
            document.querySelectorAll('.calc-income').forEach(el => totalIncome += parseFloat(el.value || 0));
            document.querySelectorAll('.calc-expense').forEach(el => totalExpense += parseFloat(el.value || 0));

            // 2. Hitung Volume Income
            volIncInputs.forEach(el => totalVolIncome += parseFloat(el.value || 0));

            // 3. Hitung Volume Expense
            volExpInputs.forEach(el => totalVolExpense += parseFloat(el.value || 0));
            
            // Update Tampilan Total Bawah
            const elInc = document.getElementById('totalIncomeDisplay');
            const elExp = document.getElementById('totalExpenseDisplay');
            const elSur = document.getElementById('surplusDisplay');
            const elVolInc = document.getElementById('totalVolIncomeDisplay');
            const elVolExp = document.getElementById('totalVolExpenseDisplay');

            if(elInc) elInc.value = totalIncome;
            if(elExp) elExp.value = totalExpense;
            if(elSur) elSur.value = totalIncome - totalExpense;
            
            if(elVolInc) elVolInc.value = totalVolIncome;
            if(elVolExp) elVolExp.value = totalVolExpense;
        }
        
        // Pasang listener
        moneyInputs.forEach(input => input.addEventListener('input', calculateGrandTotals));
        volIncInputs.forEach(input => input.addEventListener('input', calculateGrandTotals));
        volExpInputs.forEach(input => input.addEventListener('input', calculateGrandTotals));
        
        // Hitung awal
        calculateGrandTotals(); 
    });
</script>
@endif