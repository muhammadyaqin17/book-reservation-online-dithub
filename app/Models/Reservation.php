<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'queue_position',
        'status',
        'reserved_at',
        'approved_at',
        'borrowed_at',
        'due_date',
        'returned_at',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'approved_at' => 'datetime',
        'borrowed_at' => 'datetime',
        'due_date' => 'datetime',
        'returned_at' => 'datetime',
        'queue_position' => 'integer',
    ];

    /**
     * Status constants
     */
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_BORROWED = 'borrowed';
    const STATUS_RETURNED = 'returned';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get all status options
     */
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_APPROVED => 'Disetujui',
            self::STATUS_BORROWED => 'Dipinjam',
            self::STATUS_RETURNED => 'Dikembalikan',
            self::STATUS_CANCELLED => 'Dibatalkan',
        ];
    }

    /**
     * Get the user that owns the reservation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book that is reserved.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
                self::STATUS_PENDING => 'bg-warning text-dark',
                self::STATUS_APPROVED => 'bg-info',
                self::STATUS_BORROWED => 'bg-primary',
                self::STATUS_RETURNED => 'bg-success',
                self::STATUS_CANCELLED => 'bg-danger',
                default => 'bg-secondary',
            };
    }

    /**
     * Check if reservation can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }

    /**
     * Check if reservation is in queue
     */
    public function isInQueue(): bool
    {
        return $this->status === self::STATUS_PENDING && $this->queue_position !== null;
    }

    /**
     * Check if reservation is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === self::STATUS_BORROWED
            && $this->due_date
            && $this->due_date->isPast();
    }

    /**
     * Scope for pending reservations
     */
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    /**
     * Scope for active reservations
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_BORROWED,
        ]);
    }

    /**
     * Scope for user's reservations
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Approve the reservation
     */
    public function approve(): void
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'queue_position' => null,
        ]);
    }

    /**
     * Mark as borrowed
     */
    public function markAsBorrowed(int $loanDays = 14): void
    {
        $this->update([
            'status' => self::STATUS_BORROWED,
            'borrowed_at' => now(),
            'due_date' => now()->addDays($loanDays),
        ]);
        $this->book->decrementStock();
    }

    /**
     * Mark as returned
     */
    public function markAsReturned(): void
    {
        $this->update([
            'status' => self::STATUS_RETURNED,
            'returned_at' => now(),
        ]);
        $this->book->incrementStock();
    }

    /**
     * Cancel the reservation
     */
    public function cancel(string $reason = null): void
    {
        // If was borrowed, increment stock
        if ($this->status === self::STATUS_BORROWED) {
            $this->book->incrementStock();
        }

        $this->update([
            'status' => self::STATUS_CANCELLED,
            'admin_notes' => $reason ?? $this->admin_notes,
        ]);

        // Reorder queue for the book
        $this->reorderQueue();
    }

    /**
     * Reorder queue positions after cancellation
     */
    protected function reorderQueue(): void
    {
        $queuedReservations = Reservation::where('book_id', $this->book_id)
            ->where('status', self::STATUS_PENDING)
            ->whereNotNull('queue_position')
            ->orderBy('queue_position')
            ->get();

        foreach ($queuedReservations as $index => $reservation) {
            $reservation->update(['queue_position' => $index + 1]);
        }
    }
}
