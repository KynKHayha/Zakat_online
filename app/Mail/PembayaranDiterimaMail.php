<?php

namespace App\Mail;

use App\Models\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PembayaranDiterimaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Transaksi $transaksi)
    {
        $this->transaksi->load(['donasiZakat', 'user', 'pembayaran']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Pembayaran Zakat Anda Telah Diterima — ' . $this->transaksi->kode_transaksi,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pembayaran-diterima',
            with: [
                'transaksi' => $this->transaksi,
                'user'      => $this->transaksi->user,
                'donasi'    => $this->transaksi->donasiZakat,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
