@extends('layouts.partner')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Menu Baru</h3>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('partner.menus.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="card">
                            <div class="card-header">
                                <h5>Informasi Dasar Menu</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Nama Menu *</label>
                                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="price">Harga (Rp) *</label>
                                            <input type="number" name="price" id="price" class="form-control" value="{{ old('price') }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label for="description">Deskripsi Menu *</label>
                                    <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label for="image">Foto Menu *</label>
                                    <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label for="category_id">Kategori *</label>
                                    <select name="category_id" id="category_id" class="form-control" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Informasi Nutrisi</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="calories">Kalori (kkal) *</label>
                                            <input type="number" name="calories" id="calories" class="form-control" value="{{ old('calories') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="proteins">Protein (g) *</label>
                                            <input type="number" name="proteins" id="proteins" class="form-control" step="0.1" value="{{ old('proteins') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="carbs">Karbohidrat (g) *</label>
                                            <input type="number" name="carbs" id="carbs" class="form-control" step="0.1" value="{{ old('carbs') }}" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="fats">Lemak (g) *</label>
                                            <input type="number" name="fats" id="fats" class="form-control" step="0.1" value="{{ old('fats') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sugars">Gula (g) *</label>
                                            <input type="number" name="sugars" id="sugars" class="form-control" step="0.1" value="{{ old('sugars') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sodium">Garam/Natrium (mg) *</label>
                                            <input type="number" name="sodium" id="sodium" class="form-control" value="{{ old('sodium') }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="fiber">Serat (g) *</label>
                                            <input type="number" name="fiber" id="fiber" class="form-control" step="0.1" value="{{ old('fiber') }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5>Informasi Tambahan</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="cooking_method">Teknik Masak *</label>
                                            <select name="cooking_method" id="cooking_method" class="form-control" required>
                                                <option value="">Pilih Teknik Masak</option>
                                                <option value="rebus" {{ old('cooking_method') == 'rebus' ? 'selected' : '' }}>Rebus</option>
                                                <option value="goreng" {{ old('cooking_method') == 'goreng' ? 'selected' : '' }}>Goreng</option>
                                                <option value="bakar" {{ old('cooking_method') == 'bakar' ? 'selected' : '' }}>Bakar</option>
                                                <option value="kukus" {{ old('cooking_method') == 'kukus' ? 'selected' : '' }}>Kukus</option>
                                                <option value="panggang" {{ old('cooking_method') == 'panggang' ? 'selected' : '' }}>Panggang</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="carbohydrate_type">Jenis Karbohidrat *</label>
                                            <select name="carbohydrate_type" id="carbohydrate_type" class="form-control" required>
                                                <option value="">Pilih Jenis Karbohidrat</option>
                                                <option value="kompleks" {{ old('carbohydrate_type') == 'kompleks' ? 'selected' : '' }}>Kompleks</option>
                                                <option value="olahan" {{ old('carbohydrate_type') == 'olahan' ? 'selected' : '' }}>Olahan</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label>Tag Bahan</label>
                                    <div class="row">
                                        @php
                                            $ingredientTags = [
                                                'jeroan' => 'Jeroan',
                                                'daging' => 'Daging',
                                                'telur' => 'Telur',
                                                'susu' => 'Susu',
                                                'udang' => 'Udang',
                                                'kacang' => 'Kacang-kacangan',
                                                'kulit_ayam' => 'Kulit Ayam',
                                                'kuning_telur' => 'Kuning Telur'
                                            ];
                                            $oldIngredientTags = old('ingredient_tags', []);
                                        @endphp
                                        
                                        @foreach($ingredientTags as $value => $label)
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="ingredient_tags[]" 
                                                           id="ingredient_{{ $value }}" value="{{ $value }}"
                                                           {{ in_array($value, $oldIngredientTags) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="ingredient_{{ $value }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="form-group mt-3">
                                    <label>Tag Rasa</label>
                                    <div class="row">
                                        @php
                                            $tasteTags = [
                                                'pedas' => 'Pedas',
                                                'manis' => 'Manis',
                                                'gurih' => 'Gurih',
                                                'asin' => 'Asin',
                                                'saus_asin' => 'Saus Asin'
                                            ];
                                            $oldTasteTags = old('taste_tags', []);
                                        @endphp
                                        
                                        @foreach($tasteTags as $value => $label)
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="taste_tags[]" 
                                                           id="taste_{{ $value }}" value="{{ $value }}"
                                                           {{ in_array($value, $oldTasteTags) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="taste_{{ $value }}">
                                                        {{ $label }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                
                                <div class="form-check mt-3">
                                    <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" 
                                           {{ old('is_available', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_available">
                                        Menu Tersedia
                                    </label>
                                </div>
                                
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_diet_menu" id="is_diet_menu" value="1" 
                                           {{ old('is_diet_menu') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_diet_menu">
                                        Menu Diet
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Simpan Menu</button>
                            <a href="{{ route('partner.menus.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection