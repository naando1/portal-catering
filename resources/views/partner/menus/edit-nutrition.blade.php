@extends('layouts.partner')

@section('title', 'Edit Informasi Nutrisi Menu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('partner.menus.index') }}">Menu</a></li>
    <li class="breadcrumb-item active">Edit Nutrisi</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Informasi Nutrisi {{ $menu->name }}</h3>
    </div>
    
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <form action="{{ route('partner.menus.update-nutrition', $menu->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="calories" class="form-label">Kalori (kkal)</label>
                        <input type="number" step="0.1" class="form-control @error('calories') is-invalid @enderror" id="calories" name="calories" value="{{ old('calories', $menu->calories) }}">
                        @error('calories')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jumlah kalori dalam kilocalories (kkal).</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="carbohydrates" class="form-label">Karbohidrat (g)</label>
                        <input type="number" step="0.1" class="form-control @error('carbohydrates') is-invalid @enderror" id="carbohydrates" name="carbohydrates" value="{{ old('carbohydrates', $menu->carbohydrates) }}">
                        @error('carbohydrates')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jumlah karbohidrat dalam gram.</div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="proteins" class="form-label">Protein (g)</label>
                        <input type="number" step="0.1" class="form-control @error('proteins') is-invalid @enderror" id="proteins" name="proteins" value="{{ old('proteins', $menu->proteins) }}">
                        @error('proteins')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jumlah protein dalam gram.</div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fats" class="form-label">Lemak (g)</label>
                        <input type="number" step="0.1" class="form-control @error('fats') is-invalid @enderror" id="fats" name="fats" value="{{ old('fats', $menu->fats) }}">
                        @error('fats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jumlah lemak total dalam gram.</div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="sugars" class="form-label">Gula (g)</label>
                        <input type="number" step="0.1" class="form-control @error('sugars') is-invalid @enderror" id="sugars" name="sugars" value="{{ old('sugars', $menu->sugars) }}">
                        @error('sugars')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jumlah gula dalam gram.</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="sodium" class="form-label">Sodium (mg)</label>
                        <input type="number" step="0.1" class="form-control @error('sodium') is-invalid @enderror" id="sodium" name="sodium" value="{{ old('sodium', $menu->sodium) }}">
                        @error('sodium')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jumlah sodium dalam miligram.</div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="fiber" class="form-label">Serat (g)</label>
                        <input type="number" step="0.1" class="form-control @error('fiber') is-invalid @enderror" id="fiber" name="fiber" value="{{ old('fiber', $menu->fiber) }}">
                        @error('fiber')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Jumlah serat dalam gram.</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="is_diet_menu" name="is_diet_menu" value="1" {{ old('is_diet_menu', $menu->is_diet_menu) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_diet_menu">Tandai sebagai Menu Diet</label>
                </div>
                <div class="form-text">
                    Menu diet akan ditampilkan dalam rekomendasi diet untuk pelanggan yang memiliki profil kesehatan tertentu.
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label">Diet Tags (Label Menu Diet)</label>
                <div class="row">
                    @foreach($dietTags as $tag)
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="diet_tag_{{ $tag->id }}" name="diet_tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, $menuDietTags) ? 'checked' : '' }}>
                                <label class="form-check-label" for="diet_tag_{{ $tag->id }}">
                                    {{ $tag->name }}
                                    @if($tag->description)
                                        <i class="fas fa-info-circle text-muted" data-toggle="tooltip" title="{{ $tag->description }}"></i>
                                    @endif
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="form-text">
                    Pilih label yang sesuai dengan menu ini untuk membantu pelanggan menemukan menu yang cocok dengan kebutuhan diet mereka.
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-info">
                    <h5 class="mb-0">Panduan Informasi Nutrisi untuk Kondisi Kesehatan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-heartbeat text-danger me-2"></i> Diabetes</h6>
                            <ul class="small">
                                <li>Gula: &lt; 5g</li>
                                <li>Karbohidrat: &lt; 30g (ideal)</li>
                                <li>Serat: &gt; 3g (ideal)</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><i class="fas fa-heart text-danger me-2"></i> Hipertensi</h6>
                            <ul class="small">
                                <li>Sodium: &lt; 400mg</li>
                                <li>Lemak: &lt; 10g (ideal)</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><i class="fas fa-lungs text-danger me-2"></i> Penyakit Jantung</h6>
                            <ul class="small">
                                <li>Lemak: &lt; 10g</li>
                                <li>Sodium: &lt; 400mg</li>
                                <li>Serat: &gt; 3g (ideal)</li>
                            </ul>
                        </div>
                        
                        <div class="col-md-6">
                            <h6><i class="fas fa-weight text-warning me-2"></i> Berat Badan</h6>
                            <ul class="small">
                                <li>Menu diet rendah kalori: &lt; 400 kkal</li>
                                <li>Menu diet sedang: 400-600 kkal</li>
                                <li>Protein tinggi (untuk diet): &gt; 20g</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-end">
                <a href="{{ route('partner.menus.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Informasi Nutrisi</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
@endsection