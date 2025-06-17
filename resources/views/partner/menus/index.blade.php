@extends('layouts.partner')

@section('title', 'Menu Catering')

@section('breadcrumb')
    <li class="breadcrumb-item active">Menu Catering</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Menu Catering</h3>
            <div class="card-tools">
                <a href="{{ route('partner.menus.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Menu
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('partner.menus.index') }}" method="GET" class="form-inline">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari menu..." value="{{ request('search') }}">
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
                            <th style="width: 10px">#</th>
                            <th>Foto</th>
                            <th>Nama Menu</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th style="width: 170px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $index => $menu)
                            <tr>
                                <td>{{ $menus->firstItem() + $index }}</td>
                                <td>
                                    @if($menu->image)
                                        <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                            <i class="fa fa-image"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $menu->name }}</td>
                                <td>{{ $menu->category->name }}</td>
                                <td>Rp {{ number_format($menu->price, 0, ',', '.') }}</td>
                                <td>
                                    @if($menu->is_available)
                                        <span class="badge badge-success">Tersedia</span>
                                    @else
                                        <span class="badge badge-danger">Tidak Tersedia</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('partner.menus.show', $menu->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('partner.menus.edit', $menu->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('partner.menus.destroy', $menu->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada menu</td>
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