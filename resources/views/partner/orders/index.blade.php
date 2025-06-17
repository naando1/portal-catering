@extends('layouts.partner')

@section('title', 'Daftar Pesanan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Pesanan</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Pesanan</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="mb-3">
                <form action="{{ route('partner.orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Cari order..." value="{{ request('search') }}">
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
                            <th>#</th>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Menu</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $index => $order)
                            <tr>
                                <td>{{ $orders->firstItem() + $index }}</td>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name }}</td>
                                <td>
                                    <span class="badge badge-info">{{ $order->menu_count }} menu</span>
                                </td>
                                <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($order->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($order->status == 'confirmed')
                                        <span class="badge badge-info">Confirmed</span>
                                    @elseif($order->status == 'processing')
                                        <span class="badge badge-primary">Processing</span>
                                    @elseif($order->status == 'delivered')
                                        <span class="badge badge-success">Delivered</span>
                                    @elseif($order->status == 'cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('partner.orders.show', $order->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada pesanan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">
            {{ $orders->links() }}
        </div>
    </div>
    <!-- /.card -->
@endsection