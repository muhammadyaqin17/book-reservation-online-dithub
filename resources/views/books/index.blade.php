@extends('layouts.guest')

@section('title', 'Katalog Buku - ' . config('app.name'))

@section('content')
<!-- Page Header -->
<section class="hero-section py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Beranda</a></li>
                <li class="breadcrumb-item text-white active">Katalog Buku</li>
            </ol>
        </nav>
        <h1 class="fw-bold mb-0">Katalog Buku</h1>
    </div>
</section>

<div class="container mt-4">
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('books.index') }}" method="GET">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pencarian</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="Judul, penulis, ISBN..." value="{{ request('search') }}">
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori</label>
                            <select name="category" class="form-select">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->books_count }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Availability -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ketersediaan</label>
                            <select name="availability" class="form-select">
                                <option value="">Semua</option>
                                <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia</option>
                                <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Dipinjam</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Urutkan</label>
                            <select name="sort" class="form-select">
                                <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                                <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Judul (A-Z)</option>
                                <option value="author" {{ request('sort') == 'author' ? 'selected' : '' }}>Penulis</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Terapkan Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'availability', 'sort']))
                        <a href="{{ route('books.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="bi bi-x-circle me-1"></i>Reset Filter
                        </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        <!-- Books Grid -->
        <div class="col-lg-9">
            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <p class="text-muted mb-0">
                    Menampilkan {{ $books->firstItem() ?? 0 }} - {{ $books->lastItem() ?? 0 }} dari {{ $books->total() }} buku
                </p>
            </div>

            @if($books->count() > 0)
            <div class="row g-4">
                @foreach($books as $book)
                <div class="col-6 col-md-4">
                    <div class="book-card h-100">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" class="card-img-top" alt="{{ $book->title }}">
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center">
                                <i class="bi bi-book display-1 text-white opacity-50"></i>
                            </div>
                        @endif
                        <div class="card-body d-flex flex-column">
                            <span class="category-badge mb-2 d-inline-block">{{ $book->category->name }}</span>
                            <h5 class="card-title flex-grow-1">{{ $book->title }}</h5>
                            <p class="author mb-2">{{ $book->author }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                @if($book->available_stock > 0)
                                    <span class="availability-badge available">
                                        <i class="bi bi-check-circle me-1"></i>Tersedia ({{ $book->available_stock }})
                                    </span>
                                @else
                                    <span class="availability-badge unavailable">
                                        <i class="bi bi-clock me-1"></i>Dipinjam
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('books.show', $book) }}" class="btn btn-reserve w-100 mt-3">
                                <i class="bi bi-eye me-1"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-5 d-flex justify-content-center">
                {{ $books->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-search display-1 text-muted"></i>
                <h4 class="mt-3">Tidak ada buku ditemukan</h4>
                <p class="text-muted">Coba ubah filter pencarian Anda</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">Reset Filter</a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
