<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Laporan Keuangan Zakat - {{ date('d-m-Y') }}</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1a202c; background: #fff; }
    .header { background: #065f46; color: white; padding: 20px 25px; margin-bottom: 0; }
    .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
    .header p { font-size: 11px; opacity: 0.8; }
    .meta { background: #ecfdf5; border-bottom: 2px solid #065f46; padding: 12px 25px; display: flex; justify-content: space-between; }
    .meta-item { }
    .meta-item label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; display: block; }
    .meta-item value { font-size: 12px; font-weight: bold; color: #065f46; }
    .content { padding: 20px 25px; }
    .summary { display: flex; gap: 15px; margin-bottom: 20px; }
    .summary-card { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; }
    .summary-card .label { font-size: 9px; color: #6b7280; text-transform: uppercase; }
    .summary-card .value { font-size: 14px; font-weight: bold; margin-top: 4px; }
    .summary-card.grand { background: #065f46; border-color: #065f46; color: white; }
    .summary-card.grand .label { color: rgba(255,255,255,0.7); }
    .summary-card.online .value { color: #1d4ed8; }
    .summary-card.ditempat .value { color: #c2410c; }
    .summary-card.grand .value { color: white; font-size: 16px; }
    table { width: 100%; border-collapse: collapse; margin-top: 5px; }
    table thead tr { background: #065f46; color: white; }
    table thead th { padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; font-weight: bold; }
    table thead th.right { text-align: right; }
    table tbody tr:nth-child(even) { background: #f9fafb; }
    table tbody tr:hover { background: #ecfdf5; }
    table tbody td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
    table tbody td.right { text-align: right; font-weight: bold; }
    table tfoot tr { background: #064e3b; color: white; }
    table tfoot td { padding: 10px; font-weight: bold; font-size: 12px; }
    table tfoot td.right { text-align: right; }
    .badge-online { background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    .badge-ditempat { background: #fed7aa; color: #c2410c; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
    .footer { margin-top: 25px; padding-top: 12px; border-top: 1px solid #e5e7eb; text-align: center; color: #9ca3af; font-size: 9px; }
    .no-data { text-align: center; padding: 40px; color: #9ca3af; }
    @page { margin: 15mm; size: A4 landscape; }
</style>
</head>
<body>

<div class="header">
    <h1>🌙 Laporan Keuangan Zakat Online</h1>
    <p>Sistem Manajemen Zakat Digital | Panel Admin/Amil</p>
</div>

<div class="meta">
    <div class="meta-item">
        <label>Periode Laporan</label>
        <value>
            {{ $dariTanggal ? \Carbon\Carbon::parse($dariTanggal)->format('d M Y') : 'Semua' }}
            s/d
            {{ $sampaiTanggal ? \Carbon\Carbon::parse($sampaiTanggal)->format('d M Y') : 'Sekarang' }}
        </value>
    </div>
    <div class="meta-item">
        <label>Dibuat Pada</label>
        <value>{{ \Carbon\Carbon::now()->format('d M Y H:i') }} WIB</value>
    </div>
    <div class="meta-item">
        <label>Total Transaksi</label>
        <value>{{ $transaksis->count() }} transaksi</value>
    </div>
    <div class="meta-item">
        <label>Dibuat Oleh</label>
        <value>Admin / Amil Zakat</value>
    </div>
</div>

<div class="content">
    <!-- Summary -->
    <div class="summary">
        <div class="summary-card online">
            <div class="label">Total Pembayaran Online</div>
            <div class="value">Rp {{ number_format($totalOnline,0,',','.') }}</div>
        </div>
        <div class="summary-card ditempat">
            <div class="label">Total Bayar di Tempat</div>
            <div class="value">Rp {{ number_format($totalDitempat,0,',','.') }}</div>
        </div>
        <div class="summary-card grand">
            <div class="label">Grand Total</div>
            <div class="value">Rp {{ number_format($grandTotal,0,',','.') }}</div>
        </div>
    </div>

    <!-- Table -->
    @if($transaksis->count())
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode Transaksi</th>
                <th>Nama Muzakki</th>
                <th>Jenis Zakat</th>
                <th>Metode Bayar</th>
                <th class="right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksis as $i => $trx)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $trx->tanggal_transaksi?->format('d/m/Y') ?? '-' }}</td>
                <td style="font-family: monospace; font-size: 9px;">{{ $trx->kode_transaksi }}</td>
                <td>{{ $trx->user?->name ?? '-' }}</td>
                <td>{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</td>
                <td>
                    @if($trx->metode_bayar==='online')
                    <span class="badge-online">Online</span>
                    @else
                    <span class="badge-ditempat">Di Tempat</span>
                    @endif
                </td>
                <td class="right">{{ number_format($trx->total_bayar,0,',','.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">TOTAL PENERIMAAN ZAKAT</td>
                <td class="right">{{ number_format($grandTotal,0,',','.') }}</td>
            </tr>
        </tfoot>
    </table>
    @else
    <div class="no-data">Tidak ada data untuk periode yang dipilih.</div>
    @endif

    <div class="footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem Zakat Online &copy; {{ date('Y') }} &bull; {{ \Carbon\Carbon::now()->format('d M Y H:i:s') }} WIB</p>
    </div>
</div>

</body>
</html>
