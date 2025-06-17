@extends('layouts.partner')

@section('title', 'Menu Diet')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('partner.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Menu Diet</li>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card bg-gradient-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Menu Diet</h6>
                        <h2 class="mb-0">{{ $menus->total() }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-heartbeat fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card bg-gradient-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Menu Diet dengan Nutrisi Lengkap</h6>
                        <h2 class="mb-0">
                            {{ $menus->filter(function ($menu) {
                                return $menu->calories && $menu->carbohydrates && $menu->proteins && $menu->fats;
                            })->count() }}
                        </h2>
                    </div>
                    <div>
                        <i class="fas fa-clipboard-check fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Menu Diet</h3>
        <div class="card-tools">
            <a href="{{ route('partner.menus.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Tambah Menu Baru
            </a>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="mb-3">
            <form action="{{ route('partner.menus.diet') }}" method="GET" class="form-inline">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Cari menu..." value="{{ request('search') }}">
                    <select name="category" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach(\App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="60">Gambar</th>
                        <th>Nama Menu</th>
                        <th>Kategori</th>
                        <th>Nutrisi</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($menus as $menu)
                        <tr>
                            <td>
                                @if($menu->image)
                                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $menu->name }}</strong>
                                <div class="small text-muted">{{ Str::limit($menu->description, 50) }}</div>
                            </td>
                            <td>{{ $menu->category->name }}</td>
                            <td>
                                <div class="d-flex flex-column">
                                    <div class="small mb-1">
                                        <span class="text-danger">Kalori: {{ $menu->calories ? number_format($menu->calories, 1) . ' kkal' : '-' }}</span>
                                    </div>
                                    <div class="d-flex flex-wrap small gap-2">
                                        <span>Karbo: {{ $menu->carbohydrates ? number_format($menu->carbohydrates, 1) . 'g' : '-' }}</span>
                                        <span>Protein: {{ $menu->proteins ? number_format($menu->proteins, 1) . 'g' : '-' }}</span>
                                        <span>Lemak: {{ $menu->fats ? number_format($menu->fats, 1) . 'g' : '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $menu->is_available ? 'badge-success' : 'badge-danger' }}">
                                    {{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                                </span>
                                <div class="mt-1">
                                    <span class="badge badge-info">
                                        Menu Diet
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('partner.menus.edit-nutrition', $menu->id) }}" class="btn btn-info btn-sm" title="Edit Nutrisi">
                                        <i class="fas fa-heartbeat"></i>
                                    </a>
                                    <a href="{{ route('partner.menus.edit', $menu->id) }}" class="btn btn-warning btn-sm" title="Edit Menu">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('partner.menus.destroy', $menu->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')" title="Hapus Menu">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada menu diet. <a href="{{ route('partner.menus.create') }}">Buat menu baru</a> dan tandai sebagai menu diet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        {{ $menus->links() }}
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Panduan Menu Diet</h5>
    </div>
    <div class="card-body">
        <p>Menu diet adalah menu yang dirancang khusus untuk mendukung kondisi kesehatan atau tujuan diet tertentu. Menu diet akan muncul di rekomendasi khusus untuk pelanggan dengan profil kesehatan tertentu.</p>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card card-body mb-3">
                    <h6>Kondisi Kesehatan yang Didukung:</h6>
                    <ul>
                        <li>Diabetes (rendah gula dan karbohidrat)</li>
                        <li>Hipertensi (rendah sodium/garam)</li>
                        <li>Penyakit Jantung (rendah lemak dan sodium)</li>
                        <li>Manajemen Berat Badan (porsi kalori sesuai kebutuhan)</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card card-body mb-3">
                    <h6>Tips Membuat Menu Diet:</h6>
                    <ol>
                        <li>Isi informasi nutrisi selengkap mungkin</li>
                        <li>Gunakan label diet yang tepat untuk menu Anda</li>
                        <li>Berikan deskripsi yang jelas tentang bahan dan proses memasak</li>
                        <li>Unggah foto yang menarik dan jelas</li>
                        <li>Perhatikan panduan nutrisi untuk kondisi kesehatan tertentu</li>
                    </ol>
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i> Pastikan informasi nutrisi yang Anda berikan akurat. Menu dengan informasi nutrisi yang lengkap akan lebih sering muncul dalam rekomendasi diet.
        </div>
    </div>
</div>
@endsection