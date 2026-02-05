@extends('layouts.guest')

@section('title', $book->title . ' - ' . config('app.name'))

@section('content')
<!-- Page Header -->
<section class="hero-section py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('books.index') }}" class="text-white-50">Katalog</a></li>
                <li class="breadcrumb-item text-white active">{{ Str::limit($book->title, 30) }}</li>
            </ol>
        </nav>
    </div>
</section>

<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Book Cover -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm sticky-top" style="top: 100px;">
                @if($book->cover_image)
                    <img src="{{ asset('storage/' . $book->cover_image) }}" class="card-img-top rounded-top" alt="{{ $book->title }}">
                @else
                    <div class="card-img-top rounded-top d-flex align-items-center justify-content-center"
                         style="height: 350px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="bi bi-book display-1 text-white opacity-50"></i>
                    </div>
                @endif
                <div class="card-body text-center">
                    <!-- Availability Status -->
                    <div class="mb-3" id="availability-status">
                        @if($availability > 0)
                            <span class="badge bg-success fs-6 px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>Tersedia ({{ $availability }} eksemplar)
                            </span>
                        @else
                            <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                                <i class="bi bi-clock me-1"></i>Sedang Dipinjam
                            </span>
                            @if($queueInfo)
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="bi bi-people me-1"></i>{{ $queueInfo['count'] }} orang dalam antrian
                                </small>
                            </div>
                            @endif
                        @endif
                    </div>

                    @auth
                        @if($availability > 0)
                            <a href="{{ route('reservations.create', $book) }}" class="btn btn-reserve btn-lg w-100">
                                <i class="bi bi-bookmark-plus me-2"></i>Reservasi Sekarang
                            </a>
                        @else
                            <a href="{{ route('reservations.create', $book) }}" class="btn btn-warning btn-lg w-100">
                                <i class="bi bi-hourglass-split me-2"></i>Masuk Antrian
                            </a>
                            <small class="text-muted d-block mt-2">
                                Anda akan masuk ke daftar antrian
                            </small>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-reserve btn-lg w-100">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Login untuk Reservasi
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Book Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <span class="category-badge mb-3 d-inline-block">{{ $book->category->name }}</span>
                    <h1 class="fw-bold mb-2">{{ $book->title }}</h1>
                    <p class="lead text-muted mb-4">oleh {{ $book->author }}</p>

                    <!-- Book Info -->
                    <div class="row g-3 mb-4">
                        @if($book->isbn)
                        <div class="col-6 col-md-4">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block">ISBN</small>
                                <strong>{{ $book->isbn }}</strong>
                            </div>
                        </div>
                        @endif
                        @if($book->publisher)
                        <div class="col-6 col-md-4">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block">Penerbit</small>
                                <strong>{{ $book->publisher }}</strong>
                            </div>
                        </div>
                        @endif
                        @if($book->published_year)
                        <div class="col-6 col-md-4">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block">Tahun Terbit</small>
                                <strong>{{ $book->published_year }}</strong>
                            </div>
                        </div>
                        @endif
                        @if($book->pages)
                        <div class="col-6 col-md-4">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block">Jumlah Halaman</small>
                                <strong>{{ $book->pages }} halaman</strong>
                            </div>
                        </div>
                        @endif
                        <div class="col-6 col-md-4">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block">Bahasa</small>
                                <strong>{{ $book->language }}</strong>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="bg-light rounded p-3">
                                <small class="text-muted d-block">Total Stok</small>
                                <strong>{{ $book->total_stock }} eksemplar</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <h5 class="fw-bold mb-3">Deskripsi</h5>
                    <div class="text-muted" style="line-height: 1.8;">
                        {!! nl2br(e($book->description)) ?: '<em>Tidak ada deskripsi tersedia.</em>' !!}
                    </div>
                </div>
            </div>

            <!-- Related Books -->
            @if($relatedBooks->count() > 0)
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">Buku Terkait</h5>
                    <div class="row g-3">
                        @foreach($relatedBooks as $relatedBook)
                        <div class="col-6 col-md-3">
                            <a href="{{ route('books.show', $relatedBook) }}" class="text-decoration-none">
                                <div class="card h-100 border-0 shadow-sm">
                                    @if($relatedBook->cover_image)
                                        <img src="{{ asset('storage/' . $relatedBook->cover_image) }}" class="card-img-top" alt="{{ $relatedBook->title }}" style="height: 150px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 150px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="bi bi-book text-white opacity-50"></i>
                                        </div>
                                    @endif
                                    <div class="card-body p-2">
                                        <p class="card-title small fw-bold mb-0 text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ $relatedBook->title }}
                                        </p>
                                        <small class="text-muted">{{ $relatedBook->author }}</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Real-time availability check
function checkAvailability() {
    fetch('{{ route("books.availability", $book) }}')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('availability-status');
            if (data.available) {
                container.innerHTML = `
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <i class="bi bi-check-circle me-1"></i>Tersedia (${data.stock} eksemplar)
                    </span>
                `;
            } else {
                container.innerHTML = `
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                        <i class="bi bi-clock me-1"></i>Sedang Dipinjam
                    </span>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-people me-1"></i>${data.queue_count} orang dalam antrian
                        </small>
                    </div>
                `;
            }
        });
}

// Check every 30 seconds
setInterval(checkAvailability, 30000);
</script>
@endpush
