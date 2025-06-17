<?php
// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\CateringPartner;
use App\Models\User;
use App\Models\Order;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Count of partners, customers, categories, menus
        $totalPartners = CateringPartner::count();
        $customerRole = Role::where('name', 'customer')->first();
        $totalCustomers = User::where('role_id', $customerRole->id)->count();
        $totalCategories = Category::count();
        $totalMenus = Menu::count();
        
        // Pending orders
        $pendingOrders = Order::where('status', 'pending')->count();
        
        // Recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Monthly order statistics
        $monthlyStats = Order::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->where('status', '!=', 'cancelled')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
        
        // Category distribution
        $categoryDistribution = Menu::select('categories.name', DB::raw('count(*) as total'))
            ->join('categories', 'menus.category_id', '=', 'categories.id')
            ->groupBy('categories.name')
            ->orderBy('total', 'desc')
            ->get();
        
        return view('admin.dashboard', compact(
            'totalPartners',
            'totalCustomers',
            'totalCategories',
            'totalMenus',
            'pendingOrders',
            'recentOrders',
            'monthlyStats',
            'categoryDistribution'
        ));
    }
}