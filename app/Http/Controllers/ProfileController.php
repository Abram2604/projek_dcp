<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()  // <--- METHOD INDEX() HARUS ADA!
    {
        $userId = Auth::id();

        // Panggil Stored Procedure
        $data = DB::select('CALL sp_get_profile(?)', [$userId]);
        
        if (empty($data)) {
            abort(404);
        }

        $user = $data[0]; // Ambil baris pertama

        return view('pages.profile.index', compact('user'));
    }
}