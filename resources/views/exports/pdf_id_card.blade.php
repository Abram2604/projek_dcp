<!DOCTYPE html>
<html>
<head>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; font-family: sans-serif; background-color: #312e81; color: white; }
        .card-container { width: 100%; height: 100%; position: relative; text-align: center; }
        .header { background-color: #4f46e5; padding: 10px 0; }
        .logo { width: 30px; vertical-align: middle; }
        .title { font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; display: inline-block; vertical-align: middle; margin-left: 5px; }
        .content { padding: 10px; }
        .qr-box { background: white; padding: 5px; display: inline-block; border-radius: 5px; margin-bottom: 5px; }
        .name { font-size: 12px; font-weight: bold; margin: 2px 0; text-transform: uppercase; }
        .role { font-size: 8px; color: #a5b4fc; margin: 0; }
        .footer { position: absolute; bottom: 5px; width: 100%; font-size: 6px; color: #6366f1; }
    </style>
</head>
<body>
    <div class="card-container">
        <div class="header">
            <img src="{{ public_path('img/logo.png') }}" class="logo">
            <span class="title">DPC FSP LEM SPSI<br>KARAWANG</span>
        </div>
        <div class="content">
            <div class="qr-box">
                <!-- Generate QR Image Base64 -->
                <img src="data:image/png;base64, {{ base64_encode(QrCode::format('png')->size(90)->generate($user->string_kode_qr)) }}">
            </div>
            <h3 class="name">{{ $user->nama_lengkap }}</h3>
            <p class="role">{{ $jabatan }}<br>{{ $divisi }}</p>
        </div>
        <div class="footer">KARTU ANGGOTA DIGITAL</div>
    </div>
</body>
</html>