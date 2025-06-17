@extends('layouts.partner')

@section('title', 'Detail Pesanan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('partner.orders.index') }}">Pesanan</a></li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Pesanan #{{ $order->order_number }}</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Informasi Pesanan</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px">Nomor Pesanan</th>
                            <td>{{ $order->order_number }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <form action="{{ route('partner.orders.status.update', $order->id) }}" method="POST" class="d-flex align-items-center">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-control form-control-sm" style="width: 150px">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm ml-2">Update</button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Catatan</th>
                            <td>{{ $order->note ?: 'Tidak ada catatan' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>Informasi Customer</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 200px">Nama Customer</th>
                            <td>{{ $order->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $order->user->email }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $order->user->phone_number ?: '-' }}</td>
                        </tr>
                        <tr>
                            <th>Alamat Pengiriman</th>
                            <td>{{ $order->delivery_address }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <h5>Item Pesanan (Menu Anda)</h5>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Menu</th>
                            <th class="text-center">Harga</th>
                            <th class="text-center">Jumlah</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partnerOrderItems as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($item->menu->image)
                                            <img src="{{ asset('storage/' . $item->menu->image) }}" alt="{{ $item->menu->name }}" class="img-thumbnail mr-2" style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                        <div>
                                            <div class="font-weight-bold">{{ $item->menu->name }}</div>
                                            <div class="small text-muted">{{ $item->menu->category->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total:</th>
                            <th class="text-right">Rp {{ number_format($partnerOrderItems->sum('subtotal'), 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($order->payment)
                <h5 class="mt-4">Bukti Pembayaran</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <p><strong>Status Pembayaran:</strong> 
                                    @if($order->payment->status == 'pending')
                                        <span class="badge badge-warning">Menunggu Verifikasi</span>
                                    @elseif($order->payment->status == 'verified')
                                        <span class="badge badge-success">Terverifikasi</span>
                                    @elseif($order->payment->status == 'rejected')
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </p>
                                @if($order->payment->payment_proof)
                                    <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" alt="Bukti Pembayaran" class="img-fluid img-thumbnail">
                                @else
                                    <p class="text-muted">Tidak ada bukti pembayaran</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <!-- /.card-body -->
        <div class="card-footer">
            <a href="{{ route('partner.orders.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </div>
    <!-- /.card -->
@endsection