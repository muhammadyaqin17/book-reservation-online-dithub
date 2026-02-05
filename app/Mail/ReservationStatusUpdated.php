<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public string $statusType
    ) {}

    public function envelope(): Envelope
    {
        $subjects = [
            'approved' => 'Reservasi Anda Disetujui',
            'borrowed' => 'Buku Telah Dipinjam',
            'returned' => 'Terima Kasih Telah Mengembalikan Buku',
            'cancelled' => 'Reservasi Dibatalkan',
            'cancelled_by_admin' => 'Reservasi Dibatalkan oleh Admin',
            'book_available' => 'Buku Yang Anda Tunggu Sudah Tersedia!',
        ];

        return new Envelope(
            subject: $subjects[$this->statusType] ?? 'Update Reservasi Buku',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-status-updated',
        );
    }
}
