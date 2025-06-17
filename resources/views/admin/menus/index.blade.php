@extends('layouts.admin')

@section('title', 'Menu Catering')

@section('breadcrumb')
    <li class="breadcrumb-item active">Menu Catering</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Menu Catering</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('admin.menus.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="category" class="form-control">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="partner" class="form-control">
                            <option value="">Semua Mitra</option>
                            @foreach($partners as $partner)
                                <option value="{{ $partner->id }}" {{ request('partner') == $partner->id ? 'selected' : '' }}>
                                    {{ $partner->business_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="availability" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>Tersedia</option>
                            <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari menu..." value="{{ request('search') }}">
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
                            <th width="10">#</th>
                            <th width="80">Gambar</th>
                            <th>Nama Menu</th>
                            <th>Kategori</th>
                            <th>Mitra</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $index => $menu)
                            <tr>
                                <td>{{ $menus->firstItem() + $index }}</td>
                                <td>
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fa fa-utensils"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $menu->name }}</td>
                                <td>{{ $menu->category->name }}</td>
                                <td>{{ $menu->cateringPartner->business_name }}</td>
                                <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($menu->is_available)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-danger">Tidak Tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.menus.show', $menu->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada menu</td>
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
    <!-- /.card -->
@endsection