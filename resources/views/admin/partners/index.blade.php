@extends('layouts.admin')

@section('title', 'Daftar Mitra Catering')

@section('styles')
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    /* Tambahan CSS  */
    .dropdown-item i {
        display: inline-block;
        width: 20px;
        text-align: center;
    }
</style>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">Mitra Catering</li>
@endsection

@section('content')
    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <form action="{{ route('admin.partners.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari mitra..." value="{{ request('search') }}">
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-8 text-right">
                    <a href="{{ route('admin.partners.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Mitra
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Logo</th>
                            <th>Nama Bisnis</th>
                            <th>Pemilik</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th style="width: 100px" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($partners as $index => $partner)
                            <tr>
                                <td>{{ $partners->firstItem() + $index }}</td>
                                <td>
                                    @if($partner->logo)
                                        <img src="{{ asset('storage/' . $partner->logo) }}" alt="{{ $partner->business_name }}" class="img-thumbnail" width="50">
                                    @else
                                        <div class="bg-secondary text-white text-center" style="width: 50px; height: 50px; line-height: 50px;">
                                            <i class="fa fa-store"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $partner->business_name }}</td>
                                <td>{{ $partner->user->name }}</td>
                                <td>{{ $partner->user->email }}</td>
                                <td>
                                    @if($partner->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="actionMenu{{ $partner->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionMenu{{ $partner->id }}">
                                            <a class="dropdown-item" href="{{ route('admin.partners.show', $partner->id) }}">
                                                <i class="fas fa-eye text-info"></i> Lihat Detail
                                            </a>
                                            <a class="dropdown-item" href="{{ route('admin.partners.edit', $partner->id) }}">
                                                <i class="fas fa-edit text-warning"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.partners.toggle-status', $partner->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="dropdown-item">
                                                    <i class="fas fa-{{ $partner->is_active ? 'power-off text-secondary' : 'check text-success' }}"></i> 
                                                    {{ $partner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                            <div class="dropdown-divider"></div>
                                            <form action="{{ route('admin.partners.destroy', $partner->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus mitra ini?')">
                                                    <i class="fas fa-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada mitra catering</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
            {{ $partners->links() }}
        </div>
    </div>
    <!-- /.card -->
@endsection

@section('scripts')
<!-- jQuery, Popper.js, dan Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js"></script>

<!-- Script untuk memperbaiki masalah dropdown dan form -->
<script>
    $(document).ready(function() {
        // Inisialisasi dropdown
        $('.dropdown-toggle').dropdown();
        
        // Mencegah dropdown tertutup saat form di dalamnya diklik
        $('.dropdown-menu form').on('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endsection