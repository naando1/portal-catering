<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CateringPartner;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        
        // Orders for the selected month
        $orders = Order::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at')
            ->get();
        
        // Orders summary
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $deliveredOrders = $orders->where('status', 'delivered')->count();
        
        // Partner statistics
        $partnerStats = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('catering_partners', 'menus.catering_partner_id', '=', 'catering_partners.id')
            ->select(
                'catering_partners.id',
                'catering_partners.business_name',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->whereYear('orders.created_at', $year)
            ->whereMonth('orders.created_at', $month)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('catering_partners.id', 'catering_partners.business_name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        // Category statistics
        $categoryStats = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                'categories.id',
                'categories.name',
                DB::raw('COUNT(order_items.id) as total_items'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->whereYear('orders.created_at', $year)
            ->whereMonth('orders.created_at', $month)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        // Prepare data for years and months dropdowns
        $years = range(date('Y') - 2, date('Y'));
        $months = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];
        
        return view('admin.reports.monthly', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'partnerStats',
            'categoryStats',
            'year',
            'month',
            'years',
            'months'
        ));
    }

    public function export(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        
        // Orders for the selected month
        $orders = Order::with(['user', 'orderItems.menu.cateringPartner'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('status', '!=', 'cancelled')
            ->orderBy('created_at')
            ->get();
        
        // Orders summary
        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $pendingOrders = $orders->where('status', 'pending')->count();
        $deliveredOrders = $orders->where('status', 'delivered')->count();
        
        // Partner statistics
        $partnerStats = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('catering_partners', 'menus.catering_partner_id', '=', 'catering_partners.id')
            ->select(
                'catering_partners.id',
                'catering_partners.business_name',
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->whereYear('orders.created_at', $year)
            ->whereMonth('orders.created_at', $month)
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('catering_partners.id', 'catering_partners.business_name')
            ->orderBy('total_revenue', 'desc')
            ->get();
        
        $monthName = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ][$month];
        
        $pdf = PDF::loadView('admin.reports.export', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'partnerStats',
            'year',
            'month',
            'monthName'
        ));
        
        return $pdf->download("laporan-portal-catering-{$monthName}-{$year}.pdf");
    }
}
