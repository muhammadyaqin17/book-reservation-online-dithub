<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendReservationNotification;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'book']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQ) use ($search) {
                        $userQ->where('name', 'ilike', "%{$search}%")
                            ->orWhere('email', 'ilike', "%{$search}%");
                    }
                    )->orWhereHas('book', function ($bookQ) use ($search) {
                        $bookQ->where('title', 'ilike', "%{$search}%");
                    }
                    );
                });
        }

        $reservations = $query->latest('reserved_at')->paginate(15)->withQueryString();
        $statuses = Reservation::getStatuses();

        return view('admin.reservations.index', compact('reservations', 'statuses'));
    }

    /**
     * Display the specified reservation.
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'book.category']);
        return view('admin.reservations.show', compact('reservation'));
    }

    /**
     * Approve a reservation.
     */
    public function approve(Reservation $reservation)
    {
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return back()->with('error', 'Reservasi tidak dapat disetujui.');
        }

        $reservation->approve();

        // Dispatch notification
        SendReservationNotification::dispatch($reservation, 'approved');

        return back()->with('success', 'Reservasi berhasil disetujui.');
    }

    /**
     * Mark reservation as borrowed.
     */
    public function markBorrowed(Request $request, Reservation $reservation)
    {
        if ($reservation->status !== Reservation::STATUS_APPROVED) {
            return back()->with('error', 'Reservasi harus disetujui terlebih dahulu.');
        }

        $book = $reservation->book;

        // Check stock
        if ($book->available_stock <= 0) {
            return back()->with('error', 'Stok buku tidak tersedia.');
        }

        $loanDays = $request->get('loan_days', 14);
        $reservation->markAsBorrowed($loanDays);

        // Clear cache
        Cache::forget("book:{$book->id}:availability");

        // Dispatch notification
        SendReservationNotification::dispatch($reservation, 'borrowed');

        return back()->with('success', 'Buku telah dipinjamkan.');
    }

    /**
     * Mark reservation as returned.
     */
    public function markReturned(Reservation $reservation)
    {
        if ($reservation->status !== Reservation::STATUS_BORROWED) {
            return back()->with('error', 'Status tidak valid untuk pengembalian.');
        }

        $reservation->markAsReturned();

        // Clear cache
        Cache::forget("book:{$reservation->book_id}:availability");

        // Dispatch notification
        SendReservationNotification::dispatch($reservation, 'returned');

        // Check and notify next in queue
        $this->notifyNextInQueue($reservation->book_id);

        return back()->with('success', 'Buku telah dikembalikan.');
    }

    /**
     * Cancel a reservation (admin).
     */
    public function cancel(Request $request, Reservation $reservation)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        if (!in_array($reservation->status, [Reservation::STATUS_PENDING, Reservation::STATUS_APPROVED, Reservation::STATUS_BORROWED])) {
            return back()->with('error', 'Reservasi tidak dapat dibatalkan.');
        }

        $reservation->cancel($request->reason ?? 'Dibatalkan oleh admin');

        // Clear cache
        Cache::forget("book:{$reservation->book_id}:availability");

        // Dispatch notification
        SendReservationNotification::dispatch($reservation, 'cancelled_by_admin');

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }

    /**
     * Notify next person in queue when book becomes available.
     */
    protected function notifyNextInQueue(int $bookId)
    {
        $nextReservation = Reservation::where('book_id', $bookId)
            ->where('status', Reservation::STATUS_PENDING)
            ->whereNotNull('queue_position')
            ->orderBy('queue_position')
            ->first();

        if ($nextReservation) {
            SendReservationNotification::dispatch($nextReservation, 'book_available');
        }
    }
}
