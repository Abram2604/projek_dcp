<table>
    <!-- JUDUL LAPORAN -->
    <thead>
        <tr>
            <td colspan="11" style="font-weight: bold; font-size: 14px; text-align: center; height: 30px; vertical-align: middle;">
                DAFTAR NAMA ANGGOTA DPC FSP LEM SPSI KARAWANG TAHUN {{ date('Y') }}
            </td>
        </tr>
        <tr></tr> <!-- Spasi Baris -->
        
        <!-- HEADER TABEL -->
        <tr>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">NO</th>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">NAMA FEDERASI</th>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">NOMOR BUKTI PENCATATAN FEDERASI</th>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">SERIKAT PEKERJA/SERIKAT BURUH</th>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">NOMOR BUKTI PENCATATAN PUK</th>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">JML ANGGOTA SP/SB</th>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">JML HASIL VERIFIKASI</th>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">TOTAL ANGGOTA SP/SB</th>
            <th rowspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">NAMA AFILIASI KONFEDERASI</th>
            <th colspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">NAMA PENGURUS</th>
        </tr>
        <tr>
            <th style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">KETUA</th>
            <th style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f2f2f2;">SEKRETARIS</th>
        </tr>
    </thead>

    <tbody>
        <!-- ROW INDUK (ROW I) - DATA MANUAL DARI CONTROLLER/TTD -->
        <tr>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">I</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">Federasi Serikat Pekerja Logam, Elektronik dan Mesin (FSPLEM SPSI)</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">Penc.29/DPC/ FSP LEM SPSI/KRW/VIII/01</td>
            <td style="border: 1px solid #000000; vertical-align: middle; font-weight: bold;">Pimpinan Unit Kerja (PUK)</td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; background-color: #e6f3ff; color: #0000FF;">{{ $totalAnggota }}</td>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">KSPSI</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $ttd->ketua_nama }}</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $ttd->sekretaris_nama }}</td>
        </tr>

        <!-- DATA DATABASE -->
        @foreach($dataPuk as $index => $p)
        <tr>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $p->nama_federasi }}</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $p->no_pencatatan_federasi }}</td>
            <td style="border: 1px solid #000000; vertical-align: middle; font-weight: bold;">{{ $p->nama_perusahaan }}</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $p->no_pencatatan }}</td>
            
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">{{ $p->jumlah_anggota }}</td>
            
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $p->hasil_verifikasi > 0 ? $p->hasil_verifikasi : '' }}</td>
            
            {{-- TOTAL (Jika Manual/Induk -> Biru, Jika Biasa -> Abu) --}}
            @if($p->manual_total_anggota)
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold; background-color: #e6f3ff; color: #0000FF;">{{ $p->manual_total_anggota }}</td>
            @else
                <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; background-color: #f9f9f9;">{{ $p->jumlah_anggota }}</td>
            @endif

            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $p->afiliasi }}</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $p->nama_ketua }}</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $p->nama_sekretaris }}</td>
        </tr>
        @endforeach

        <!-- FOOTER TOTAL -->
        <tr>
            <td colspan="5" style="border: 1px solid #000000; text-align: right; font-weight: bold; vertical-align: middle; background-color: #f2f2f2;">JUMLAH TOTAL ANGGOTA SP/SB TERDAFTAR</td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle; background-color: #f2f2f2;">{{ $totalAnggota }}</td>
            <td style="border: 1px solid #000000; background-color: #f2f2f2;"></td>
            <td style="border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle; background-color: #f2f2f2;">{{ $totalAnggota }}</td>
            <td colspan="3" style="border: 1px solid #000000; text-align: center; font-weight: bold; vertical-align: middle; background-color: #f2f2f2;">KSPSI</td>
        </tr>
    </tbody>
</table>

{{-- SECTION TANDA TANGAN (Layout Mapping Excel) --}}
<table>
    <tr></tr><tr></tr> <!-- Spasi 2 Baris -->

    <!-- Baris 1: Mengetahui & Tanggal -->
    <tr>
        <td></td> <!-- A -->
        <td colspan="3" style="text-align: center; vertical-align: bottom;">Mengetahui,</td> <!-- B,C,D (Kiri) -->
        <td></td><td></td><td></td><td></td><td></td> <!-- E,F,G,H,I -->
        <td colspan="2" style="text-align: center; vertical-align: bottom;">{{ $ttd->kota_surat }}, {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</td> <!-- J,K (Kanan) -->
    </tr>

    <!-- Baris 2: Jabatan Dinas & Nama DPC -->
    <tr>
        <td></td>
        <td colspan="3" style="text-align: center; font-weight: bold; vertical-align: top;">KEPALA DINAS TENAGA KERJA<br>KABUPATEN KARAWANG</td>
        <td></td><td></td><td></td><td></td><td></td>
        <td colspan="2" style="text-align: center; font-weight: bold; vertical-align: top;">DPC FSP LEM SPSI KARAWANG</td>
    </tr>
    
    <!-- Baris 3: Jabatan Ketua & Sekretaris (Hanya di Kanan) -->
    <tr>
        <td></td>
        <td colspan="3"></td> <!-- Kiri Kosong -->
        <td></td><td></td><td></td><td></td><td></td>
        <td style="text-align: center; font-weight: bold; vertical-align: top;">KETUA</td>      <!-- J -->
        <td style="text-align: center; font-weight: bold; vertical-align: top;">SEKRETARIS</td> <!-- K -->
    </tr>

    <!-- Spasi untuk Gambar TTD (4 Baris) -->
    <tr></tr><tr></tr><tr></tr><tr></tr>

    <!-- Baris 4: Nama Pejabat -->
    <tr>
        <td></td>
        <td colspan="3" style="text-align: center; font-weight: bold; text-decoration: underline; vertical-align: bottom;">{{ $ttd->kadis_nama }}</td>
        <td></td><td></td><td></td><td></td><td></td>
        <td style="text-align: center; font-weight: bold; text-decoration: underline; vertical-align: bottom;">{{ $ttd->ketua_nama }}</td>      <!-- J -->
        <td style="text-align: center; font-weight: bold; text-decoration: underline; vertical-align: bottom;">{{ $ttd->sekretaris_nama }}</td> <!-- K -->
    </tr>

    <!-- Baris 5: NIP (Kiri) -->
    <tr>
        <td></td>
        <td colspan="3" style="text-align: center; vertical-align: top;">Pembina TK. I / NIP : {{ $ttd->kadis_nip }}</td>
        <td></td><td></td><td></td><td></td><td></td>
        <td></td> <!-- Kanan Kosong -->
        <td></td>
    </tr>
</table>