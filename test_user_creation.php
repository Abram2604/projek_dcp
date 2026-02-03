<?php
/**
 * Simple test script to debug user creation
 */
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Create request
$request = \Illuminate\Http\Request::create('/users', 'POST', [
    'nama_lengkap' => 'Test User ' . time(),
    'username' => 'testuser_' . time(),
    'password' => 'password123',
    'id_jabatan' => 1,
    'id_divisi' => null,
    'email' => 'test@' . time() . '.com',
    'nomor_hp' => '081234567890'
]);

// Login user first
$user = \App\Models\Anggota::find(1);
\Illuminate\Support\Facades\Auth::login($user);
session([
    'user_jabatan' => 'Ketua DPC',
    'user_level'   => 'BPH',
    'user_divisi'  => 'Bidang Kesekretariatan',
]);

echo "User logged in: " . $user->nama_lengkap . "\n";
echo "Session user_jabatan: " . session('user_jabatan') . "\n\n";

// Process the request
try {
    $response = $kernel->handle($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Stack: " . $e->getTraceAsString() . "\n";
}

// Check if user was created
$lastUser = \App\Models\Anggota::latest()->first();
echo "\nLast user in DB: " . $lastUser->username . " (" . $lastUser->nama_lengkap . ")\n";
?>
