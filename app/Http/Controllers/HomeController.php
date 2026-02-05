<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application homepage.
     */
    public function index()
    {
        // Featured books (latest with stock)
        $featuredBooks = Book::with('category')
            ->active()
            ->available()
            ->latest()
            ->limit(8)
            ->get();

        // Categories with book count
        $categories = Category::active()
            ->withCount(['books' => function ($query) {
            $query->where('is_active', true);
        }])
            ->limit(8)
            ->get();

        // Statistics
        $stats = [
            'total_books' => Book::active()->count(),
            'available_books' => Book::active()->available()->count(),
            'categories' => Category::active()->count(),
        ];

        return view('home', compact('featuredBooks', 'categories', 'stats'));
    }
}
