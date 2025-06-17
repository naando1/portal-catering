@extends('layouts.admin')

@section('title', 'Laporan Bulanan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan Bulanan</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Bulanan</h3>
            <div class="card-tools">
                <a href="{{ route('admin.reports.monthly.export', ['year' => $year, 'month' => $month]) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-download"></i> Export PDF
                </a>
            </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="mb-4">
                <form action="{{ route('admin.reports.monthly') }}" method="GET" class="row g-2">
                    <div class="col-md-3">
                        <select name="year" class="form-control">
                            @foreach($years as $y)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="month" class="form-control">
                            @foreach($months as $m => $monthName)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $monthName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">Lihat Laporan</button>
                    </div>
                </form>
            </div>

            <!-- Summary Stats -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                            <p>Total Pendapatan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $totalOrders }}</h3>
                            <p>Total Pesanan</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ $pendingOrders }}</h3>
                            <p>Pesanan Pending</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $deliveredOrders }}</h3>
                            <p>Pesanan Terkirim</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-truck"></i>
                        </div>
                    </div>
                </div>
                <!-- ./col -->
            </div>
            <!-- /.row -->

            <!-- Partner Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Statistik Mitra Catering</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Mitra</th>
                                    <th class="text-center">Jumlah Pesanan</th>
                                    <th class="text-end">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($partnerStats as $partner)
                                    <tr>
                                        <td>{{ $partner->business_name }}</td>
                                        <td class="text-center">{{ $partner->total_orders }}</td>
                                        <td class="text-end">Rp {{ number_format($partner->total_revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Category Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Statistik Kategori</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th class="text-center">Jumlah Item</th>
                                    <th class="text-end">Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categoryStats as $category)
                                    <tr>
                                        <td>{{ $category->name }}</td>
                                        <td class="text-center">{{ $category->total_items }}</td>
                                        <td class="text-end">Rp {{ number_format($category->total_revenue, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pesanan ({{ $month }} {{ $year }})</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $order->user->name }}</td>
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
                                        <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                        <td>{{ $order->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada pesanan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@endsection

@section('scripts')
<script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    // Charts can be added here if needed
</script>
@endsection