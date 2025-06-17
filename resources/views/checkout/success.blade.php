@extends('layouts.main')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="fa fa-check-circle fa-5x text-success"></i>
            </div>
            <h1 class="h3 mb-3">Pesanan Berhasil!</h1>
            <p class="text-muted mb-4">Terima kasih atas pesanan Anda. Pesanan Anda telah kami terima dan sedang diproses.</p>
            
            <div class="mb-4">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <p class="mb-1"><strong>Nomor Order:</strong> {{ $order->order_number }}</p>
                                <p class="mb-1"><strong>Total:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning">{{ ucfirst($order->status) }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info mb-4">
                <p class="mb-0">
                    <i class="fa fa-info-circle me-1"></i> Kami akan segera memproses pesanan Anda setelah pembayaran terverifikasi. Anda dapat memantau status pesanan di halaman <a href="{{ route('customer.orders.index') }}" class="alert-link">Pesanan Saya</a>.
                </p>
            </div>
            
            <div>
                <a href="{{ route('home') }}" class="btn btn-primary me-2">Kembali ke Home</a>
                <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary">Lihat Pesanan Saya</a>
            </div>
        </div>
    </div>
</div>
@endsection