<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Zakat Ditolak</title>
<style>
    body { margin: 0; padding: 0; font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; }
    .wrapper { max-width: 600px; margin: 30px auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 30px rgba(0,0,0,0.08); }
    .header { background: linear-gradient(135deg, #991b1b, #dc2626); padding: 40px; text-align: center; }
    .header-icon { width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; margin: 0 auto 15px; font-size: 36px; display: flex; align-items: center; justify-content: center; }
    .header h1 { color: white; font-size: 22px; margin: 0; font-weight: 700; }
    .header p { color: rgba(255,255,255,0.8); font-size: 13px; margin: 6px 0 0; }
    .body { padding: 35px 40px; }
    .greeting { font-size: 16px; color: #374151; margin-bottom: 15px; }
    .greeting strong { color: #dc2626; }
    .message { font-size: 14px; color: #6b7280; line-height: 1.7; margin-bottom: 25px; }
    .card { background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 20px 25px; margin-bottom: 20px; }
    .card-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #991b1b; margin-bottom: 15px; }
    table.detail { width: 100%; border-collapse: collapse; }
    table.detail tr td { padding: 8px 0; font-size: 13px; border-bottom: 1px solid #fecaca; }
    table.detail tr:last-child td { border-bottom: none; }
    table.detail .label { color: #6b7280; width: 45%; }
    table.detail .value { color: #111827; font-weight: 600; text-align: right; }
    .reason-box { background: #fff7ed; border: 1px solid #fed7aa; border-left: 4px solid #f97316; border-radius: 8px; padding: 15px 20px; margin: 20px 0; }
    .reason-box .reason-title { font-size: 12px; font-weight: 700; color: #c2410c; text-transform: uppercase; margin-bottom: 8px; }
    .reason-box p { font-size: 14px; color: #374151; margin: 0; line-height: 1.6; }
    .steps { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 20px 25px; margin: 20px 0; }
    .steps-title { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #1d4ed8; margin-bottom: 12px; }
    .step { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px; font-size: 13px; color: #374151; }
    .step-num { background: #2563eb; color: white; width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; }
    .divider { height: 1px; background: #e5e7eb; margin: 25px 0; }
    .cta { text-align: center; margin: 25px 0; }
    .btn { display: inline-block; background: #2563eb; color: white; padding: 13px 30px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; margin: 0 5px; }
    .btn-secondary { background: #065f46; }
    .badge { display: inline-block; background: #dc2626; color: white; padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-top: 10px; }
    .footer { background: #f9fafb; padding: 25px 40px; text-align: center; border-top: 1px solid #e5e7eb; }
    .footer p { font-size: 12px; color: #9ca3af; margin: 4px 0; }
    .footer strong { color: #065f46; }
</style>
</head>
<body>
<div class="wrapper">
    <!-- Header -->
    <div class="header">
        <div class="header-icon">❌</div>
        <h1>Pembayaran Ditolak</h1>
        <p>Transaksi zakat Anda tidak dapat diproses</p>
    </div>

    <!-- Body -->
    <div class="body">
        <p class="greeting">Assalamu'alaikum, <strong>{{ $user->name }}</strong></p>
        <p class="message">
            Kami mohon maaf, setelah dilakukan verifikasi oleh tim Amil, pembayaran zakat Anda tidak dapat kami proses.
            Di bawah ini adalah informasi lengkap mengenai penolakan tersebut.
        </p>

        <!-- Detail Card -->
        <div class="card">
            <div class="card-title">📋 Detail Transaksi yang Ditolak</div>
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
                    <td class="label">Total</td>
                    <td class="value" style="font-size:16px;">Rp {{ number_format($transaksi->total_bayar,0,',','.') }}</td>
                </tr>
            </table>
            <span class="badge">❌ DITOLAK</span>
        </div>

        <!-- Reason -->
        <div class="reason-box">
            <div class="reason-title">⚠️ Alasan Penolakan</div>
            <p>{{ $alasan ?? $transaksi->alasan_tolak ?? 'Bukti pembayaran tidak valid atau tidak dapat diverifikasi.' }}</p>
        </div>

        <!-- Next Steps -->
        <div class="steps">
            <div class="steps-title">💡 Langkah Selanjutnya</div>
            <div class="step">
                <div class="step-num">1</div>
                <p>Baca alasan penolakan di atas dengan teliti.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <p>Perbaiki kendala yang disebutkan (misalnya: unggah ulang bukti transfer yang jelas).</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <p>Buat transaksi baru melalui portal Zakat Online dan ulangi pembayaran.</p>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <p>Hubungi kami jika ada pertanyaan atau membutuhkan bantuan lebih lanjut.</p>
            </div>
        </div>

        <div class="divider"></div>

        <div class="cta">
            <a href="{{ url('/user/donasi/buat') }}" class="btn btn-secondary">Buat Transaksi Baru</a>
            <a href="{{ url('/user/riwayat') }}" class="btn">Lihat Riwayat</a>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Kami mohon maaf atas ketidaknyamanan ini.</p>
        <p><strong>Zakat Online</strong> — Sistem Manajemen Zakat Digital</p>
        <p style="margin-top:10px;">Email ini dikirim otomatis, mohon tidak membalas email ini. Hubungi kami di <strong>admin@zakatzakat.com</strong></p>
    </div>
</div>
</body>
</html>
