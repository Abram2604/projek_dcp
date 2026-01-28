<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TtdController extends Controller
{
    public function edit()
    {
        $ttd = DB::table('Pengaturan_Tanda_Tangan')->first();
        return view('pages.pengaturan_ttd.form', compact('ttd'));
    }

    public function update(Request $request)
    {
        DB::table('Pengaturan_Tanda_Tangan')->updateOrInsert(
            ['id' => 1],
            [
                'ketua_nama' => $request->ketua_nama,
                'sekretaris_nama' => $request->sekretaris_nama,
                'bendahara_nama' => $request->bendahara_nama,
                'kota_surat' => $request->kota_surat,
                'updated_at' => now()
            ]
        );

        return back()->with('success', 'Tanda tangan berhasil diperbarui');
    }
}
