<?php

namespace App\Http\Controllers;

use App\Jobs\SendReservationNotification;
use App\Models\Book;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user's reservations.
     */
    public function myReservations()
    {
        $reservations = Reservation::with('book.category')
            ->forUser(Auth::id())
            ->latest('reserved_at')
            ->paginate(10);

        $activeCount = Reservation::forUser(Auth::id())->active()->count();
        $completedCount = Reservation::forUser(Auth::id())
            ->where('status', Reservation::STATUS_RETURNED)
            ->count();

        return view('reservations.my', compact('reservations', 'activeCount', 'completedCount'));
    }

    /**
     * Show the reservation form.
     */
    public function create(Book $book)
    {
        if (!$book->is_active) {
            abort(404);
        }

        // Check if user already has active reservation for this book
        $existingReservation = Reservation::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->active()
            ->first();

        if ($existingReservation) {
            return back()->with('error', 'Anda sudah memiliki reservasi aktif untuk buku ini.');
        }

        $availability = $book->getCachedAvailability();
        $queuePosition = $availability > 0 ? null : $book->getNextQueuePosition();

        return view('reservations.create', compact('book', 'availability', 'queuePosition'));
    }

    /**
     * Store a newly created reservation.
     */
    public function store(Request $request, Book $book)
    {
        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if user already has active reservation for this book
        $existingReservation = Reservation::where('user_id', Auth::id())
            ->where('book_id', $book->id)
            ->active()
            ->first();

        if ($existingReservation) {
            return back()->with('error', 'Anda sudah memiliki reservasi aktif untuk buku ini.');
        }

        try {
            DB::beginTransaction();

            $availability = $book->fresh()->available_stock;
            $queuePosition = null;

            // If no stock available, add to queue
            if ($availability <= 0) {
                $queuePosition = $book->getNextQueuePosition();
            }

            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'book_id' => $book->id,
                'queue_position' => $queuePosition,
                'status' => Reservation::STATUS_PENDING,
                'reserved_at' => now(),
                'notes' => $request->notes,
            ]);

            // Clear cache
            Cache::forget("book:{$book->id}:availability");

            // Dispatch notification job
            SendReservationNotification::dispatch($reservation, 'created');

            DB::commit();

            $message = $queuePosition
                ? "Reservasi berhasil! Anda berada di posisi antrian #{$queuePosition}."
                : 'Reservasi berhasil dibuat! Menunggu konfirmasi admin.';

            return redirect()->route('reservations.my')->with('success', $message);
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }

    /**
     * Cancel a reservation.
     */
    public function cancel(Reservation $reservation)
    {
        // Check ownership
        if ($reservation->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$reservation->canBeCancelled()) {
            return back()->with('error', 'Reservasi tidak dapat dibatalkan.');
        }

        $reservation->cancel('Dibatalkan oleh pengguna');

        // Clear cache
        Cache::forget("book:{$reservation->book_id}:availability");

        // Dispatch notification
        SendReservationNotification::dispatch($reservation, 'cancelled');

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }
}
