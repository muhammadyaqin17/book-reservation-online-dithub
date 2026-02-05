@extends('layouts.guest')

@section('title', 'Reservasi Saya - ' . config('app.name'))

@section('content')
<!-- Page Header -->
<section class="hero-section py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-white-50">Beranda</a></li>
                <li class="breadcrumb-item text-white active">Reservasi Saya</li>
            </ol>
        </nav>
        <h1 class="fw-bold mb-0 mt-2">Reservasi Saya</h1>
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

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                        <i class="bi bi-hourglass-split fs-3 text-primary"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $activeCount }}</h3>
                        <p class="text-muted mb-0">Reservasi Aktif</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                    </div>
                    <div>
                        <h3 class="fw-bold mb-0">{{ $completedCount }}</h3>
                        <p class="text-muted mb-0">Buku Dikembalikan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0"><i class="bi bi-bookmark-check me-2"></i>Daftar Reservasi</h5>
        </div>
        <div class="card-body p-0">
            @if($reservations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Buku</th>
                            <th>Status</th>
                            <th>Tanggal Reservasi</th>
                            <th>Jatuh Tempo</th>
                            <th class="pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $reservation)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    @if($reservation->book->cover_image)
                                        <img src="{{ asset('storage/' . $reservation->book->cover_image) }}"
                                             alt="" class="rounded me-3" style="width: 50px; height: 70px; object-fit: cover;">
                                    @else
                                        <div class="rounded me-3 d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 70px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="bi bi-book text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('books.show', $reservation->book) }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ Str::limit($reservation->book->title, 30) }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $reservation->book->author }}</small>
                                        @if($reservation->isInQueue())
                                            <br>
                                            <small class="text-warning">
                                                <i class="bi bi-hourglass-split me-1"></i>Antrian #{{ $reservation->queue_position }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $reservation->status_badge_class }}">
                                    {{ $reservation->status_label }}
                                </span>
                                @if($reservation->isOverdue())
                                    <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Terlambat</small>
                                @endif
                            </td>
                            <td>
                                {{ $reservation->reserved_at->format('d M Y') }}
                                <br><small class="text-muted">{{ $reservation->reserved_at->format('H:i') }}</small>
                            </td>
                            <td>
                                @if($reservation->due_date)
                                    {{ $reservation->due_date->format('d M Y') }}
                                    @if($reservation->isOverdue())
                                        <br><small class="text-danger">{{ $reservation->due_date->diffForHumans() }}</small>
                                    @else
                                        <br><small class="text-muted">{{ $reservation->due_date->diffForHumans() }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                @if($reservation->canBeCancelled())
                                <form action="{{ route('reservations.cancel', $reservation) }}" method="POST"
                                      onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle me-1"></i>Batalkan
                                    </button>
                                </form>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3 border-top">
                {{ $reservations->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="bi bi-bookmark display-1 text-muted"></i>
                <h4 class="mt-3">Belum ada reservasi</h4>
                <p class="text-muted">Mulai cari buku dan lakukan reservasi</p>
                <a href="{{ route('books.index') }}" class="btn btn-primary">
                    <i class="bi bi-collection me-1"></i>Lihat Katalog Buku
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
