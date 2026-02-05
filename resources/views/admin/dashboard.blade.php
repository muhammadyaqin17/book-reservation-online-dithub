@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stats-card bg-primary">
            <i class="bi bi-book stats-icon"></i>
            <h3>{{ number_format($stats['total_books']) }}</h3>
            <p>Total Buku</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stats-card bg-success">
            <i class="bi bi-check-circle stats-icon"></i>
            <h3>{{ number_format($stats['available_books']) }}</h3>
            <p>Buku Tersedia</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stats-card bg-warning">
            <i class="bi bi-hourglass-split stats-icon"></i>
            <h3>{{ number_format($stats['pending_reservations']) }}</h3>
            <p>Menunggu Konfirmasi</p>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stats-card bg-info">
            <i class="bi bi-people stats-icon"></i>
            <h3>{{ number_format($stats['total_users']) }}</h3>
            <p>Total Anggota</p>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Reservations -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Reservasi Terbaru</h5>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if($recentReservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>Buku</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReservations as $reservation)
                            <tr>
                                <td>
                                    <strong>{{ $reservation->user->name }}</strong>
                                    <br><small class="text-muted">{{ $reservation->user->email }}</small>
                                </td>
                                <td>{{ Str::limit($reservation->book->title, 25) }}</td>
                                <td><span class="badge {{ $reservation->status_badge_class }}">{{ $reservation->status_label }}</span></td>
                                <td>{{ $reservation->reserved_at->format('d M Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-inbox display-6"></i>
                    <p class="mt-2 mb-0">Belum ada reservasi</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Overdue Reservations -->
        @if($overdueReservations->count() > 0)
        <div class="card mt-4">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle me-2"></i>Peminjaman Terlambat</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>Buku</th>
                                <th>Jatuh Tempo</th>
                                <th>Keterlambatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overdueReservations as $reservation)
                            <tr>
                                <td>{{ $reservation->user->name }}</td>
                                <td>{{ Str::limit($reservation->book->title, 25) }}</td>
                                <td>{{ $reservation->due_date->format('d M Y') }}</td>
                                <td class="text-danger">{{ $reservation->due_date->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Quick Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart me-2"></i>Statistik</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-3">
                    <span>Total Kategori</span>
                    <strong>{{ $stats['total_categories'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Buku Dipinjam</span>
                    <strong>{{ $stats['active_borrowed'] }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Dalam Antrian</span>
                    <strong>{{ $stats['pending_reservations'] }}</strong>
                </div>
            </div>
        </div>

        <!-- Low Stock Alert -->
        @if($lowStockBooks->count() > 0)
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="bi bi-exclamation-circle me-2"></i>Stok Rendah</h5>
            </div>
            <ul class="list-group list-group-flush">
                @foreach($lowStockBooks as $book)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>{{ Str::limit($book->title, 20) }}</span>
                    <span class="badge bg-danger rounded-pill">{{ $book->available_stock }} tersisa</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endsection
