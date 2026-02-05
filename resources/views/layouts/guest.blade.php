<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Book Reservation'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1e3a5f;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --bg-light: #f8f9fa;
            --text-dark: #2c3e50;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        /* Navbar */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2c5282 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .navbar .nav-link:hover {
            color: #fff !important;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2c5282 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .hero-section h1 {
            font-weight: 700;
        }

        /* Search Box */
        .search-box {
            background: white;
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-top: -3rem;
            position: relative;
            z-index: 10;
        }

        .search-box .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 2px solid #e9ecef;
        }

        .search-box .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .search-box .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }

        /* Book Cards */
        .book-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .book-card .card-img-top {
            height: 250px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .book-card .card-body {
            padding: 1.5rem;
        }

        .book-card .card-title {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-card .author {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .book-card .category-badge {
            background: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
        }

        /* Availability Badge */
        .availability-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 1rem;
            font-weight: 600;
        }

        .availability-badge.available {
            background: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }

        .availability-badge.unavailable {
            background: rgba(231, 76, 60, 0.1);
            color: var(--accent-color);
        }

        /* Category Section */
        .category-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .category-card:hover {
            border-color: var(--secondary-color);
            transform: translateY(-3px);
        }

        .category-card i {
            font-size: 2.5rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
            display: block;
        }

        .category-card h6 {
            font-weight: 600;
            margin-bottom: 0;
        }

        /* Footer */
        .footer {
            background: var(--primary-color);
            color: rgba(255,255,255,0.8);
            padding: 3rem 0 1.5rem;
            margin-top: 4rem;
        }

        .footer h5 {
            color: white;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .footer a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: white;
        }

        /* Buttons */
        .btn-reserve {
            background: var(--secondary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-reserve:hover {
            background: #2980b9;
            color: white;
            transform: scale(1.02);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.5s ease forwards;
        }

        /* Responsive Fixes */
        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }

            .search-box {
                margin-top: -2rem;
                padding: 1rem;
            }

            .book-card .card-img-top {
                height: 180px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="bi bi-book me-2"></i>{{ config('app.name', 'Book Reservation') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <i class="bi bi-house me-1"></i>Beranda
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}" href="{{ route('books.index') }}">
                                <i class="bi bi-collection me-1"></i>Katalog Buku
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">
                                    <i class="bi bi-person-plus me-1"></i>Daftar
                                </a>
                            </li>
                        @else
                            @if(Auth::user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-speedometer2 me-1"></i>Admin Panel
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('reservations.my') }}">
                                    <i class="bi bi-bookmark me-1"></i>Reservasi Saya
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle me-1"></i>{{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h5><i class="bi bi-book me-2"></i>{{ config('app.name') }}</h5>
                        <p class="small">
                            Sistem reservasi buku online untuk memudahkan anggota dalam melakukan peminjaman buku secara efisien.
                        </p>
                    </div>
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <h5>Link Cepat</h5>
                        <ul class="list-unstyled">
                            <li class="mb-2"><a href="{{ route('books.index') }}"><i class="bi bi-chevron-right me-1"></i>Katalog Buku</a></li>
                            @auth
                            <li class="mb-2"><a href="{{ route('reservations.my') }}"><i class="bi bi-chevron-right me-1"></i>Reservasi Saya</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h5>Kontak</h5>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia</li>
                            <li class="mb-2"><i class="bi bi-envelope me-2"></i>info@bookreservation.com</li>
                            <li class="mb-2"><i class="bi bi-telephone me-2"></i>(021) 123-4567</li>
                        </ul>
                    </div>
                </div>
                <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
                <div class="text-center small">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
