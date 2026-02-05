<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Reservasi</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { padding: 30px; text-align: center; color: #fff; }
        .header.approved { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
        .header.borrowed { background: linear-gradient(135deg, #007bff 0%, #6610f2 100%); }
        .header.returned { background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%); }
        .header.cancelled, .header.cancelled_by_admin { background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%); }
        .header.book_available { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .status-badge { display: inline-block; padding: 8px 20px; border-radius: 20px; font-weight: bold; margin: 10px 0; }
        .status-badge.approved { background: #d4edda; color: #155724; }
        .status-badge.borrowed { background: #cce5ff; color: #004085; }
        .status-badge.returned { background: #d1ecf1; color: #0c5460; }
        .status-badge.cancelled, .status-badge.cancelled_by_admin { background: #f8d7da; color: #721c24; }
        .status-badge.book_available { background: #fff3cd; color: #856404; }
        .book-card { background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #667eea; }
        .book-card h3 { margin: 0 0 10px; color: #1e3a5f; }
        .book-card p { margin: 5px 0; color: #666; }
        .info-box { background: #e7f3ff; border: 1px solid #b6d4fe; border-radius: 8px; padding: 15px; margin: 20px 0; }
        .btn { display: inline-block; background: #667eea; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header {{ $statusType }}">
            @if($statusType === 'approved')
                <h1>✅ Reservasi Disetujui</h1>
            @elseif($statusType === 'borrowed')
                <h1>📖 Buku Dipinjam</h1>
            @elseif($statusType === 'returned')
                <h1>🎉 Terima Kasih</h1>
            @elseif($statusType === 'cancelled' || $statusType === 'cancelled_by_admin')
                <h1>❌ Reservasi Dibatalkan</h1>
            @elseif($statusType === 'book_available')
                <h1>🔔 Buku Tersedia!</h1>
            @endif
        </div>
        <div class="content">
            <p>Halo <strong>{{ $reservation->user->name }}</strong>,</p>

            @if($statusType === 'approved')
                <p>Reservasi Anda telah <span class="status-badge approved">Disetujui</span></p>
                <p>Silakan datang ke perpustakaan untuk mengambil buku.</p>
            @elseif($statusType === 'borrowed')
                <p>Buku telah <span class="status-badge borrowed">Dipinjam</span></p>
                <div class="info-box">
                    <p><strong>⏰ Jatuh Tempo:</strong> {{ $reservation->due_date?->format('d M Y') }}</p>
                    <p>Harap kembalikan buku tepat waktu untuk menghindari denda.</p>
                </div>
            @elseif($statusType === 'returned')
                <p>Buku telah <span class="status-badge returned">Dikembalikan</span></p>
                <p>Terima kasih telah meminjam dan mengembalikan buku tepat waktu.</p>
            @elseif($statusType === 'cancelled')
                <p>Reservasi telah <span class="status-badge cancelled">Dibatalkan</span> sesuai permintaan Anda.</p>
            @elseif($statusType === 'cancelled_by_admin')
                <p>Reservasi telah <span class="status-badge cancelled_by_admin">Dibatalkan oleh Admin</span></p>
                @if($reservation->admin_notes)
                    <div class="info-box">
                        <p><strong>Alasan:</strong> {{ $reservation->admin_notes }}</p>
                    </div>
                @endif
            @elseif($statusType === 'book_available')
                <p>Kabar baik! Buku yang Anda tunggu kini <span class="status-badge book_available">Tersedia</span></p>
                <p>Segera kunjungi perpustakaan untuk mengambil buku Anda.</p>
            @endif

            <div class="book-card">
                <h3>{{ $reservation->book->title }}</h3>
                <p><strong>Penulis:</strong> {{ $reservation->book->author }}</p>
                <p><strong>Kategori:</strong> {{ $reservation->book->category->name }}</p>
            </div>

            <a href="{{ route('reservations.my') }}" class="btn">Lihat Reservasi Saya</a>
        </div>
        <div class="footer">
            <p>Email ini dikirim otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
