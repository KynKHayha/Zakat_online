<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Zakat — {{ $user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.5; }

        /* Header */
        .header { background: linear-gradient(135deg, #047857, #059669); color: white; padding: 24px 30px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .logo-area h1 { font-size: 20px; font-weight: 800; margin-bottom: 3px; }
        .logo-area p { font-size: 10px; opacity: 0.85; }
        .header-right { text-align: right; font-size: 10px; opacity: 0.9; }
        .header-right .doc-title { font-size: 14px; font-weight: 700; margin-bottom: 4px; }

        /* Info Muzakki */
        .muzakki-section { background: #f0fdf4; border: 1px solid #bbf7d0; margin: 20px 30px 0; border-radius: 8px; padding: 16px 20px; }
        .muzakki-section h2 { font-size: 11px; font-weight: 700; color: #065f46; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .info-row { display: flex; gap: 8px; }
        .info-label { font-weight: 600; color: #6b7280; width: 100px; flex-shrink: 0; }
        .info-value { color: #1f2937; }

        /* Periode */
        .periode-bar { margin: 0 30px; background: #1f2937; color: white; padding: 8px 16px; border-radius: 0 0 8px 8px; display: flex; justify-content: space-between; font-size: 10px; }

        /* Summary Cards */
        .summary-cards { display: flex; gap: 12px; margin: 20px 30px; }
        .summary-card { flex: 1; background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px; text-align: center; }
        .summary-card.green { border-color: #86efac; background: #f0fdf4; }
        .summary-card.amber { border-color: #fcd34d; background: #fffbeb; }
        .summary-card .card-label { font-size: 9px; color: #6b7280; font-weight: 600; text-transform: uppercase; margin-bottom: 5px; }
        .summary-card .card-value { font-size: 15px; font-weight: 800; color: #065f46; }
        .summary-card.amber .card-value { color: #92400e; }
        .summary-card .card-sub { font-size: 9px; color: #9ca3af; margin-top: 2px; }

        /* Subtotal per Jenis */
        .section-title { font-size: 11px; font-weight: 700; color: #374151; margin: 0 30px 8px; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #059669; padding-bottom: 5px; }
        .subtotal-grid { display: flex; flex-wrap: wrap; gap: 8px; margin: 0 30px 16px; }
        .subtotal-item { background: white; border: 1px solid #e5e7eb; border-radius: 6px; padding: 10px 14px; }
        .subtotal-item .s-label { font-size: 9px; color: #6b7280; margin-bottom: 3px; }
        .subtotal-item .s-value { font-size: 12px; font-weight: 700; color: #059669; }
        .subtotal-item .s-count { font-size: 9px; color: #9ca3af; }

        /* Main Table */
        .table-wrapper { margin: 0 30px 20px; }
        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #059669; color: white; }
        thead th { padding: 9px 10px; text-align: left; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody tr:hover { background: #f0fdf4; }
        tbody td { padding: 8px 10px; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
        tfoot tr { background: #064e3b; color: white; }
        tfoot td { padding: 10px; font-size: 11px; font-weight: 800; }

        /* Utang Table */
        .utang-header { margin: 0 30px 8px; }
        .utang-header h3 { font-size: 11px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #f59e0b; padding-bottom: 5px; }
        .utang-table thead tr { background: #f59e0b; }
        .utang-table tfoot tr { background: #92400e; }

        /* Badge */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 9px; font-weight: 700; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-amber { background: #fef3c7; color: #92400e; }
        .badge-red   { background: #fee2e2; color: #991b1b; }
        .badge-gray  { background: #f3f4f6; color: #4b5563; }

        /* Footer */
        .doc-footer { margin: 20px 30px 0; padding-top: 16px; border-top: 1px solid #e5e7eb; display: flex; justify-content: space-between; }
        .signature-box { text-align: center; }
        .signature-box .sig-title { font-size: 10px; color: #6b7280; margin-bottom: 60px; }
        .signature-box .sig-name { font-size: 10px; font-weight: 700; border-top: 1px solid #374151; padding-top: 5px; }
        .footer-note { font-size: 9px; color: #9ca3af; text-align: center; margin-top: 16px; padding: 0 30px 20px; }

        @page { margin: 0; }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-top">
            <div class="logo-area">
                <h1>🕌 Zakat Online</h1>
                <p>Sistem Manajemen Zakat Digital</p>
                <p>Jl. Masjid Al-Ikhlas No. 12, Bandar Lampung</p>
            </div>
            <div class="header-right">
                <p class="doc-title">LAPORAN ZAKAT PRIBADI</p>
                <p>Dicetak: {{ now()->format('d M Y, H:i') }} WIB</p>
                <p>No. Dokumen: LPR-{{ $user->id }}-{{ now()->format('Ymd') }}</p>
            </div>
        </div>
    </div>

    {{-- PERIODE BAR --}}
    <div class="periode-bar">
        <span>📅 Periode: {{ \Carbon\Carbon::parse($dariTanggal)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($sampaiTanggal)->format('d M Y') }}</span>
        <span>Total Transaksi: {{ $transaksis->count() }} transaksi sukses</span>
    </div>

    {{-- INFO MUZAKKI --}}
    <div class="muzakki-section">
        <h2>Data Muzakki</h2>
        <div class="info-grid">
            <div>
                <div class="info-row"><span class="info-label">Nama Lengkap</span><span class="info-value">: {{ $user->name }}</span></div>
                <div class="info-row"><span class="info-label">Email</span><span class="info-value">: {{ $user->email }}</span></div>
                <div class="info-row"><span class="info-label">No. HP</span><span class="info-value">: {{ $user->phone ?? '-' }}</span></div>
            </div>
            <div>
                <div class="info-row"><span class="info-label">Alamat</span><span class="info-value">: {{ $user->address ?? '-' }}</span></div>
                <div class="info-row"><span class="info-label">ID Muzakki</span><span class="info-value">: #{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}</span></div>
                <div class="info-row"><span class="info-label">Terdaftar</span><span class="info-value">: {{ $user->created_at->format('d M Y') }}</span></div>
            </div>
        </div>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="summary-cards">
        <div class="summary-card green">
            <p class="card-label">Total Zakat Dibayar</p>
            <p class="card-value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
            <p class="card-sub">{{ $transaksis->count() }} transaksi sukses</p>
        </div>
        <div class="summary-card">
            <p class="card-label">Rata-rata per Transaksi</p>
            <p class="card-value">Rp {{ $transaksis->count() > 0 ? number_format($grandTotal / $transaksis->count(), 0, ',', '.') : '0' }}</p>
            <p class="card-sub">Per transaksi sukses</p>
        </div>
        <div class="summary-card">
            <p class="card-label">Jenis Zakat</p>
            <p class="card-value">{{ $subtotalPerJenis->count() }}</p>
            <p class="card-sub">Jenis berbeda dibayar</p>
        </div>
    </div>

    {{-- SUBTOTAL PER JENIS --}}
    @if($subtotalPerJenis->count() > 0)
    <p class="section-title">Ringkasan per Jenis Zakat</p>
    <div class="subtotal-grid">
        @foreach($subtotalPerJenis as $jenis => $sub)
        <div class="subtotal-item">
            <p class="s-label">{{ $sub['label'] }}</p>
            <p class="s-value">Rp {{ number_format($sub['total'], 0, ',', '.') }}</p>
            <p class="s-count">{{ $sub['jumlah'] }} transaksi</p>
        </div>
        @endforeach
    </div>
    @endif

    {{-- TABEL TRANSAKSI --}}
    <p class="section-title">Detail Transaksi Sukses</p>
    <div class="table-wrapper">
        @if($transaksis->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width:30px">No</th>
                    <th>Kode Transaksi</th>
                    <th>Jenis Zakat</th>
                    <th>Atas Nama</th>
                    <th>Jumlah (Rp)</th>
                    <th>Metode</th>
                    <th>Tgl Verifikasi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksis as $idx => $trx)
                <tr>
                    <td style="color:#9ca3af">{{ $idx + 1 }}</td>
                    <td style="font-family:monospace;font-size:9px;color:#374151;font-weight:700">{{ $trx->kode_transaksi }}</td>
                    <td>{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</td>
                    <td>{{ $trx->donasiZakat?->nama_muzakki ?? $user->name }}</td>
                    <td style="font-weight:700;color:#065f46">{{ number_format($trx->total_bayar, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $trx->metode_bayar === 'online' ? 'badge-green' : 'badge-amber' }}">
                            {{ $trx->label_metode_bayar }}
                        </span>
                    </td>
                    <td style="color:#6b7280">{{ $trx->diverifikasi_at?->format('d/m/Y') ?? '-' }}</td>
                    <td><span class="badge badge-green">✓ Sukses</span></td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align:right">GRAND TOTAL</td>
                    <td>{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
        @else
        <p style="text-align:center;color:#9ca3af;padding:20px">Tidak ada transaksi sukses pada periode ini.</p>
        @endif
    </div>

    {{-- TANDA TANGAN & FOOTER --}}
    <div class="doc-footer">
        <div class="signature-box">
            <p class="sig-title">Muzakki</p>
            <p class="sig-name">{{ $user->name }}</p>
        </div>
        <div style="text-align:center;font-size:9px;color:#9ca3af;flex:1;padding:0 30px">
            <p>Dokumen ini adalah laporan resmi dari Sistem Zakat Online.</p>
            <p>Laporan ini sah tanpa tanda tangan basah.</p>
            <p style="margin-top:4px">© {{ date('Y') }} Sistem Zakat Online — Semua hak dilindungi.</p>
        </div>
        <div class="signature-box">
            <p class="sig-title">Mengetahui, Amil Zakat</p>
            <p class="sig-name">Pengurus LAZ</p>
        </div>
    </div>

    <div class="footer-note">
        <p>Laporan ini digenerate otomatis pada {{ now()->format('d M Y H:i:s') }} WIB &bull; Zakat Online v1.0</p>
    </div>

</body>
</html>
