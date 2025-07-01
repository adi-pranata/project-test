<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengaduan Anda Telah Diterima</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #1e40af;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            background: #f8fafc;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e2e8f0;
        }

        .info-box {
            background: white;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #1e40af;
        }

        .token-box {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
        }

        .token {
            font-family: monospace;
            font-size: 18px;
            font-weight: bold;
            color: #92400e;
            background: white;
            padding: 10px;
            border-radius: 4px;
            display: inline-block;
            margin: 10px 0;
        }

        .button {
            display: inline-block;
            background: #1e40af;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>🏛️ Kabupaten Badung</h1>
        <p>Sistem Pengaduan Masyarakat</p>
    </div>

    <div class="content">
        <h2>Pengaduan Anda Telah Diterima</h2>

        <p>Yth. Bapak/Ibu <strong>{{ $pengaduan->nama }}</strong>,</p>

        <p>Terima kasih telah menyampaikan pengaduan kepada Pemerintah Kabupaten Badung.
            Pengaduan Anda telah kami terima dan akan segera diproses oleh tim kami.</p>

        <div class="info-box">
            <h3>Detail Pengaduan:</h3>
            <p><strong>Judul:</strong> {{ $pengaduan->judul }}</p>
            <p><strong>Tanggal Pengaduan:</strong> {{ $pengaduan->created_at->format('d F Y, H:i') }}</p>
            <p><strong>Status:</strong> Menunggu Verifikasi</p>
        </div>

        <div class="token-box">
            <h3>⚠️ PENTING - Simpan Kode Tracking Ini</h3>
            <p>Gunakan email dan kode tracking berikut untuk memantau status pengaduan Anda:</p>
            <div class="token">{{ $pengaduan->token }}</div>
            <p><small>Kode ini bersifat rahasia, jangan bagikan kepada orang lain.</small></p>
        </div>

        <h3>📋 Langkah Selanjutnya:</h3>
        <ol>
            <li>Pengaduan Anda akan diverifikasi oleh admin dalam 1-3 hari kerja</li>
            <li>Jika disetujui, Anda akan menerima tanggapan dari petugas terkait</li>
            <li>Anda dapat memantau status pengaduan menggunakan kode tracking di atas</li>
        </ol>

        <div style="text-align: center;">
            <a href="{{ $tracking_url }}" class="button">🔍 Lacak Pengaduan</a>
        </div>

        <div class="info-box">
            <h3>🕐 Waktu Penyelesaian:</h3>
            <p>• Verifikasi: 1-3 hari kerja<br>
                • Tanggapan pertama: 3-7 hari kerja<br>
                • Penyelesaian: 7-14 hari kerja (tergantung kompleksitas)</p>
        </div>

        <p>Jika Anda memiliki pertanyaan atau memerlukan bantuan, silakan hubungi:</p>
        <ul>
            <li>📞 Telp: (0361) 123-4567</li>
            <li>📧 Email: pengaduan@badung.go.id</li>
            <li>🌐 Website: www.badung.go.id</li>
        </ul>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis oleh Sistem Pengaduan Masyarakat<br>
            Kabupaten Badung. Mohon tidak membalas email ini.</p>
        <p>© {{ date('Y') }} Pemerintah Kabupaten Badung. Hak Cipta Dilindungi.</p>
    </div>
</body>

</html>