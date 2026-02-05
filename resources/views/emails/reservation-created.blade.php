<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reservasi Buku</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%); color: #fff; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .book-card { background: #f8f9fa; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #667eea; }
        .book-card h3 { margin: 0 0 10px; color: #1e3a5f; }
        .book-card p { margin: 5px 0; color: #666; }
        .queue-info { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 15px; margin: 20px 0; text-align: center; }
        .queue-number { font-size: 32px; font-weight: bold; color: #856404; }
        .btn { display: inline-block; background: #667eea; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin-top: 20px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Reservasi Buku</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $reservation->user->name }}</strong>,</p>

            @if($reservation->queue_position)
                <p>Reservasi Anda telah kami terima. Karena buku sedang dipinjam, Anda masuk ke daftar antrian.</p>
                <div class="queue-info">
                    <p>Posisi Antrian Anda</p>
                    <div class="queue-number">#{{ $reservation->queue_position }}</div>
                    <p>Kami akan mengirim email saat buku tersedia.</p>
                </div>
            @else
                <p>Reservasi Anda telah berhasil dibuat dan sedang menunggu konfirmasi admin.</p>
            @endif

            <div class="book-card">
                <h3>{{ $reservation->book->title }}</h3>
                <p><strong>Penulis:</strong> {{ $reservation->book->author }}</p>
                <p><strong>Kategori:</strong> {{ $reservation->book->category->name }}</p>
                <p><strong>Tanggal Reservasi:</strong> {{ $reservation->reserved_at->format('d M Y H:i') }}</p>
            </div>

            @if($reservation->notes)
            <p><strong>Catatan Anda:</strong> {{ $reservation->notes }}</p>
            @endif

            <p>Anda dapat melihat status reservasi di akun Anda.</p>

            <a href="{{ route('reservations.my') }}" class="btn">Lihat Reservasi Saya</a>
        </div>
        <div class="footer">
            <p>Email ini dikirim otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
