@extends('layouts.partner')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalCategories }}</h3>
                    <p>Kategori Menu</p>
                </div>
                <div class="icon">
                    <i class="fas fa-th-list"></i>
                </div>
                <a href="{{ route('partner.menus.create') }}" class="small-box-footer">Tambah Menu <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalMenus }}</h3>
                    <p>Menu Catering</p>
                </div>
                <div class="icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <a href="{{ route('partner.menus.index') }}" class="small-box-footer">Lihat Menu <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $pendingOrders }}</h3>
                    <p>Pending Orders</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="{{ route('partner.orders.index', ['status' => 'pending']) }}" class="small-box-footer">Lihat Pesanan <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->

    <!-- Main row -->
    <div class="row">
        <!-- Left col -->
        <section class="col-lg-7 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
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
            <!-- /.card -->

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
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td><a href="{{ route('partner.orders.show', $order->id) }}">{{ $order->order_number }}</a></td>
                                        <td>{{ $order->customer_name }}</td>
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
                                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada pesanan</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- /.table-responsive -->
                </div>
                <!-- /.card-body -->
                <div class="card-footer text-center">
                    <a href="{{ route('partner.orders.index') }}" class="uppercase">Lihat Semua Pesanan</a>
                </div>
                <!-- /.card-footer -->
            </div>
            <!-- /.card -->
        </section>
        <!-- /.Left col -->

        <!-- right col (We are only adding the ID to make the widgets sortable)-->
        <section class="col-lg-5 connectedSortable">
            <!-- Partner Info -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                        @if(auth()->user()->cateringPartner->logo)
                            <img class="profile-user-img img-fluid img-circle" src="{{ asset('storage/' . auth()->user()->cateringPartner->logo) }}" alt="Logo">
                        @else
                            <img class="profile-user-img img-fluid img-circle" src="{{ asset('vendor/adminlte/dist/img/user4-128x128.jpg') }}" alt="Logo">
                        @endif
                    </div>

                    <h3 class="profile-username text-center">{{ auth()->user()->cateringPartner->business_name }}</h3>

                    <p class="text-muted text-center">Mitra Catering</p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Total Menu</b> <a class="float-right">{{ $totalMenus }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Total Pesanan</b> <a class="float-right">{{ $pendingOrders }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>Status</b> <a class="float-right">{{ auth()->user()->cateringPartner->is_active ? 'Aktif' : 'Tidak Aktif' }}</a>
                        </li>
                    </ul>

                    <a href="{{ route('partner.profile.edit') }}" class="btn btn-primary btn-block"><b>Edit Profil</b></a>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- Quick Actions Box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-bolt mr-1"></i>
                        Aksi Cepat
                    </h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body p-0">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="{{ route('partner.menus.create') }}" class="nav-link">
                                <i class="fas fa-plus mr-2"></i> Tambah Menu Baru
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('partner.orders.index', ['status' => 'pending']) }}" class="nav-link">
                                <i class="fas fa-clock mr-2"></i> Cek Pesanan Pending
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('partner.reports.monthly') }}" class="nav-link">
                                <i class="fas fa-file-alt mr-2"></i> Lihat Laporan Bulanan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('menus.index', ['partner' => auth()->user()->cateringPartner->id]) }}" class="nav-link" target="_blank">
                                <i class="fas fa-eye mr-2"></i> Lihat Menu di Website
                            </a>
                        </li>
                    </ul>
                </div>
                <!-- /.card-body -->
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
        });
    </script>
@endsection