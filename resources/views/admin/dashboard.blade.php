@extends('layouts.admin')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalPartners }}</h3>
                    <p>Mitra Catering</p>
                </div>
                <div class="icon">
                    <i class="fas fa-store"></i>
                </div>
                <a href="{{ route('admin.partners.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalCustomers }}</h3>
                    <p>Customer</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalMenus }}</h3>
                    <p>Menu Catering</p>
                </div>
                <div class="icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <a href="{{ route('admin.menus.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $pendingOrders }}</h3>
                    <p>Pending Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
            {{-- <!-- Custom tabs (Charts with tabs)-->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Statistik Bulanan
                    </h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="chart">
                        <canvas id="monthlyChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div><!-- /.card-body -->
            </div>
            <!-- /.card --> --}}

            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        Pesanan Terbaru
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
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
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td><a href="{{ route('admin.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('admin.orders.index') }}" class="uppercase">Lihat Semua Pesanan</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->
        </section>
        <!-- /.Left col -->

        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable">
            <!-- Menu Category Distribution -->
            {{-- <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Distribusi Kategori Menu
                    </h3>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <canvas id="categoryPieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div><!-- /.card-body -->
            </div>
            <!-- /.card --> --}}

            <!-- Categories List -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-th-list mr-1"></i>
                        Kategori Menu
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @foreach($categoryDistribution as $category)
                            <li class="item">
                                <div class="product-info">
                                    <a href="{{ route('admin.menus.index', ['category' => $category->id]) }}" class="product-title">
                                        {{ $category->name }}
                                        <span class="badge badge-info float-right">{{ $category->total }} Menu</span>
                                    </a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('admin.categories.index') }}" class="uppercase">Lihat Semua Kategori</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->
        </section>
        <!-- /.right col -->
    </div>
    <!-- /.row -->
@endsection

@section('scripts')
    <!-- ChartJS -->
    <script src="{{ asset('vendor/adminlte/plugins/chart.js/Chart.min.js') }}"></script>
    <script>
        $(function () {
            // Monthly statistics chart
            var monthlyData = @json($monthlyStats);
            var months = monthlyData.map(function(item) {
                var monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return monthNames[item.month - 1] + ' ' + item.year;
            });
            var orders = monthlyData.map(function(item) {
                return item.total_orders;
            });
            var revenue = monthlyData.map(function(item) {
                return item.total_revenue / 1000; // Convert to thousands
            });
            
            var ctx = document.getElementById('monthlyChart').getContext('2d');
            var chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [
                        {
                            label: 'Jumlah Pesanan',
                            data: orders,
                            borderColor: '#007bff',
                            backgroundColor: 'transparent',
                            pointBorderColor: '#007bff',
                            pointBackgroundColor: '#007bff',
                            fill: false
                        },
                        {
                            label: 'Pendapatan (ribuan Rp)',
                            data: revenue,
                            borderColor: '#28a745',
                            backgroundColor: 'transparent',
                            pointBorderColor: '#28a745',
                            pointBackgroundColor: '#28a745',
                            fill: false
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        display: true
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                display: true
                            },
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });

            // Category distribution pie chart
            var categoryData = @json($categoryDistribution);
            var categoryNames = categoryData.map(function(item) {
                return item.name;
            });
            var categoryTotals = categoryData.map(function(item) {
                return item.total;
            });
            
            var backgroundColors = [
                '#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de',
                '#6c757d', '#28a745', '#17a2b8', '#dc3545', '#ffc107', '#343a40'
            ];
            
            var ctx2 = document.getElementById('categoryPieChart').getContext('2d');
            var pieChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: categoryNames,
                    datasets: [{
                        data: categoryTotals,
                        backgroundColor: backgroundColors.slice(0, categoryNames.length)
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    legend: {
                        position: 'right'
                    }
                }
            });
        });
    </script>
@endsection