<!-- resources/views/cart/index.blade.php -->
@extends('layouts.main')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Keranjang Belanja</h1>
    
    @if($cart->cartItems->count() > 0)
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th class="text-center">Harga</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Subtotal</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->cartItems as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0" style="width: 60px; height: 60px;">
                                                        @if($item->menu->image)
                                                            <img src="{{ asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu->name }}" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 60px;">
                                                                <i class="fa fa-image"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ms-3">
                                                        <h6 class="mb-0">{{ $item->menu->name }}</h6>
                                                        <small class="text-muted">{{ $item->menu->cateringPartner->business_name }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">Rp {{ number_format($item->menu->price, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="input-group input-group-sm" style="width: 100px;">
                                                        <input type="number" name="quantity" class="form-control" value="{{ $item->quantity }}" min="1" onchange="this.form.submit()">
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="text-center">Rp {{ number_format($item->menu->price * $item->quantity, 0, ',', '.') }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus item ini dari keranjang?')">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah Item:</span>
                            <span>{{ $cart->cartItems->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Jumlah Menu:</span>
                            <span>{{ $cart->cartItems->sum('quantity') }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold text-primary">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">
                            Lanjutkan ke Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fa fa-shopping-cart fa-4x text-muted mb-3"></i>
                <h4 class="mb-3">Keranjang Anda kosong</h4>
                <p class="text-muted mb-4">Anda belum menambahkan menu apapun ke keranjang.</p>
                <a href="{{ route('menus.index') }}" class="btn btn-primary">Lihat Menu Catering</a>
            </div>
        </div>
    @endif
</div>
@endsection