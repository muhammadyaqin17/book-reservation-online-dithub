@extends('layouts.guest')

@section('title', 'Reservasi Buku - ' . config('app.name'))

@section('content')
<!-- Page Header -->
<section class="hero-section py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Beranda</a></li>
                <li class="breadcrumb-item"><a href="{{ route('books.show', $book) }}" class="text-white-50">{{ Str::limit($book->title, 20) }}</a></li>
                <li class="breadcrumb-item text-white active">Reservasi</li>
            </ol>
        </nav>
    </div>
</section>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h2 class="fw-bold mb-4">
                        <i class="bi bi-bookmark-plus me-2 text-primary"></i>Reservasi Buku
                    </h2>

                    <!-- Book Info -->
                    <div class="d-flex gap-3 mb-4 p-3 bg-light rounded">
                        @if($book->cover_image)
                            <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}"
                                 style="width: 80px; height: 120px; object-fit: cover;" class="rounded">
                        @else
                            <div class="d-flex align-items-center justify-content-center rounded"
                                 style="width: 80px; height: 120px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="bi bi-book text-white"></i>
                            </div>
                        @endif
                        <div>
                            <h5 class="fw-bold mb-1">{{ $book->title }}</h5>
                            <p class="text-muted mb-2">{{ $book->author }}</p>
                            <span class="category-badge">{{ $book->category->name }}</span>
                        </div>
                    </div>

                    <!-- Status Info -->
                    @if($availability > 0)
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Buku tersedia!</strong> Stok saat ini: {{ $availability }} eksemplar.
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Buku sedang dipinjam.</strong> Anda akan masuk ke daftar antrian di posisi #{{ $queuePosition }}.
                            <br><small>Kami akan mengirim email notifikasi saat buku tersedia.</small>
                        </div>
                    @endif

                    <!-- Reservation Form -->
                    <form action="{{ route('reservations.store', $book) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="notes" class="form-label fw-semibold">Catatan (opsional)</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                      rows="3" placeholder="Tambahkan catatan untuk admin jika diperlukan...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="border-top pt-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-reserve btn-lg">
                                    @if($availability > 0)
                                        <i class="bi bi-bookmark-check me-2"></i>Konfirmasi Reservasi
                                    @else
                                        <i class="bi bi-hourglass-split me-2"></i>Masuk Antrian
                                    @endif
                                </button>
                                <a href="{{ route('books.show', $book) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-info-circle me-2 text-info"></i>Informasi Peminjaman</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Masa peminjaman adalah 14 hari</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Anda dapat membatalkan reservasi sebelum buku dipinjam</li>
                        <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i>Notifikasi akan dikirim melalui email</li>
                        <li><i class="bi bi-check2 text-success me-2"></i>Harap kembalikan buku tepat waktu</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
