<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index()
    {
        $stats = [
            'total_books' => Book::count(),
            'available_books' => Book::where('available_stock', '>', 0)->count(),
            'total_categories' => Category::count(),
            'total_users' => User::where('role', 'member')->count(),
            'pending_reservations' => Reservation::where('status', Reservation::STATUS_PENDING)->count(),
            'active_borrowed' => Reservation::where('status', Reservation::STATUS_BORROWED)->count(),
        ];

        // Recent reservations
        $recentReservations = Reservation::with(['user', 'book'])
            ->latest('reserved_at')
            ->limit(5)
            ->get();

        // Low stock books
        $lowStockBooks = Book::where('available_stock', '<=', 1)
            ->where('is_active', true)
            ->limit(5)
            ->get();

        // Overdue reservations
        $overdueReservations = Reservation::with(['user', 'book'])
            ->where('status', Reservation::STATUS_BORROWED)
            ->where('due_date', '<', now())
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReservations', 'lowStockBooks', 'overdueReservations'));
    }
}
