<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\DietRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\RecommendationFeedback;

class DashboardController extends Controller
{
    protected $dietService;

    public function __construct(DietRecommendationService $dietService)
    {
        $this->dietService = $dietService;
    }

    public function index()
    {
        $user = auth()->user();
        
        // Dapatkan metrik kesehatan
        $healthMetrics = [
            'bmi' => $user->bodyMetrics->bmi ?? 0,
            'bmi_category' => $user->getBmiCategory() ?? 'Tidak diketahui',
            'target_calories' => $user->bodyMetrics->target_calories ?? 0,
        ];
        
        // Dapatkan kondisi kesehatan
        $healthConditions = $user->healthProfile ? $user->healthProfile->getActiveConditions() : [];
        
        // Dapatkan statistik rekomendasi
        $feedbackStats = [
            'total' => RecommendationFeedback::where('user_id', $user->id)->count(),
            'relevant' => RecommendationFeedback::where('user_id', $user->id)->where('is_relevant', true)->count(),
        ];
        
        // Dapatkan pesanan terakhir
        $recentOrders = $user->orders()->with('orderItems.menu')->latest()->take(3)->get();
        
        return view('customer.dashboard', compact('healthMetrics', 'healthConditions', 'feedbackStats', 'recentOrders'));
    }
}
