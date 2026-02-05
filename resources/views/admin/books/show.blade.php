@extends('layouts.admin')

@section('page-title', 'Detail Buku')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.books.index') }}">Buku</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            @if($book->cover_image)
                <img src="{{ asset('storage/' . $book->cover_image) }}" class="card-img-top" alt="{{ $book->title }}">
            @else
                <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="bi bi-book display-1 text-white opacity-50"></i>
                </div>
            @endif
            <div class="card-body text-center">
                <span class="badge bg-{{ $book->available_stock > 0 ? 'success' : 'danger' }} mb-2">
                    {{ $book->available_stock > 0 ? 'Tersedia' : 'Tidak Tersedia' }}
                </span>
                <h5 class="card-title">{{ $book->title }}</h5>
                <p class="text-muted">{{ $book->author }}</p>
                <div class="d-flex gap-2 justify-content-center">
                    <a href="{{ route('admin.books.edit', $book) }}" class="btn btn-warning"><i class="bi bi-pencil me-1"></i>Edit</a>
                    <a href="{{ route('books.show', $book) }}" class="btn btn-outline-primary" target="_blank"><i class="bi bi-eye me-1"></i>Lihat</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0">Informasi Buku</h5></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3"><strong>ISBN:</strong><br>{{ $book->isbn ?? '-' }}</div>
                    <div class="col-6 mb-3"><strong>Kategori:</strong><br>{{ $book->category->name }}</div>
                    <div class="col-6 mb-3"><strong>Penerbit:</strong><br>{{ $book->publisher ?? '-' }}</div>
                    <div class="col-6 mb-3"><strong>Tahun Terbit:</strong><br>{{ $book->published_year ?? '-' }}</div>
                    <div class="col-6 mb-3"><strong>Halaman:</strong><br>{{ $book->pages ?? '-' }}</div>
                    <div class="col-6 mb-3"><strong>Bahasa:</strong><br>{{ $book->language }}</div>
                    <div class="col-6 mb-3"><strong>Total Stok:</strong><br>{{ $book->total_stock }}</div>
                    <div class="col-6 mb-3"><strong>Stok Tersedia:</strong><br>{{ $book->available_stock }}</div>
                </div>
                <hr>
                <strong>Deskripsi:</strong>
                <p class="mt-2">{{ $book->description ?? 'Tidak ada deskripsi.' }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Riwayat Reservasi</h5></div>
            <div class="card-body p-0">
                @if($book->reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>Pengguna</th><th>Status</th><th>Tanggal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($book->reservations->take(10) as $reservation)
                            <tr>
                                <td>{{ $reservation->user->name }}</td>
                                <td><span class="badge {{ $reservation->status_badge_class }}">{{ $reservation->status_label }}</span></td>
                                <td>{{ $reservation->reserved_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">Belum ada reservasi</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
