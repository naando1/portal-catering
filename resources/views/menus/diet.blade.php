{{-- resources/views/menus/diet.blade.php --}}
@extends('layouts.main')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Filter Menu Diet</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('menus.diet') }}" method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label">Cari Menu</label>
                            <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nama menu...">
                        </div>
                        
                        <div class="mb-3">
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
                        
                        <div class="mb-3">
                            <label for="diet_tag" class="form-label">Label Diet</label>
                            <select class="form-select" id="diet_tag" name="diet_tag">
                                <option value="">Semua Label</option>
                                @foreach($dietTags as $tag)
                                    <option value="{{ $tag->id }}" {{ request('diet_tag') == $tag->id ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="condition" class="form-label">Kondisi Kesehatan</label>
                            <select class="form-select" id="condition" name="condition">
                                <option value="">Semua Kondisi</option>
                                <option value="diabetes" {{ request('condition') == 'diabetes' ? 'selected' : '' }}>Diabetes</option>
                                <option value="hypertension" {{ request('condition') == 'hypertension' ? 'selected' : '' }}>Hipertensi</option>
                                <option value="heart" {{ request('condition') == 'heart' ? 'selected' : '' }}>Penyakit Jantung</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="calorie" class="form-label">Kalori</label>
                            <select class="form-select" id="calorie" name="calorie">
                                <option value="">Semua Range</option>
                                <option value="low" {{ request('calorie') == 'low' ? 'selected' : '' }}>Rendah (≤ 400 kkal)</option>
                                <option value="medium" {{ request('calorie') == 'medium' ? 'selected' : '' }}>Sedang (401-600 kkal)</option>
                                <option value="high" {{ request('calorie') == 'high' ? 'selected' : '' }}>Tinggi (> 600 kkal)</option>
                            </select>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i> Terapkan Filter
                            </button>
                            <a href="{{ route('menus.diet') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Reset Filter
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            @if(Auth::check())
                @if($hasCompleteProfile)
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">Profil Kesehatan Anda</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <div>BMI:</div>
                                <div class="fw-bold">{{ Auth::user()->getBmi() }}</div>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <div>Kategori:</div>
                                <div class="fw-bold">{{ Auth::user()->getBmiCategory() }}</div>
                            </div>
                            @if(Auth::user()->dietPreference)
                                <div class="d-flex justify-content-between mb-2">
                                    <div>Jenis Diet:</div>
                                    <div class="fw-bold">
                                        @if(Auth::user()->dietPreference->diet_type == 'regular')
                                            Diet Seimbang
                                        @elseif(Auth::user()->dietPreference->diet_type == 'low_carb')
                                            Rendah Karbohidrat
                                        @elseif(Auth::user()->dietPreference->diet_type == 'low_fat')
                                            Rendah Lemak
                                        @elseif(Auth::user()->dietPreference->diet_type == 'low_sugar')
                                            Rendah Gula
                                        @elseif(Auth::user()->dietPreference->diet_type == 'low_sodium')
                                            Rendah Garam
                                        @elseif(Auth::user()->dietPreference->diet_type == 'high_protein')
                                            Tinggi Protein
                                        @elseif(Auth::user()->dietPreference->diet_type == 'vegetarian')
                                            Vegetarian
                                        @elseif(Auth::user()->dietPreference->diet_type == 'vegan')
                                            Vegan
                                        @endif
                                    </div>
                                </div>
                            @endif
                            <div class="mt-2">
                                <a href="{{ route('customer.profile.edit', ['tab' => 'health']) }}" class="btn btn-sm btn-outline-success w-100">
                                    <i class="fas fa-edit me-1"></i> Edit Profil Kesehatan
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Dapatkan Rekomendasi</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-3">Isi profil kesehatan Anda untuk mendapatkan rekomendasi menu diet yang sesuai dengan kebutuhan Anda.</p>
                            <div class="d-grid">
                                <a href="{{ route('customer.profile.edit', ['tab' => 'health']) }}" class="btn btn-info">
                                    <i class="fas fa-clipboard-list me-1"></i> Isi Profil Kesehatan
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Rekomendasi Pribadi</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Login untuk mendapatkan rekomendasi menu diet yang dipersonalisasi sesuai kondisi kesehatan Anda.</p>
                        <div class="d-grid">
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-1"></i> Login
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-9">
            @if($hasCompleteProfile && $personalRecommendations && $personalRecommendations->count() > 0)
                <div class="mb-4">
                    <h4 class="mb-3">Rekomendasi Menu untuk Anda</h4>
                    <div class="row g-3">
                        @foreach($personalRecommendations as $menu)
                            <div class="col-md-4">
                                <div class="card h-100 shadow-sm">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->name }}" style="height: 160px; object-fit: cover;">
                                    @else
                                        <div class="bg-light text-center py-5">
                                            <i class="fas fa-utensils fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $menu->name }}</h6>
                                        <p class="card-text small text-muted mb-1">{{ $menu->cateringPartner->business_name }}</p>
                                        <p class="card-text small mb-2">{{ Str::limit($menu->description, 60) }}</p>
                                        
                                        <div class="d-flex justify-content-between mb-2">
                                            <div>
                                                <span class="badge bg-primary">{{ $menu->calories ?? '?' }} kkal</span>
                                            </div>
                                            <div>
                                                <span class="fw-bold text-success">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap gap-1 mb-2">
                                            @foreach($menu->dietTags as $tag)
                                                <span class="badge bg-info">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer bg-white">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('menus.diet.show', $menu->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                            <form action="{{ route('cart.add', $menu->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                                    <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="border-top pt-4 mb-4"></div>
            @endif
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Menu Diet</h4>
                <span class="text-muted">{{ $menus->total() }} menu ditemukan</span>
            </div>
            
            @if($menus->count() > 0)
                <div class="row g-3">
                    @foreach($menus as $menu)
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm">
                                @if($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->name }}" style="height: 160px; object-fit: cover;">
                                @else
                                    <div class="bg-light text-center py-5">
                                        <i class="fas fa-utensils fa-3x text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="card-body">
                                    <h6 class="card-title">{{ $menu->name }}</h6>
                                    <p class="card-text small text-muted mb-1">{{ $menu->cateringPartner->business_name }}</p>
                                    <p class="card-text small mb-2">{{ Str::limit($menu->description, 60) }}</p>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <span class="badge bg-primary">{{ $menu->calories ?? '?' }} kkal</span>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-success">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        @foreach($menu->dietTags as $tag)
                                            <span class="badge bg-info">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="card-footer bg-white">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('menus.diet.show', $menu->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                        <form action="{{ route('cart.add', $menu->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary w-100">
                                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4 d-flex justify-content-center">
                    {{ $menus->withQueryString()->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    Tidak ada menu diet yang ditemukan dengan filter yang dipilih. Silakan coba filter lain.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection