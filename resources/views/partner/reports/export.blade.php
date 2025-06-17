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
        <h1>Laporan Bulanan {{ $partner->business_name }}</h1>
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
                    <td><strong>Total Item Terjual:</strong></td>
                    <td class="text-right">{{ $orderItems->sum('quantity') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mb-4">
        <h3>Statistik Menu</h3>
        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Qty Terjual</th>
                    <th>Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($menuStats as $menu => $stats)
                    <tr>
                        <td>{{ $menu }}</td>
                        <td class="text-center">{{ $stats['total_quantity'] }}</td>
                        <td class="text-right">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mb-4">
        <h3>Daftar Pesanan</h3>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Menu</th>
                    <th>Qty</th>
                    <th>Harga</th>
                    <th>Subtotal</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderItems as $item)
                    <tr>
                        <td>{{ $item->order_number }}</td>
                        <td>{{ $item->menu_name }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($item->status) }}</td>
                        <td>{{ date('d M Y', strtotime($item->created_at)) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="text-center">
        <p>Laporan ini dibuat otomatis oleh sistem {{ config('app.name', 'Portal Catering') }} pada {{ date('d M Y H:i') }}</p>
    </div>
</body>
</html>