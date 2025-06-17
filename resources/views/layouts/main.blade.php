<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'Portal Catering') }}</title>
    <!-- Favicon -->
    @if($favicon = \App\Models\Setting::where('key', 'site_favicon')->first()?->value)
        <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
    <!-- Header -->
<header class="bg-dark text-white">
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if($logo = \App\Models\Setting::where('key', 'site_logo')->first()?->value)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Portal Catering" height="40">
                @else       
                    Portal Catering
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('menus.*') ? 'active' : '' }}" href="{{ route('menus.index') }}">Menu Catering</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('menus.diet') ? 'active' : '' }}" href="{{ route('menus.diet') }}">
                            <i class="fas fa-apple-alt"></i> Menu Diet
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">Tentang Kami</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <!-- Tampilkan jika user sudah login -->
                        @if(auth()->user()->isCustomer())
                            <li class="nav-item">
                                <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                                    <i class="fa fa-shopping-cart"></i> Keranjang
                                    @php
                                        $cartItemCount = auth()->user()->cart?->cartItems->count() ?? 0;
                                    @endphp
                                    @if($cartItemCount > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                            {{ $cartItemCount }}
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if(auth()->user()->isCustomer())
                                    <li><a class="dropdown-item" href="{{ route('customer.orders.index') }}">Pesanan Saya</a></li>
                                    <li><a class="dropdown-item" href="{{ route('customer.profile.edit') }}">Profil</a></li>
                                @elseif(auth()->user()->isPartner())
                                    <li><a class="dropdown-item" href="{{ route('partner.dashboard') }}">Dashboard</a></li>
                                @elseif(auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <!-- Tampilkan jika user belum login -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
</header>

    <!-- Main Content -->
    <main class="py-4">
        @if(session('success'))
            <div class="container">
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="container">
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
<footer class="bg-dark text-white py-5">
    <div class="container">
        <div class="row">
            <!-- Kolom 1: Portal Catering -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Portal Catering</h5>
                <p class="mb-3">Portal untuk memesan berbagai jenis katering dari mitra-mitra terpercaya.</p>
                <div class="social-links">
                    <a href="#" class="me-2 text-white"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="me-2 text-white"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="me-2 text-white"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            
            <!-- Kolom 2: Tautan -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Tautan</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-white text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="{{ route('menus.index') }}" class="text-white text-decoration-none">Menu Catering</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-white text-decoration-none">Tentang Kami</a></li>
                    <li class="mb-2"><a href="{{ route('login') }}" class="text-white text-decoration-none">Login</a></li>
                    <li class="mb-2"><a href="{{ route('register') }}" class="text-white text-decoration-none">Register</a></li>
                </ul>
            </div>
            
            <!-- Kolom 3: Kontak -->
            <div class="col-md-4 mb-4">
                <h5 class="mb-3">Kontak</h5>
                <p class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> Jl. Adi No. 123, Kota Salatiga</p>
                <p class="mb-2"><i class="fas fa-envelope me-2"></i> info@portalcatering.com</p>
                <p class="mb-2"><i class="fas fa-phone me-2"></i> (021) 1234567</p>
                <p class="mb-2"><i class="fas fa-clock me-2"></i> Senin - Jumat: 08.00 - 17.00</p>
            </div>
        </div>
        
        <hr class="my-4">
        
        <div class="text-center">
            <p class="mb-0">&copy; {{ date('Y') }} Portal Catering. All rights reserved.</p>
        </div>
    </div>
</footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/scripts.js') }}"></script>
    @yield('scripts')
</body>
</html>