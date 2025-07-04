@extends('layouts.main')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menus.index') }}">Menu Catering</a></li>
            <li class="breadcrumb-item active">{{ $menu->name }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            @if($menu->image)
                                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="img-fluid rounded">
                            @else
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                                    <i class="fa fa-image fa-5x"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-primary me-2">{{ $menu->category->name }}</span>
                                <div class="text-warning">
                                    @php
                                        $rating = $menu->getAverageRating();
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
                            <h1 class="h3 mb-3">{{ $menu->name }}</h1>
                            <p class="fw-bold text-primary h4 mb-3">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                            <div class="mb-3">
                                <div class="text-muted mb-2">
                                    <i class="fa fa-store me-1"></i> <strong>Mitra Catering:</strong> {{ $menu->cateringPartner->business_name }}
                                </div>
                                <div class="text-muted">
                                    <i class="fa fa-utensils me-1"></i> <strong>Kategori:</strong> {{ $menu->category->name }}
                                </div>
                            </div>
                            
                            @auth
                                @if(auth()->user()->isCustomer())
                                    <form action="{{ route('cart.add', $menu->id) }}" method="POST">
                                        @csrf
                                        <div class="row g-2 mb-3">
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <span class="input-group-text">Qty</span>
                                                    <input type="number" name="quantity" class="form-control" value="1" min="1">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <button type="submit" class="btn btn-primary w-100">
                                                    <i class="fa fa-cart-plus me-1"></i> Tambah ke Keranjang
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 mb-3">
                                    Login untuk Memesan
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Deskripsi Menu</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $menu->description }}</p>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Ulasan ({{ $menu->reviews->count() }})</h5>
                </div>
                <div class="card-body">
                    @auth
                        @if(auth()->user()->isCustomer())
                            <div class="mb-4">
                                <h6>Berikan Ulasan</h6>
                                <form action="{{ route('reviews.store', $menu->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Rating</label>
                                        <div class="rating-stars">
                                            <div class="rate">
                                                @for($i = 5; $i >= 1; $i--)
                                                    <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ $userReview && $userReview->rating == $i ? 'checked' : '' }} />
                                                    <label for="star{{ $i }}" title="{{ $i }} stars">{{ $i }} stars</label>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comment" class="form-label">Komentar</label>
                                        <textarea class="form-control" id="comment" name="comment" rows="3">{{ $userReview ? $userReview->comment : '' }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                                </form>
                            </div>
                            <hr>
                        @endif
                    @endauth
                    
                    <div class="review-list">
                        @forelse($menu->reviews as $review)
                            <div class="review-item mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="reviewer">
                                        <span class="fw-bold">{{ $review->user->name }}</span>
                                        <span class="text-muted small ms-2">{{ $review->created_at->format('d M Y, H:i') }}</span>
                                    </div>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p class="mb-0">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <div class="text-muted">Belum ada ulasan untuk menu ini.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informasi Nutrisi</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Kalori
                                    <span class="badge bg-primary rounded-pill">{{ $menu->calories }} kkal</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Protein
                                    <span class="badge bg-success rounded-pill">{{ $menu->proteins }} g</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Karbohidrat
                                    <span class="badge bg-info rounded-pill">{{ $menu->carbohydrates }} g</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Lemak
                                    <span class="badge bg-warning rounded-pill">{{ $menu->fats }} g</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Gula
                                    <span class="badge bg-danger rounded-pill">{{ $menu->sugars }} g</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Sodium/Garam
                                    <span class="badge bg-secondary rounded-pill">{{ $menu->sodium }} mg</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Serat
                                    <span class="badge bg-success rounded-pill">{{ $menu->fiber }} g</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Jenis Karbohidrat
                                    <span class="badge bg-primary rounded-pill">{{ $menu->getCarbohydrateTypeLabel() }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informasi Tambahan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Teknik Masak:</strong> {{ $menu->getCookingMethodLabel() }}</p>
                            <p><strong>Bahan:</strong> {{ $menu->getIngredientTagsString() }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Rasa:</strong> {{ $menu->getTasteTagsString() }}</p>
                            <p><strong>Menu Diet:</strong> {{ $menu->is_diet_menu ? 'Ya' : 'Tidak' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($menu->dietTags->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Tag Diet</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($menu->dietTags as $tag)
                                <span class="badge bg-success">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                        
                        @auth
                            @if(auth()->user()->healthProfile)
                                <div class="mt-3 pt-3 border-top">
                                    <h6>Kesesuaian dengan Kondisi Kesehatan Anda:</h6>
                                    
                                    @php
                                        $suitabilityScore = $menu->getHealthSuitabilityScore(auth()->user()->healthProfile);
                                        
                                        if ($suitabilityScore >= 80) {
                                            $badgeClass = 'bg-success';
                                            $message = 'Sangat Cocok';
                                        } elseif ($suitabilityScore >= 50) {
                                            $badgeClass = 'bg-warning';
                                            $message = 'Cukup Cocok';
                                        } else {
                                            $badgeClass = 'bg-danger';
                                            $message = 'Kurang Cocok';
                                        }
                                    @endphp
                                    
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 20px;">
                                            <div class="progress-bar {{ $badgeClass }}" role="progressbar" 
                                                 style="width: {{ $suitabilityScore }}%;" 
                                                 aria-valuenow="{{ $suitabilityScore }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $suitabilityScore }}%
                                            </div>
                                        </div>
                                        <span class="badge {{ $badgeClass }} ms-2">{{ $message }}</span>
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Tentang Mitra</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($menu->cateringPartner->logo)
                            <img src="{{ asset('storage/' . $menu->cateringPartner->logo) }}" alt="{{ $menu->cateringPartner->business_name }}" class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded-circle mx-auto" style="width: 100px; height: 100px;">
                                <i class="fa fa-store fa-3x"></i>
                            </div>
                        @endif
                        <h5 class="mt-3">{{ $menu->cateringPartner->business_name }}</h5>
                    </div>
                    <p class="card-text">{{ $menu->cateringPartner->description }}</p>
                    <a href="{{ route('menus.index', ['partner' => $menu->cateringPartner->id]) }}" class="btn btn-outline-primary w-100">
                        Lihat Semua Menu
                    </a>
                </div>
            </div>
            
            @if($relatedMenus->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Menu Terkait</h5>
                    </div>
                    <div class="card-body">
                        <div class="related-menus">
                            @foreach($relatedMenus as $relatedMenu)
                                <div class="related-menu-item mb-3 pb-3 border-bottom">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0" style="width: 60px; height: 60px;">
                                            @if($relatedMenu->image)
                                                <img src="{{ asset('storage/' . $relatedMenu->image) }}" alt="{{ $relatedMenu->name }}" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                                                    <i class="fa fa-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $relatedMenu->name }}</h6>
                                            <div class="text-primary fw-bold">Rp {{ number_format($relatedMenu->price, 0, ',', '.') }}</div>
                                            <a href="{{ route('menus.show', $relatedMenu->id) }}" class="stretched-link"></a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .rating-stars .rate {
        float: left;
        height: 46px;
        padding: 0 10px;
    }
    .rating-stars .rate:not(:checked) > input {
        position:absolute;
        top:-9999px;
    }
    .rating-stars .rate:not(:checked) > label {
        float:right;
        width:1em;
        overflow:hidden;
        white-space:nowrap;
        cursor:pointer;
        font-size:30px;
        color:#ccc;
    }
    .rating-stars .rate:not(:checked) > label:before {
        content: '★ ';
    }
    .rating-stars .rate > input:checked ~ label {
        color: #ffc700;    
    }
    .rating-stars .rate:not(:checked) > label:hover,
    .rating-stars .rate:not(:checked) > label:hover ~ label {
        color: #deb217;  
    }
    .rating-stars .rate > input:checked + label:hover,
    .rating-stars .rate > input:checked + label:hover ~ label,
    .rating-stars .rate > input:checked ~ label:hover,
    .rating-stars .rate > input:checked ~ label:hover ~ label {
        color: #c59b08;
    }
</style>
@endsection