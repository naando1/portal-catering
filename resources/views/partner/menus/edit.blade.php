@extends('layouts.partner')

@section('title', 'Edit Menu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('partner.menus.index') }}">Menu Catering</a></li>
    <li class="breadcrumb-item active">Edit Menu</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Edit Menu</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="{{ route('partner.menus.update', $menu->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">Nama Menu <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $menu->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="category_id">Kategori <span class="text-danger">*</span></label>
                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $menu->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description">Deskripsi <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $menu->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="price">Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $menu->price) }}" required min="0">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="image">Foto Menu</label>
                    @if($menu->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="img-thumbnail" style="max-height: 200px;">
                        </div>
                    @endif
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">Pilih file</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Upload foto menu dengan ukuran maksimal 2MB. Biarkan kosong jika tidak ingin mengubah foto.</small>
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_available" name="is_available" value="1" {{ old('is_available', $menu->is_available) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_available">Tersedia</label>
                    </div>
                    <small class="form-text text-muted">Centang jika menu ini tersedia untuk dipesan.</small>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="{{ route('partner.menus.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->

    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informasi Nutrisi (Opsional)</h5>
        </div>
        
        <div class="card-body">
            <p class="text-muted">
                Anda dapat mengisi informasi nutrisi menu untuk mendukung fitur menu diet. Isi sebanyak yang Anda bisa, informasi ini akan membantu customer dengan kebutuhan diet khusus.
            </p>
            
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="is_diet_menu" name="is_diet_menu" value="1" {{ old('is_diet_menu', $menu->is_diet_menu) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_diet_menu">Tandai sebagai Menu Diet</label>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="calories" class="form-label">Kalori (kkal)</label>
                    <input type="number" step="0.1" class="form-control @error('calories') is-invalid @enderror" id="calories" name="calories" value="{{ old('calories', $menu->calories) }}">
                    @error('calories')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="carbohydrates" class="form-label">Karbohidrat (g)</label>
                    <input type="number" step="0.1" class="form-control @error('carbohydrates') is-invalid @enderror" id="carbohydrates" name="carbohydrates" value="{{ old('carbohydrates', $menu->carbohydrates) }}">
                    @error('carbohydrates')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="proteins" class="form-label">Protein (g)</label>
                    <input type="number" step="0.1" class="form-control @error('proteins') is-invalid @enderror" id="proteins" name="proteins" value="{{ old('proteins', $menu->proteins) }}">
                    @error('proteins')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="fats" class="form-label">Lemak (g)</label>
                    <input type="number" step="0.1" class="form-control @error('fats') is-invalid @enderror" id="fats" name="fats" value="{{ old('fats', $menu->fats) }}">
                    @error('fats')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="sugars" class="form-label">Gula (g)</label>
                    <input type="number" step="0.1" class="form-control @error('sugars') is-invalid @enderror" id="sugars" name="sugars" value="{{ old('sugars', $menu->sugars) }}">
                    @error('sugars')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="sodium" class="form-label">Sodium (mg)</label>
                    <input type="number" step="0.1" class="form-control @error('sodium') is-invalid @enderror" id="sodium" name="sodium" value="{{ old('sodium', $menu->sodium) }}">
                    @error('sodium')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Diet Tags (Label Menu Diet)</label>
                    <div class="row">
                        @foreach($dietTags as $tag)
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="diet_tag_{{ $tag->id }}" name="diet_tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, $menuDietTags) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="diet_tag_{{ $tag->id }}">
                                        {{ $tag->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="text-end">
                <a href="{{ route('partner.menus.edit-nutrition', $menu->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-utensils me-1"></i> Edit Nutrisi Lengkap
                </a>
            </div>
        </div>
    </div>    
@endsection

@section('scripts')
<script src="{{ asset('vendor/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
$(function () {
    bsCustomFileInput.init();
});
</script>
@endsection