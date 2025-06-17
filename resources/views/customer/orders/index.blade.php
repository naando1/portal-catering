@extends('layouts.main')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Pesanan Saya</h1>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if($orders->isEmpty())
                <div class="text-center py-5">
                    <i class="fa fa-shopping-bag fa-4x text-muted mb-3"></i>
                    <h4 class="mb-3">Belum ada pesanan</h4>
                    <p class="text-muted mb-4">Anda belum memiliki pesanan. Mulailah dengan menjelajahi menu katering kami.</p>
                    <a href="{{ route('menus.index') }}" class="btn btn-primary">Jelajahi Menu</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nomor Pesanan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->order_number }}</td>
                                    <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                    <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($order->status == 'pending')
                                            <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                        @elseif($order->status == 'confirmed')
                                            <span class="badge bg-info">Dikonfirmasi</span>
                                        @elseif($order->status == 'processing')
                                            <span class="badge bg-primary">Diproses</span>
                                        @elseif($order->status == 'delivered')
                                            <span class="badge bg-success">Dikirim</span>
                                        @elseif($order->status == 'cancelled')
                                            <span class="badge bg-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection