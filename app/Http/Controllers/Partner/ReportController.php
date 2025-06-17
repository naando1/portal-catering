<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDF;

class ReportController extends Controller
{
    public function monthly(Request $request)
    {
        $partner = auth()->user()->cateringPartner;
        $partnerMenus = Menu::where('catering_partner_id', $partner->id)->pluck('id');
        
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        
        $orderItems = OrderItem::whereIn('menu_id', $partnerMenus)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select(
                'orders.id as order_id',
                'orders.order_number',
                'orders.created_at',
                'orders.status',
                'menus.name as menu_name',
                'order_items.quantity',
                'order_items.price',
                'order_items.subtotal'
            )
            ->whereYear('orders.created_at', $year)
            ->whereMonth('orders.created_at', $month)
            ->where('orders.status', '!=', 'cancelled')
            ->orderBy('orders.created_at')
            ->get();
        
        $totalRevenue = $orderItems->sum('subtotal');
        $totalOrders = $orderItems->pluck('order_id')->unique()->count();
        $menuStats = $orderItems->groupBy('menu_name')->map(function ($items) {
            return [
                'total_quantity' => $items->sum('quantity'),
                'total_revenue' => $items->sum('subtotal')
            ];
        });
        
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
        
        return view('partner.reports.monthly', compact(
            'orderItems',
            'totalRevenue',
            'totalOrders',
            'menuStats',
            'year',
            'month',
            'years',
            'months'
        ));
    }

    public function export(Request $request)
    {
        $partner = auth()->user()->cateringPartner;
        $partnerMenus = Menu::where('catering_partner_id', $partner->id)->pluck('id');
        
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');
        
        $orderItems = OrderItem::whereIn('menu_id', $partnerMenus)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select(
                'orders.id as order_id',
                'orders.order_number',
                'orders.created_at',
                'orders.status',
                'menus.name as menu_name',
                'order_items.quantity',
                'order_items.price',
                'order_items.subtotal'
            )
            ->whereYear('orders.created_at', $year)
            ->whereMonth('orders.created_at', $month)
            ->where('orders.status', '!=', 'cancelled')
            ->orderBy('orders.created_at')
            ->get();
        
        $totalRevenue = $orderItems->sum('subtotal');
        $totalOrders = $orderItems->pluck('order_id')->unique()->count();
        $menuStats = $orderItems->groupBy('menu_name')->map(function ($items) {
            return [
                'total_quantity' => $items->sum('quantity'),
                'total_revenue' => $items->sum('subtotal')
            ];
        });
        
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
        
        $pdf = PDF::loadView('partner.reports.export', compact(
            'orderItems',
            'totalRevenue',
            'totalOrders',
            'menuStats',
            'year',
            'month',
            'monthName',
            'partner'
        ));
        
        return $pdf->download("laporan-{$partner->business_name}-{$monthName}-{$year}.pdf");
    }
}