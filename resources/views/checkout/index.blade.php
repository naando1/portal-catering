@extends('layouts.main')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Checkout</h1>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informasi Pengiriman</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('checkout.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="delivery_address" class="form-label">Alamat Pengiriman <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('delivery_address') is-invalid @enderror" id="delivery_address" name="delivery_address" rows="3" required>{{ auth()->user()->address }}</textarea>
                            @error('delivery_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="note" class="form-label">Catatan</label>
                            <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="2"></textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="payment_proof" class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" id="payment_proof" name="payment_proof" required>
                            @error('payment_proof')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Upload bukti transfer pembayaran sesuai total pesanan ke rekening mitra catering.
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary me-2">Kembali ke Keranjang</a>
                            <button type="submit" class="btn btn-primary">Proses Pesanan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Ringkasan Pesanan</h5>
                </div>
                <div class="card-body">
                    @foreach($cart->cartItems as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <strong>{{ $item->menu->name }}</strong>
                                <div class="text-muted small">{{ $item->quantity }} x Rp {{ number_format($item->menu->price, 0, ',', '.') }}</div>
                            </div>
                            <div class="text-end">
                                Rp {{ number_format($item->menu->price * $item->quantity, 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold text-primary">Rp {{ number_format($cart->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informasi Rekening</h5>
                </div>
                <div class="list-group list-group-flush">
                    @php
                        $partners = $cart->cartItems->pluck('menu.cateringPartner')->unique('id');
                    @endphp
                    
                    @foreach($partners as $partner)
                        <div class="list-group-item">
                            <h6 class="mb-1">{{ $partner->business_name }}</h6>
                            <p class="mb-0 text-muted">
                                Bank BCA<br>
                                No. Rekening: 1234567890<br>
                                Atas Nama: {{ $partner->business_name }}
                            </p>
                        </div>
                    @endforeach
                </div>
                <div class="card-footer bg-white text-muted small">
                    <i class="fa fa-info-circle me-1"></i> Silahkan transfer sesuai total pesanan ke rekening mitra catering di atas.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
