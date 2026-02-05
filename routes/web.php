<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\BookController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- */

// Public routes
Route::get('/', [HomeController::class , 'index'])->name('home');

// Book catalog (public)
Route::get('/books', [BookController::class , 'index'])->name('books.index');
Route::get('/books/{book:slug}', [BookController::class , 'show'])->name('books.show');
Route::get('/api/books/{book}/availability', [BookController::class , 'checkAvailability'])->name('books.availability');

// Authentication routes
Auth::routes();

// Member routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/my-reservations', [ReservationController::class , 'myReservations'])->name('reservations.my');
    Route::get('/books/{book:slug}/reserve', [ReservationController::class , 'create'])->name('reservations.create');
    Route::post('/books/{book}/reserve', [ReservationController::class , 'store'])->name('reservations.store');
    Route::delete('/reservations/{reservation}', [ReservationController::class , 'cancel'])->name('reservations.cancel');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class , 'index'])->name('dashboard');

    // Books
    Route::resource('books', Admin\BookController::class);

    // Categories
    Route::resource('categories', Admin\CategoryController::class)->except(['show']);

    // Reservations
    Route::get('reservations', [Admin\ReservationController::class , 'index'])->name('reservations.index');
    Route::get('reservations/{reservation}', [Admin\ReservationController::class , 'show'])->name('reservations.show');
    Route::post('reservations/{reservation}/approve', [Admin\ReservationController::class , 'approve'])->name('reservations.approve');
    Route::post('reservations/{reservation}/borrow', [Admin\ReservationController::class , 'markBorrowed'])->name('reservations.borrow');
    Route::post('reservations/{reservation}/return', [Admin\ReservationController::class , 'markReturned'])->name('reservations.return');
    Route::post('reservations/{reservation}/cancel', [Admin\ReservationController::class , 'cancel'])->name('reservations.cancel');

    // Users
    Route::resource('users', Admin\UserController::class);
});
