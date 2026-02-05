<?php

namespace App\Jobs;

use App\Mail\ReservationCreated;
use App\Mail\ReservationStatusUpdated;
use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendReservationNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public Reservation $reservation,
        public string $notificationType
    ) {}

    public function handle(): void
    {
        $user = $this->reservation->user;

        if (!$user || !$user->email) {
            return;
        }

        $mailable = match ($this->notificationType) {
            'created' => new ReservationCreated($this->reservation),
            'approved' => new ReservationStatusUpdated($this->reservation, 'approved'),
            'borrowed' => new ReservationStatusUpdated($this->reservation, 'borrowed'),
            'returned' => new ReservationStatusUpdated($this->reservation, 'returned'),
            'cancelled' => new ReservationStatusUpdated($this->reservation, 'cancelled'),
            'cancelled_by_admin' => new ReservationStatusUpdated($this->reservation, 'cancelled_by_admin'),
            'book_available' => new ReservationStatusUpdated($this->reservation, 'book_available'),
            default => null,
        };

        if ($mailable) {
            Mail::to($user->email)->send($mailable);
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('Failed to send reservation notification', [
            'reservation_id' => $this->reservation->id,
            'type' => $this->notificationType,
            'error' => $exception->getMessage(),
        ]);
    }
}
