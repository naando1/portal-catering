<?php
// app/Http/Controllers/Partner/DashboardController.php
namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $partner = auth()->user()->cateringPartner;

        // Get statistics for dashboard
        $totalCategories = Category::count();
        
        $totalMenus = Menu::where('catering_partner_id', $partner->id)->count();
        
        $partnerMenus = Menu::where('catering_partner_id', $partner->id)->pluck('id');
        
        $pendingOrders = OrderItem::whereIn('menu_id', $partnerMenus)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'pending')
            ->select('orders.id')
            ->distinct()
            ->count();
        
        $recentOrders = OrderItem::whereIn('menu_id', $partnerMenus)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select('orders.*', 'users.name as customer_name')
            ->orderBy('orders.created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get monthly order statistics
        $monthlyStats = OrderItem::whereIn('menu_id', $partnerMenus)
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select(
                DB::raw('YEAR(orders.created_at) as year'),
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('COUNT(DISTINCT orders.id) as total_orders'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->where('orders.created_at', '>=', now()->subMonths(6))
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return view('partner.dashboard', compact(
            'totalCategories',
            'totalMenus',
            'pendingOrders',
            'recentOrders',
            'monthlyStats'
        ));
    }
}