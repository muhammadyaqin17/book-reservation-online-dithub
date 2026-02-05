@extends('layouts.guest')

@section('title', 'Beranda - ' . config('app.name'))

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-3">Selamat Datang di Perpustakaan Digital</h1>
        <p class="lead mb-0">Temukan dan reservasi buku favoritmu dengan mudah</p>
    </div>
</section>

<!-- Search Box -->
<div class="container">
    <div class="search-box">
        <form action="{{ route('books.index') }}" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-lg-9">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0"
                               placeholder="Cari judul buku, penulis, atau ISBN..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-lg-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Cari Buku
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Stats Section -->
<div class="container mt-5">
    <div class="row g-4 text-center">
        <div class="col-md-4">
            <div class="p-4">
                <i class="bi bi-book display-4 text-primary mb-3"></i>
                <h3 class="fw-bold">{{ number_format($stats['total_books']) }}</h3>
                <p class="text-muted mb-0">Total Koleksi Buku</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4">
                <i class="bi bi-check-circle display-4 text-success mb-3"></i>
                <h3 class="fw-bold">{{ number_format($stats['available_books']) }}</h3>
                <p class="text-muted mb-0">Buku Tersedia</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="p-4">
                <i class="bi bi-folder display-4 text-info mb-3"></i>
                <h3 class="fw-bold">{{ $stats['categories'] }}</h3>
                <p class="text-muted mb-0">Kategori</p>
            </div>
        </div>
    </div>
</div>

<!-- Categories Section -->
<section class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Kategori Buku</h2>
        <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="row g-4">
        @foreach($categories as $category)
        <div class="col-6 col-md-3">
            <a href="{{ route('books.index', ['category' => $category->slug]) }}" class="text-decoration-none">
                <div class="category-card h-100">
                    <i class="{{ $category->icon ?? 'bi-book' }}"></i>
                    <h6>{{ $category->name }}</h6>
                    <small class="text-muted">{{ $category->books_count }} buku</small>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</section>

<!-- Featured Books Section -->
<section class="container mt-5 pt-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Buku Terbaru</h2>
        <a href="{{ route('books.index') }}" class="btn btn-outline-primary">
            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="row g-4">
        @forelse($featuredBooks as $book)
        <div class="col-6 col-md-4 col-lg-3">
            <div class="book-card animate-fade-in">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" class="card-img-top" alt="{{ $book->title }}">
                @else
                    <div class="card-img-top d-flex align-items-center justify-content-center">
                        <i class="bi bi-book display-1 text-white opacity-50"></i>
                    </div>
                @endif
                <div class="card-body">
                    <span class="category-badge mb-2 d-inline-block">{{ $book->category->name }}</span>
                    <h5 class="card-title">{{ $book->title }}</h5>
                    <p class="author mb-2">{{ $book->author }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        @if($book->available_stock > 0)
                            <span class="availability-badge available">
                                <i class="bi bi-check-circle me-1"></i>Tersedia
                            </span>
                        @else
                            <span class="availability-badge unavailable">
                                <i class="bi bi-clock me-1"></i>Dipinjam
                            </span>
                        @endif
                        <a href="{{ route('books.show', $book) }}" class="btn btn-sm btn-outline-primary">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <p class="text-muted mt-3">Belum ada buku tersedia.</p>
        </div>
        @endforelse
    </div>
</section>

<!-- CTA Section -->
<section class="container mt-5 pt-3">
    <div class="bg-primary text-white rounded-4 p-5 text-center" style="background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);">
        <h3 class="fw-bold mb-3">Ingin Meminjam Buku?</h3>
        <p class="mb-4">Daftar sekarang untuk mulai melakukan reservasi buku</p>
        @guest
        <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5">
            <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
        </a>
        @else
        <a href="{{ route('books.index') }}" class="btn btn-light btn-lg px-5">
            <i class="bi bi-collection me-2"></i>Lihat Katalog
        </a>
        @endguest
    </div>
</section>
@endsection
