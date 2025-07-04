@extends('layouts.main')

@section('title', 'Menu Diet Sehat')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Rekomendasi Menu Diet</h1>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Profil Kesehatan Anda</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>Status BMI</h6>
                        <p class="mb-1">BMI: <strong>{{ number_format($userMetrics['bmi'], 1) }}</strong></p>
                        <p class="mb-0">Kategori: <strong>{{ $userMetrics['bmi_category'] }}</strong></p>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Kebutuhan Kalori</h6>
                        <p class="mb-1">BMR: <strong>{{ number_format($userMetrics['bmr']) }} kkal/hari</strong></p>
                        <p class="mb-1">TDEE: <strong>{{ number_format($userMetrics['tdee']) }} kkal/hari</strong></p>
                        <p class="mb-0">Target: <strong>{{ number_format($userMetrics['target_calories']) }} kkal/hari</strong></p>
                    </div>
                    
                    <div class="mb-0">
                        <h6>Kondisi Kesehatan</h6>
                        <ul class="list-unstyled mb-0">
                            @if(Auth::user()->healthProfile->has_diabetes)
                                <li><i class="fas fa-check-circle text-danger"></i> Diabetes</li>
                            @endif
                            @if(Auth::user()->healthProfile->has_hypertension)
                                <li><i class="fas fa-check-circle text-danger"></i> Hipertensi</li>
                            @endif
                            @if(Auth::user()->healthProfile->has_heart_disease)
                                <li><i class="fas fa-check-circle text-danger"></i> Penyakit Jantung</li>
                            @endif
                            @if(Auth::user()->healthProfile->has_cholesterol)
                                <li><i class="fas fa-check-circle text-danger"></i> Kolesterol Tinggi</li>
                            @endif
                            @if(Auth::user()->healthProfile->has_hemorrhoids)
                                <li><i class="fas fa-check-circle text-danger"></i> Ambeien</li>
                            @endif
                            @if(!Auth::user()->healthProfile->has_diabetes && 
                                !Auth::user()->healthProfile->has_hypertension && 
                                !Auth::user()->healthProfile->has_heart_disease && 
                                !Auth::user()->healthProfile->has_cholesterol && 
                                !Auth::user()->healthProfile->has_hemorrhoids)
                                <li><i class="fas fa-check-circle text-success"></i> Tidak ada kondisi khusus</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Menu Rekomendasi untuk Anda</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Rekomendasi menu berdasarkan kondisi kesehatan dan preferensi Anda.</p>
                    
                    @if($recommendedMenus->isEmpty())
                        <div class="alert alert-info">
                            Belum ada rekomendasi menu yang sesuai. Silakan perbarui preferensi diet Anda atau hubungi kami untuk konsultasi.
                        </div>
                    @else
                        <div class="row">
                            @foreach($recommendedMenus as $menu)
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 shadow-sm">
                                        @if($menu->image)
                                            <img src="{{ asset('storage/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->name }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                <i class="fas fa-utensils fa-3x text-secondary"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $menu->name }}</h5>
                                            <p class="card-text text-muted">{{ Str::limit($menu->description, 100) }}</p>
                                            
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-info">{{ $menu->calories }} kkal</span>
                                                <span class="badge bg-success">{{ number_format($menu->price, 0, ',', '.') }} IDR</span>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-4 text-center">
                                                    <small class="d-block text-muted">Protein</small>
                                                    <span>{{ $menu->proteins }}g</span>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <small class="d-block text-muted">Karbo</small>
                                                    <span>{{ $menu->carbohydrates }}g</span>
                                                </div>
                                                <div class="col-4 text-center">
                                                    <small class="d-block text-muted">Lemak</small>
                                                    <span>{{ $menu->fats }}g</span>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between">
                                                <a href="{{ route('menus.show', $menu->id) }}" class="btn btn-outline-primary btn-sm">Detail</a>
                                                
                                                <form action="{{ route('cart.add', $menu->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-primary btn-sm">
                                                        <i class="fas fa-cart-plus"></i> Tambah
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="card-footer bg-white">
                                            <small class="text-muted">Kesesuaian: {{ number_format($menu->similarity_score * 100, 0) }}%</small>
                                            
                                            <button type="button" class="btn btn-sm btn-outline-secondary float-end" data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $menu->id }}">
                                                <i class="fas fa-thumbs-up"></i> Feedback
                                            </button>
                                            
                                            <!-- Modal Feedback -->
                                            <div class="modal fade" id="feedbackModal{{ $menu->id }}" tabindex="-1" aria-labelledby="feedbackModalLabel{{ $menu->id }}" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('menus.feedback', $menu->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="feedbackModalLabel{{ $menu->id }}">Feedback untuk {{ $menu->name }}</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label">Apakah rekomendasi ini relevan untuk Anda?</label>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="is_relevant" id="relevant_yes{{ $menu->id }}" value="1" checked>
                                                                        <label class="form-check-label" for="relevant_yes{{ $menu->id }}">
                                                                            Ya, rekomendasi ini sesuai dengan kebutuhan saya
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="is_relevant" id="relevant_no{{ $menu->id }}" value="0">
                                                                        <label class="form-check-label" for="relevant_no{{ $menu->id }}">
                                                                            Tidak, rekomendasi ini kurang sesuai
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="comment{{ $menu->id }}" class="form-label">Komentar (opsional)</label>
                                                                    <textarea class="form-control" id="comment{{ $menu->id }}" name="comment" rows="3"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-primary">Kirim Feedback</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Rekomendasi Kalori per Waktu Makan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h6>Sarapan</h6>
                                    <h4 class="text-primary">{{ round($userMetrics['target_calories'] * 0.25) }} kkal</h4>
                                    <small class="text-muted">25% dari total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h6>Makan Siang</h6>
                                    <h4 class="text-primary">{{ round($userMetrics['target_calories'] * 0.35) }} kkal</h4>
                                    <small class="text-muted">35% dari total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h6>Makan Malam</h6>
                                    <h4 class="text-primary">{{ round($userMetrics['target_calories'] * 0.30) }} kkal</h4>
                                    <small class="text-muted">30% dari total</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h6>Camilan</h6>
                                    <h4 class="text-primary">{{ round($userMetrics['target_calories'] * 0.10) }} kkal</h4>
                                    <small class="text-muted">10% dari total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($feedbackRecommendations->isNotEmpty())
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Menu Populer untuk Kondisi Kesehatan Anda</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Menu yang disukai oleh pengguna lain dengan kondisi kesehatan serupa.</p>
                    
                    <div class="row">
                        @foreach($feedbackRecommendations as $menu)
                            <div class="col-md-6 col-lg-3 mb-4">
                                <div class="card h-100 shadow-sm">
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" class="card-img-top" alt="{{ $menu->name }}" style="height: 150px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 150px;">
                                            <i class="fas fa-utensils fa-3x text-secondary"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $menu->name }}</h6>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-info">{{ $menu->calories }} kkal</span>
                                            <span class="badge bg-success">{{ number_format($menu->price, 0, ',', '.') }} IDR</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('menus.show', $menu->id) }}" class="btn btn-outline-primary btn-sm">Detail</a>
                                            
                                            <form action="{{ route('cart.add', $menu->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </form>
                                        </div>
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

@push('styles')
<style>
.menu-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
}

.menu-card {
    transition: transform 0.3s ease;
}

.badge {
    font-size: 0.75em;
}

.text-orange {
    color: #fd7e14 !important;
}

@media (max-width: 768px) {
    .card-img-top {
        height: 150px !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form on select change (optional)
    $('.form-select').on('change', function() {
        if ($(this).closest('form').length) {
            // Optional: auto-submit on filter change
            // $(this).closest('form').submit();
        }
    });
    
    // Toggle filter collapse icon
    $('#filterCollapse').on('show.bs.collapse', function() {
        $('.card-header i.fa-chevron-down').removeClass('fa-chevron-down').addClass('fa-chevron-up');
    });
    
    $('#filterCollapse').on('hide.bs.collapse', function() {
        $('.card-header i.fa-chevron-up').removeClass('fa-chevron-up').addClass('fa-chevron-down');
    });
});
</script>
@endpush
@endsection