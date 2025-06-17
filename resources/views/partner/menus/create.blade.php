@extends('layouts.partner')

@section('title', 'Tambah Menu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('partner.menus.index') }}">Menu Catering</a></li>
    <li class="breadcrumb-item active">Tambah Menu</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Form Tambah Menu</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="{{ route('partner.menus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="name">Nama Menu <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="category_id">Kategori <span class="text-danger">*</span></label>
                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="price">Harga (Rp) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" required min="0">
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="image">Foto Menu <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" accept="image/*" required>
                            <label class="custom-file-label" for="image">Pilih file</label>
                        </div>
                    </div>
                    <small class="form-text text-muted">Upload foto menu dengan ukuran maksimal 2MB.</small>
                    @error('image')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_available" name="is_available" value="1" {{ old('is_available') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_available">Tersedia</label>
                    </div>
                    <small class="form-text text-muted">Centang jika menu ini tersedia untuk dipesan.</small>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('partner.menus.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@endsection

@section('scripts')
<script src="{{ asset('vendor/adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script>
$(function () {
    bsCustomFileInput.init();
});
</script>
@endsection