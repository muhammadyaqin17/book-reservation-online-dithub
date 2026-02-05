<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    /**
     * Display a listing of books (catalog).
     */
    public function index(Request $request)
    {
        $query = Book::query()
            ->with('category')
            ->active();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by availability
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->available();
            }
            elseif ($request->availability === 'unavailable') {
                $query->where('available_stock', 0);
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $allowedSorts = ['title', 'author', 'created_at', 'available_stock'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        $books = $query->paginate(12)->withQueryString();
        $categories = Category::active()->withCount('books')->get();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book)
    {
        if (!$book->is_active) {
            abort(404);
        }

        $book->load('category');

        // Check availability from cache
        $availability = Cache::remember("book:{$book->id}:availability", 60, function () use ($book) {
            return $book->available_stock;
        });

        // Get related books
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->active()
            ->limit(4)
            ->get();

        // Get queue info if book is not available
        $queueInfo = null;
        if ($availability === 0) {
            $queueInfo = [
                'count' => $book->queuedReservations()->count(),
                'estimated_wait' => $book->activeReservations()->count() * 14, // Assuming 14 days loan period
            ];
        }

        return view('books.show', compact('book', 'availability', 'relatedBooks', 'queueInfo'));
    }

    /**
     * API: Check book availability in real-time
     */
    public function checkAvailability(Book $book)
    {
        $availability = Cache::remember("book:{$book->id}:availability", 30, function () use ($book) {
            return $book->fresh()->available_stock;
        });

        return response()->json([
            'available' => $availability > 0,
            'stock' => $availability,
            'queue_count' => $book->queuedReservations()->count(),
        ]);
    }
}
