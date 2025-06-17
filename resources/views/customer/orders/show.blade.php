@extends('layouts.main')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('customer.orders.index') }}">Pesanan Saya</a></li>
            <li class="breadcrumb-item active">Detail Pesanan</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Pesanan #{{ $order->order_number }}</h5>
                    <span class="badge 
                        @if($order->status == 'pending') bg-warning
                        @elseif($order->status == 'confirmed') bg-info
                        @elseif($order->status == 'processing') bg-primary
                        @elseif($order->status == 'delivered') bg-success
                        @elseif($order->status == 'cancelled') bg-danger
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Informasi Pesanan</h6>
                            <p class="mb-1"><small><strong>Tanggal Pemesanan:</strong> {{ $order->created_at->format('d M Y, H:i') }}</small></p>
                            <p class="mb-1"><small><strong>Status:</strong> 
                                @if($order->status == 'pending')
                                    Menunggu Konfirmasi
                                @elseif($order->status == 'confirmed')
                                    Dikonfirmasi
                                @elseif($order->status == 'processing')
                                    Diproses
                                @elseif($order->status == 'delivered')
                                    Dikirim
                                @elseif($order->status == 'cancelled')
                                    Dibatalkan
                                @endif
                            </small></p>
                            <p class="mb-0"><small><strong>Catatan:</strong> {{ $order->note ?: 'Tidak ada catatan' }}</small></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Informasi Pengiriman</h6>
                            <p class="mb-1"><small><strong>Nama:</strong> {{ $order->user->name }}</small></p>
                            <p class="mb-0"><small><strong>Alamat Pengiriman:</strong> {{ $order->delivery_address }}</small></p>
                        </div>
                    </div>
                    
                    <h6 class="mb-3">Item Pesanan</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Harga</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0" style="width: 50px; height: 50px;">
                                                    @if($item->menu->image)
                                                        <img src="{{ asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu->name }}" class="img-fluid rounded">
                                                    @else
                                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded" style="width: 50px; height: 50px;">
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
                                        <td class="text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th class="text-end">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Status Pembayaran</h5>
                </div>
                <div class="card-body">
                    @if($order->payment)
                        <div class="text-center mb-3">
                            @if($order->payment->status == 'pending')
                                <div class="badge bg-warning py-2 px-3 mb-3">Menunggu Verifikasi</div>
                            @elseif($order->payment->status == 'verified')
                                <div class="badge bg-success py-2 px-3 mb-3">Terverifikasi</div>
                            @elseif($order->payment->status == 'rejected')
                                <div class="badge bg-danger py-2 px-3 mb-3">Ditolak</div>
                            @endif
                        </div>
                        
                        <p class="mb-3"><strong>Total Pembayaran:</strong> Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</p>
                        
                        @if($order->payment->payment_proof)
                            <div class="mb-4">
                                <p class="mb-2"><strong>Bukti Pembayaran:</strong></p>
                                <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" alt="Bukti Pembayaran" class="img-fluid rounded border">
                            </div>
                        @endif
                        
                        @if($order->payment->status == 'rejected')
                            <form action="{{ route('customer.orders.payment', $order->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="payment_proof" class="form-label">Upload Bukti Pembayaran Baru</label>
                                    <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" id="payment_proof" name="payment_proof" required>
                                    @error('payment_proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Upload Bukti Baru</button>
                                </div>
                                <div class="form-text text-center mt-2">
                                    Silakan upload bukti pembayaran yang valid.
                                </div>
                            </form>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-times-circle fa-3x text-danger mb-3"></i>
                            <h5 class="mb-3">Belum Ada Pembayaran</h5>
                            <p class="text-muted">Silakan lakukan pembayaran untuk pesanan ini.</p>
                            
                            <form action="{{ route('customer.orders.payment', $order->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="payment_proof" class="form-label">Upload Bukti Pembayaran</label>
                                    <input type="file" class="form-control @error('payment_proof') is-invalid @enderror" id="payment_proof" name="payment_proof" required>
                                    @error('payment_proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">Upload Bukti Pembayaran</button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Informasi Rekening</h5>
                </div>
                <div class="card-body">
                    @php
                        $partners = $order->orderItems->pluck('menu.cateringPartner')->unique('id');
                    @endphp
                    
                    @foreach($partners as $partner)
                        <div class="mb-3">
                            <h6>{{ $partner->business_name }}</h6>
                            <p class="mb-0 text-muted">
                                Bank BCA<br>
                                No. Rekening: 1234567890<br>
                                Atas Nama: {{ $partner->business_name }}
                            </p>
                        </div>
                    @endforeach
                    
                    <div class="alert alert-info mb-0">
                        <i class="fa fa-info-circle me-1"></i> Silakan transfer tepat sesuai nominal total pesanan.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection