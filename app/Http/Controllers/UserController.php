<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $allUsers = DB::select('CALL sp_anggota_list(?, ?)', [$search, null]);

        $page = $request->input('page', 1); 
        $perPage = 10;
        $offset = ($page * $perPage) - $perPage; 

        $itemsForCurrentPage = array_slice($allUsers, $offset, $perPage, true);

        $users = new LengthAwarePaginator(
            $itemsForCurrentPage, 
            count($allUsers),    
            $perPage,             
            $page,               
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $divisi = DB::select('CALL sp_divisi_list()');
        $jabatan = DB::table('Jabatan')->orderBy('nama_jabatan', 'asc')->get();

        return view('pages.users.index', compact('users', 'divisi', 'jabatan'));
    }

    public function activate($id)
    {
        // Kita update manual saja query-nya agar simpel
        DB::update('UPDATE Anggota SET status_aktif = 1, diupdate_pada = NOW() WHERE id = ?', [$id]);
        
        return redirect()->back()->with('success', 'User berhasil diaktifkan kembali.');
    }

    public function store(Request $request)
    {
        // Gunakan Validator::make() untuk named error bag
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:Anggota,username',
            'password'     => 'required|string|min:6',
            'id_jabatan'   => 'required|integer',
            'email'        => 'nullable|email',
            'nomor_hp'     => 'nullable|string|max:20',
            'id_divisi'    => 'nullable|integer',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'id_jabatan.required' => 'Jabatan wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'create') // Named error bag 'create'
                ->withInput();
        }

        try {
            // Hash Password Laravel
            $passwordHash = Hash::make($request->password);

            // Panggil SP Create (Otomatis generate QR di database)
            DB::statement('CALL sp_anggota_create(?, ?, ?, ?, ?, ?, ?)', [
                $request->nama_lengkap,
                $request->username,
                $passwordHash,
                $request->email,
                $request->nomor_hp,
                $request->id_divisi, // Bisa null
                $request->id_jabatan
            ]);

            return redirect()->back()->with('success', 'Anggota berhasil ditambahkan & QR Code dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Gagal menambah anggota: ' . $e->getMessage()], 'create')
                ->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'username'     => 'required|string|unique:Anggota,username,' . $id,
            'email'        => 'nullable|email',
            'nomor_hp'     => 'nullable|string|max:20',
            'id_jabatan'   => 'required|integer',
            'id_divisi'    => 'nullable|integer',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'id_jabatan.required' => 'Jabatan wajib dipilih.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'update')
                ->withInput();
        }

        try {
            DB::statement('CALL sp_anggota_update(?, ?, ?, ?, ?, ?, ?)', [
                $id,
                $request->nama_lengkap,
                $request->username,
                $request->email,
                $request->nomor_hp,
                $request->id_divisi,
                $request->id_jabatan
            ]);
            
            return redirect()->back()->with('success', 'Data anggota berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['general' => 'Gagal update: ' . $e->getMessage()], 'update')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        // Soft Delete via SP
        DB::statement('CALL sp_anggota_soft_delete(?)', [$id]);
        return redirect()->back()->with('success', 'Anggota dinonaktifkan (Soft Delete).');
    }

    public function resetPassword(Request $request, $id)
    {
        // Gunakan Validator::make() untuk named error bag
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:6',
        ], [
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'reset') // Named error bag 'reset'
                ->withInput()
                ->with('reset_user_id', $id)
                ->with('reset_user_name', $request->input('user_name', 'User'));
        }

        try {
            $passwordHash = Hash::make($request->new_password);

            DB::statement('CALL sp_anggota_reset_password(?, ?)', [
                $id,
                $passwordHash
            ]);

            return redirect()->back()->with('success', 'Password berhasil direset.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['new_password' => 'Gagal reset password: ' . $e->getMessage()], 'reset')
                ->withInput()
                ->with('reset_user_id', $id)
                ->with('reset_user_name', $request->input('user_name', 'User'));
        }
    }

    public function generateQr($id)
    {
        // Regenerate QR untuk user lama
        DB::statement('CALL sp_anggota_generate_qr(?)', [$id]);
        return redirect()->back()->with('success', 'QR Code berhasil digenerate ulang.');
    }
}