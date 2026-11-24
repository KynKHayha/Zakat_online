<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Zakat Diterima</title>
<style>
    body { margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; }
    .wrapper { max-width: 600px; margin: 30px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #065f46, #059669); padding: 40px 40px 35px; text-align: center; }
    .header-icon { width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 36px; }
    .header h1 { color: white; font-size: 22px; margin: 0; font-weight: 700; }
    .header p { color: rgba(255,255,255,0.8); font-size: 13px; margin: 6px 0 0; }
    .arabic { font-size: 20px; color: rgba(255,255,255,0.9); margin: 15px 0 5px; font-family: 'Traditional Arabic', serif; }
    .body { padding: 35px 40px; }
    .greeting { font-size: 16px; color: #374151; margin-bottom: 15px; }
    .greeting strong { color: #065f46; }
    .message { font-size: 14px; color: #6b7280; line-height: 1.7; margin-bottom: 25px; }
    .card { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px 25px; margin-bottom: 20px; }
    .card-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #065f46; margin-bottom: 15px; }
    table.detail { width: 100%; border-collapse: collapse; }
    table.detail tr td { padding: 8px 0; font-size: 13px; border-bottom: 1px solid #d1fae5; }
    table.detail tr:last-child td { border-bottom: none; }
    table.detail .label { color: #6b7280; width: 45%; }
    table.detail .value { color: #111827; font-weight: 600; text-align: right; }
    table.detail .value.amount { color: #065f46; font-size: 16px; }
    .badge { display: inline-block; background: #065f46; color: white; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 10px; }
    .divider { height: 1px; background: #e5e7eb; margin: 25px 0; }
    .cta { text-align: center; margin: 25px 0; }
    .btn { display: inline-block; background: #059669; color: white; padding: 13px 30px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; }
    .footer { background: #f9fafb; padding: 25px 40px; text-align: center; border-top: 1px solid #e5e7eb; }
    .footer p { font-size: 12px; color: #9ca3af; margin: 4px 0; }
    .footer strong { color: #065f46; }
    .quote { background: #ecfdf5; border-left: 4px solid #059669; padding: 15px 20px; border-radius: 0 8px 8px 0; margin: 20px 0; }
    .quote p { font-size: 13px; color: #374151; font-style: italic; margin: 0; }
</style>
</head>
<body>
<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <div class="header-icon">✅</div>
        <h1>Pembayaran Diterima!</h1>
        <p>Zakat Anda telah terverifikasi dan diterima</p>
        <div class="arabic">بَارَكَ اللَّهُ لَكَ</div>
        <p style="font-size:11px;color:rgba(255,255,255,0.7);">Semoga Allah memberkahi Anda</p>
    </div>

    <!-- Body -->
    <div class="body">
        <p class="greeting">Assalamu'alaikum, <strong>{{ $user->name }}</strong> 👋</p>
        <p class="message">
            Alhamdulillah! Kami dengan senang hati memberitahukan bahwa pembayaran zakat Anda telah berhasil diverifikasi oleh tim Amil.
            Zakat Anda akan segera disalurkan kepada yang berhak menerimanya.
        </p>

        <!-- Detail Card -->
        <div class="card">
            <div class="card-title">📋 Detail Transaksi</div>
            <table class="detail">
                <tr>
                    <td class="label">Kode Transaksi</td>
                    <td class="value" style="font-family:monospace;font-size:13px;">{{ $transaksi->kode_transaksi }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Muzakki</td>
                    <td class="value">{{ $donasi->nama_muzakki }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Zakat</td>
                    <td class="value">{{ $donasi->label_jenis_zakat }}</td>
                </tr>
                <tr>
                    <td class="label">Metode Bayar</td>
                    <td class="value">{{ $transaksi->label_metode_bayar }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Verifikasi</td>
                    <td class="value">{{ $transaksi->diverifikasi_at?->format('d M Y, H:i') }} WIB</td>
                </tr>
                <tr>
                    <td class="label">Total Zakat</td>
                    <td class="value amount">Rp {{ number_format($transaksi->total_bayar,0,',','.') }}</td>
                </tr>
            </table>
            <span class="badge">✅ SUKSES</span>
        </div>

        <div class="quote">
            <p>"Ambillah zakat dari sebagian harta mereka, dengan zakat itu kamu membersihkan dan mensucikan mereka."</p>
            <p style="margin-top:8px;font-size:11px;color:#065f46;font-style:normal;font-weight:600;">— QS. At-Taubah: 103</p>
        </div>

        <div class="divider"></div>

        <div class="cta">
            <a href="{{ url('/user/riwayat') }}" class="btn">Lihat Riwayat Transaksi</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Terima kasih telah menunaikan kewajiban zakat Anda</p>
        <p><strong>Zakat Online</strong> — Sistem Manajemen Zakat Digital</p>
        <p style="margin-top:10px;">Email ini dikirim otomatis, mohon tidak membalas email ini.</p>
    </div>
</div>
</body>
</html>
