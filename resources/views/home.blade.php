@extends('layouts.main')

@section('content')
<!-- Hero Section -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">{{ $siteName }}</h1>
                <p class="lead mb-4">{{ $siteDescription }}</p>
            {{-- <a href="{{ route('menus.index') }}" class="btn btn-light btn-lg">Lihat Menu</a> --}}
            </div>
            <div class="col-lg-6 mt-4 mt-lg-0">
                <img src="{{ asset('images/hero-image.jpg') }}" alt="Catering Service" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Kategori Katering</h2>
            <p class="lead text-muted">Temukan berbagai jenis katering sesuai kebutuhan Anda</p>
        </div>
        <div class="row g-4">
            @foreach($categories as $category)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="card-img-top">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="fa fa-utensils fa-3x"></i>
                            </div>
                        @endif
                        <div class="card-body text-center">
                            <h5 class="card-title">{{ $category->name }}</h5>
                            <p class="card-text small text-muted">{{ Str::limit($category->description, 60) }}</p>
                            <a href="{{ route('menus.index', ['category' => $category->id]) }}" class="btn btn-outline-primary">Lihat Menu</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('menus.index') }}" class="btn btn-primary">Lihat Semua Kategori</a>
        </div>
    </div>
</section>

<!-- Featured Menus Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Menu Unggulan</h2>
            <p class="lead text-muted">Menu terbaik dari mitra katering kami</p>
        </div>
        <div class="row g-4">
            @foreach($featuredMenus as $menu)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="position-relative">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fa fa-image fa-3x"></i>
                                </div>
                            @endif
                            <span class="badge bg-primary position-absolute top-0 end-0 m-2">{{ $menu->category->name }}</span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $menu->name }}</h5>
                            <p class="card-text small text-muted mb-2">{{ Str::limit($menu->description, 80) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                <div class="text-warning">
                                    @php
                                        $rating = $menu->averageRating();
                                        $fullStars = floor($rating);
                                        $halfStar = $rating - $fullStars >= 0.5;
                                    @endphp
                                    
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $fullStars)
                                            <i class="fas fa-star small"></i>
                                        @elseif($halfStar && $i == $fullStars + 1)
                                            <i class="fas fa-star-half-alt small"></i>
                                        @else
                                            <i class="far fa-star small"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <div class="text-muted small mb-3">
                                <i class="fa fa-store me-1"></i> {{ $menu->cateringPartner->business_name }}
                            </div>
                            <a href="{{ route('menus.show', $menu->id) }}" class="btn btn-outline-primary w-100">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('menus.index') }}" class="btn btn-primary">Lihat Semua Menu</a>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Mitra Katering</h2>
            <p class="lead text-muted">Penyedia layanan katering terpercaya</p>
        </div>
        <div class="row g-4">
            @foreach($partners as $partner)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($partner->logo)
                                    <img src="{{ asset('storage/' . $partner->logo) }}" alt="{{ $partner->business_name }}" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle mx-auto" style="width: 100px; height: 100px;">
                                        <i class="fa fa-store fa-3x"></i>
                                    </div>
                                @endif
                            </div>
                            <h5 class="card-title">{{ $partner->business_name }}</h5>
                            <p class="card-text small text-muted">{{ Str::limit($partner->description, 100) }}</p>
                            <a href="{{ route('menus.index', ['partner' => $partner->id]) }}" class="btn btn-outline-primary">Lihat Menu</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold">Cara Pemesanan</h2>
            <p class="lead text-muted">Ikuti langkah-langkah berikut untuk memesan katering</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-rounded bg-primary text-white mb-3 mx-auto">
                            <i class="fa fa-search fa-2x"></i>
                        </div>
                        <h5 class="card-title">1. Pilih Menu</h5>
                        <p class="card-text text-muted">Jelajahi berbagai pilihan menu katering dari mitra terpercaya.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-rounded bg-primary text-white mb-3 mx-auto">
                            <i class="fa fa-shopping-cart fa-2x"></i>
                        </div>
                        <h5 class="card-title">2. Tambahkan ke Keranjang</h5>
                        <p class="card-text text-muted">Tambahkan menu yang Anda inginkan ke keranjang belanja.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="icon-rounded bg-primary text-white mb-3 mx-auto">
                            <i class="fa fa-check-circle fa-2x"></i>
                        </div>
                        <h5 class="card-title">3. Checkout & Bayar</h5>
                        <p class="card-text text-muted">Lakukan pembayaran dan tunggu pesanan Anda diproses.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="text-center">
            <h2 class="display-5 fw-bold mb-4">Siap untuk memesan katering?</h2>
            <p class="lead mb-4">Dapatkan pengalaman memesan katering terbaik dengan berbagai pilihan menu.</p>
            <a href="{{ route('menus.index') }}" class="btn btn-light btn-lg">Jelajahi Menu Sekarang</a>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    .icon-rounded {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        border-radius: 50%;
    }
</style>
@endsection