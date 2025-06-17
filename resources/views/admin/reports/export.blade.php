<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Bulanan - {{ $monthName }} {{ $year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        h1, h2, h3 {
            margin-top: 0;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .mb-4 {
            margin-bottom: 16px;
        }
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="text-center mb-4">
        <h1>Laporan Bulanan Portal Catering</h1>
        <h2>{{ $monthName }} {{ $year }}</h2>
    </div>

    <div class="mb-4">
        <div class="summary-box">
            <h3>Ringkasan</h3>
            <table>
                <tr>
                    <td><strong>Total Pendapatan:</strong></td>
                    <td class="text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Total Pesanan:</strong></td>
                    <td class="text-right">{{ $totalOrders }}</td>
                </tr>
                <tr>
                    <td><strong>Pesanan Pending:</strong></td>
                    <td class="text-right">{{ $pendingOrders }}</td>
                </tr>
                <tr>
                    <td><strong>Pesanan Terkirim:</strong></td>
                    <td class="text-right">{{ $deliveredOrders }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mb-4">
        <h3>Statistik Mitra Catering</h3>
        <table>
            <thead>
                <tr>
                    <th>Mitra</th>
                    <th>Jumlah Pesanan</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($partnerStats as $partner)
                    <tr>
                        <td>{{ $partner->business_name }}</td>
                        <td class="text-center">{{ $partner->total_orders }}</td>
                        <td class="text-right">Rp {{ number_format($partner->total_revenue, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <h3>Daftar Pesanan</h3>
        <table>
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
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
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

    <div class="text-center">
        <p>Laporan ini dibuat otomatis oleh sistem {{ config('app.name') }} pada {{ date('d M Y H:i') }}</p>
    </div>
</body>
</html>