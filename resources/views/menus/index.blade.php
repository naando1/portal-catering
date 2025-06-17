@extends('layouts.main')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Menu Catering</h1>
    
    <!-- Filter & Search Form -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form action="{{ route('menus.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="category" class="form-label">Kategori</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="partner" class="form-label">Mitra Catering</label>
                    <select class="form-select" id="partner" name="partner">
                        <option value="">Semua Mitra</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner->id }}" {{ request('partner') == $partner->id ? 'selected' : '' }}>
                                {{ $partner->business_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari Menu</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="search" name="search" placeholder="Nama menu..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Menu List -->
    <div class="row g-4">
        @forelse($menus as $menu)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    @if($menu->image)
                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="card-img-top" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fa fa-image fa-3x"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">{{ $menu->category->name }}</span>
                            <div class="text-warning">
                                @php
                                    $rating = $menu->averageRating();
                                    $fullStars = floor($rating);
                                    $halfStar = $rating - $fullStars >= 0.5;
                                @endphp
                                
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $fullStars)
                                        <i class="fas fa-star"></i>
                                    @elseif($halfStar && $i == $fullStars + 1)
                                        <i class="fas fa-star-half-alt"></i>
                                    @else
                                        <i class="far fa-star"></i>
                                    @endif
                                @endfor
                                <small class="text-muted">({{ $menu->reviews->count() }})</small>
                            </div>
                        </div>
                        <h5 class="card-title">{{ $menu->name }}</h5>
                        <p class="card-text small text-muted mb-2">{{ Str::limit($menu->description, 100) }}</p>
                        <p class="fw-bold text-primary mb-0">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        <div class="text-muted small mb-3">
                            <i class="fa fa-store me-1"></i> {{ $menu->cateringPartner->business_name }}
                        </div>
                        <a href="{{ route('menus.show', $menu->id) }}" class="btn btn-outline-primary w-100">Detail Menu</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Tidak ada menu yang ditemukan. Silakan coba filter atau pencarian lain.
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-center">
        {{ $menus->withQueryString()->links() }}
    </div>
</div>
@endsection