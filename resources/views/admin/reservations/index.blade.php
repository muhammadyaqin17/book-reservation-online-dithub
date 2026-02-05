@extends('layouts.admin')

@section('page-title', 'Manajemen Reservasi')

@section('breadcrumb')
<li class="breadcrumb-item active">Reservasi</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-bookmark-check me-2"></i>Daftar Reservasi</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <form action="{{ route('admin.reservations.index') }}" method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama pengguna atau buku..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary me-1"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('admin.reservations.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>

        <!-- Reservations Table -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pengguna</th>
                        <th>Buku</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                    <tr>
                        <td>
                            <strong>{{ $reservation->user->name }}</strong>
                            <br><small class="text-muted">{{ $reservation->user->email }}</small>
                        </td>
                        <td>
                            {{ Str::limit($reservation->book->title, 30) }}
                            @if($reservation->isInQueue())
                                <br><small class="text-warning"><i class="bi bi-hourglass-split"></i> Antrian #{{ $reservation->queue_position }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $reservation->status_badge_class }}">{{ $reservation->status_label }}</span>
                            @if($reservation->isOverdue())
                                <br><small class="text-danger"><i class="bi bi-exclamation-triangle"></i> Terlambat</small>
                            @endif
                        </td>
                        <td>
                            {{ $reservation->reserved_at->format('d M Y H:i') }}
                            @if($reservation->due_date)
                                <br><small class="text-muted">Tempo: {{ $reservation->due_date->format('d M Y') }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group-vertical btn-group-sm">
                                @if($reservation->status === 'pending')
                                    <form action="{{ route('admin.reservations.approve', $reservation) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-check"></i> Setujui</button>
                                    </form>
                                @endif
                                @if($reservation->status === 'approved')
                                    <form action="{{ route('admin.reservations.borrow', $reservation) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-arrow-up-right"></i> Pinjam</button>
                                    </form>
                                @endif
                                @if($reservation->status === 'borrowed')
                                    <form action="{{ route('admin.reservations.return', $reservation) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-info btn-sm"><i class="bi bi-arrow-down-left"></i> Kembali</button>
                                    </form>
                                @endif
                                @if(in_array($reservation->status, ['pending', 'approved', 'borrowed']))
                                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $reservation->id }}">
                                        <i class="bi bi-x-circle"></i> Batal
                                    </button>
                                @endif
                            </div>

                            <!-- Cancel Modal -->
                            <div class="modal fade" id="cancelModal{{ $reservation->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.reservations.cancel', $reservation) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Batalkan Reservasi</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Batalkan reservasi <strong>{{ $reservation->book->title }}</strong> oleh <strong>{{ $reservation->user->name }}</strong>?</p>
                                                <div class="mb-3">
                                                    <label for="reason" class="form-label">Alasan (opsional)</label>
                                                    <textarea name="reason" class="form-control" rows="2" placeholder="Alasan pembatalan..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-danger">Batalkan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada reservasi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reservations->links() }}
    </div>
</div>
@endsection
