@extends('layouts.admin')

@section('page-title', 'Detail Pengguna')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
<li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <span class="display-6">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <h4>{{ $user->name }}</h4>
                <p class="text-muted">{{ $user->email }}</p>
                <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-primary' }} mb-3">{{ ucfirst($user->role) }}</span>
                <hr>
                <div class="text-start">
                    <p><strong>Telepon:</strong> {{ $user->phone ?? '-' }}</p>
                    <p><strong>Alamat:</strong> {{ $user->address ?? '-' }}</p>
                    <p><strong>Terdaftar:</strong> {{ $user->created_at->format('d M Y H:i') }}</p>
                </div>
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning w-100 mt-2"><i class="bi bi-pencil me-1"></i>Edit</a>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Riwayat Reservasi</h5></div>
            <div class="card-body p-0">
                @if($user->reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr><th>Buku</th><th>Status</th><th>Tanggal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($user->reservations as $reservation)
                            <tr>
                                <td>{{ Str::limit($reservation->book->title, 30) }}</td>
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
