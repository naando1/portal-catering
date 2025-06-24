<?php
// app/Http/Controllers/Api/RecommendationController.php - NEW

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DietRecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    protected $dietService;

    public function __construct(DietRecommendationService $dietService)
    {
        $this->dietService = $dietService;
    }

    /**
     * Get personal recommendations for current user
     */
    public function personal(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->hasCompleteHealthProfile()) {
            return response()->json([
                'success' => false,
                'message' => 'Profil kesehatan belum lengkap'
            ], 400);
        }

        $limit = $request->get('limit', 10);
        $recommendations = $this->dietService->getRecommendedMenus($user, $limit);

        return response()->json([
            'success' => true,
            'data' => $recommendations->map(function($menu) {
                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'description' => $menu->description,
                    'price' => $menu->price,
                    'calories' => $menu->calories,
                    'image' => asset('storage/' . $menu->image),
                    'partner' => $menu->cateringPartner->business_name,
                    'score' => $menu->score ?? 0,
                    'category' => $menu->category->name,
                ];
            })
        ]);
    }
}