<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Anggota extends Authenticatable
{
    use HasFactory, Notifiable;

    // Arahkan ke nama tabel yang benar di database Anda
    protected $table = 'Anggota';
    
    // Tentukan Primary Key
    protected $primaryKey = 'id';
    
    // Matikan timestamp default (created_at/updated_at)
    // dan arahkan ke kolom timestamp custom Anda
    const CREATED_AT = 'dibuat_pada';
    const UPDATED_AT = 'diupdate_pada';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'username',
        'password_hash',
        'nama_lengkap',
        'id_divisi',
        'email',       // Dipakai buat nyimpen role sementara
        'status_aktif'
    ];

    // Sembunyikan password saat data diambil
    protected $hidden = [
        'password_hash',
    ];

    // OVERRIDE: Beritahu Laravel kalau kolom password kita namanya 'password_hash'
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}