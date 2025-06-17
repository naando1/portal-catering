@extends('layouts.partner')

@section('title', 'Laporan Bulanan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Laporan Bulanan</li>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Laporan Bulanan</h3>
            @if(isset($month) && isset($year))
                <div class="card-tools">
                    <a href="{{ route('partner.reports.monthly.export', ['month' => $month, 'year' => $year]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-download"></i> Export PDF
                    </a>
                </div>
            @endif
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <div class="mb-4">
                <form action="{{ route('partner.reports.monthly') }}" method="GET" class="row g-2">
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

            @if(isset($orders) && count($orders) > 0)
                <!-- Summary Stats -->
                <div class="row">
                    <div class="col-lg-4 col-6">
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
                    <div class="col-lg-4 col-6">
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
                    <div class="col-lg-4 col-6">
                        <!-- small box -->
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $totalMenuItems }}</h3>
                                <p>Total Item Terjual</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-utensils"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->

                <!-- Menu Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3 class="card-title">Statistik Menu</h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Qty Terjual</th>
                                        <th class="text-right">Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($menuStats as $menu)
                                        <tr>
                                            <td>{{ $menu->name }}</td>
                                            <td>{{ $menu->category_name }}</td>
                                            <td class="text-center">{{ $menu->total_quantity }}</td>
                                            <td class="text-right">Rp {{ number_format($menu->total_revenue, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Orders List -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Pesanan ({{ $months[$month] }} {{ $year }})</h3>
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
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('partner.orders.show', $order->id) }}">
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
                                            <td>Rp {{ number_format($order->partner_amount, 0, ',', '.') }}</td>
                                            <td>{{ $order->created_at->format('d M Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    @if(isset($month) && isset($year))
                        <p>Tidak ada data pesanan untuk periode {{ $months[$month] }} {{ $year }}.</p>
                    @else
                        <p>Silakan pilih periode laporan untuk melihat data.</p>
                    @endif
                </div>
            @endif
        </div>
        <!-- /.card-body -->
    </div>
    <!-- /.card -->
@endsection

@section('scripts')
<script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script>
<script>
    // You can add charts here if needed
</script>
@endsection