<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Anggota extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'Anggota';
    protected $primaryKey = 'id';
    
    // --- SOLUSI 1: KONFIGURASI TIMESTAMP ---
    // Beritahu Laravel nama kolom yang benar
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diupdate_pada'; 

    // --- SOLUSI 2: MATIKAN UPDATE OTOMATIS SAAT LOGIN ---
    // Ini mencegah error jika kolom 'remember_token' tidak ada di database
    public $timestamps = false; // Matikan timestamp otomatis sementara agar aman
    
    protected $fillable = [
        'username',
        'password_hash',
        'nama_lengkap',
        'id_divisi',
        'id_jabatan',
        'email',
        'status_aktif'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // Override Password Name
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    // --- SOLUSI 3: MATIKAN REMEMBER TOKEN ---
    // Agar Laravel tidak mencoba menulis ke kolom 'remember_token' yang mungkin error
    public function getRememberTokenName()
    {
        return null; // Disable remember token
    }

    public function setRememberToken($value)
    {
        // Do nothing (Jangan simpan apa-apa)
    }
}