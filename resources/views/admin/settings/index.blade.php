@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pengaturan</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pengaturan Website</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Informasi Umum</h5>
                        
                        <div class="form-group mb-3">
                            <label for="site_name">Nama Website</label>
                            <input type="text" class="form-control @error('site_name') is-invalid @enderror" id="site_name" name="site_name" value="{{ $settings['site_name'] ?? 'Portal Catering' }}">
                            @error('site_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="site_description">Deskripsi Website</label>
                            <textarea class="form-control @error('site_description') is-invalid @enderror" id="site_description" name="site_description" rows="3">{{ $settings['site_description'] ?? 'Portal untuk memesan berbagai jenis katering dari mitra-mitra terpercaya.' }}</textarea>
                            @error('site_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="footer_text">Teks Footer</label>
                            <input type="text" class="form-control @error('footer_text') is-invalid @enderror" id="footer_text" name="footer_text" value="{{ $settings['footer_text'] ?? '© ' . date('Y') . ' Portal Catering. All rights reserved.' }}">
                            @error('footer_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <h5 class="mb-3">Logo & Favicon</h5>
                        
                        <div class="form-group mb-3">
                            <label for="site_logo">Logo Website</label>
                            @if(isset($settings['site_logo']))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $settings['site_logo']) }}" alt="Site Logo" class="img-thumbnail" style="max-height: 100px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('site_logo') is-invalid @enderror" id="site_logo" name="site_logo">
                            @error('site_logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="site_favicon">Favicon</label>
                            @if(isset($settings['site_favicon']))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $settings['site_favicon']) }}" alt="Favicon" class="img-thumbnail" style="max-height: 50px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('site_favicon') is-invalid @enderror" id="site_favicon" name="site_favicon">
                            @error('site_favicon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="row">
                    <div class="col-md-12">
                        <h5 class="mb-3">Konten Halaman</h5>
                        
                        <div class="form-group mb-3">
                            <label for="about_content">Konten Tentang Kami</label>
                            <textarea class="form-control @error('about_content') is-invalid @enderror" id="about_content" name="about_content" rows="6">{{ $settings['about_content'] ?? 'Portal Catering adalah platform yang menghubungkan pelanggan dengan penyedia jasa katering terbaik di kota Anda. Kami berkomitmen untuk menyediakan berbagai pilihan menu katering berkualitas untuk berbagai kebutuhan.' }}</textarea>
                            @error('about_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="contact_info">Informasi Kontak</label>
                            <textarea class="form-control @error('contact_info') is-invalid @enderror" id="contact_info" name="contact_info" rows="4">{{ $settings['contact_info'] ?? '<p><i class="fas fa-map-marker-alt me-2"></i> Jl. Example No. 123, Kota Example</p><p><i class="fas fa-envelope me-2"></i> info@portalcatering.com</p><p><i class="fas fa-phone me-2"></i> (021) 1234567</p>' }}</textarea>
                            @error('contact_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Anda dapat menggunakan tag HTML sederhana.</small>
                        </div>
                    </div>
                </div>
                
                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@endsection

@section('scripts')
<script>
    // Add a WYSIWYG editor for the about_content field if needed
    // Example: CKEDITOR.replace('about_content');
</script>
@endsection