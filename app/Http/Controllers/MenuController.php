<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use App\Models\CateringPartner;
use App\Models\Review;
use App\Models\DietTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DietRecommendationService;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with(['cateringPartner', 'category'])
            ->where('is_available', true);

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by partner
        if ($request->has('partner') && $request->partner) {
            $query->where('catering_partner_id', $request->partner);
        }

        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menus = $query->paginate(12);
        $categories = Category::all();
        $partners = CateringPartner::where('is_active', true)->get();

        return view('menus.index', compact('menus', 'categories', 'partners'));
    }

    public function show(Menu $menu)
    {
        $menu->load(['cateringPartner', 'category', 'reviews.user']);
        
        $relatedMenus = Menu::where('category_id', $menu->category_id)
            ->where('id', '!=', $menu->id)
            ->where('is_available', true)
            ->take(4)
            ->get();

        $userReview = null;
        if (auth()->check()) {
            $userReview = Review::where('menu_id', $menu->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        return view('menus.show', compact('menu', 'relatedMenus', 'userReview'));
    }
    
    /**
     * Menampilkan halaman menu diet
     */
    public function dietMenu(Request $request)
    {
        $query = Menu::with(['cateringPartner', 'category', 'dietTags'])
                     ->where('is_available', true)
                     ->where('is_diet_menu', true);
        
        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Filter berdasarkan diet tag
        if ($request->has('diet_tag') && $request->diet_tag) {
            $query->whereHas('dietTags', function ($q) use ($request) {
                $q->where('diet_tags.id', $request->diet_tag);
            });
        }
        
        // Filter berdasarkan kondisi kesehatan
        if ($request->has('condition') && $request->condition) {
            switch ($request->condition) {
                case 'diabetes':
                    $query->where('sugars', '<=', 5);
                    break;
                case 'hypertension':
                    $query->where('sodium', '<=', 400);
                    break;
                case 'heart':
                    $query->where('fats', '<=', 10)
                          ->where('sodium', '<=', 400);
                    break;
            }
        }
        
        // Filter berdasarkan kalori
        if ($request->has('calorie') && $request->calorie) {
            switch ($request->calorie) {
                case 'low':
                    $query->where('calories', '<=', 400);
                    break;
                case 'medium':
                    $query->where('calories', '>', 400)
                          ->where('calories', '<=', 600);
                    break;
                case 'high':
                    $query->where('calories', '>', 600);
                    break;
            }
        }
        
        // Pencarian berdasarkan nama menu
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $menus = $query->paginate(12);
        $categories = Category::all();
        $dietTags = DietTag::all();
        
        // Jika user sudah login, cek apakah profil kesehatan lengkap
        $user = Auth::user();
        $hasCompleteProfile = $user && $user->hasCompleteHealthProfile();
        
        // Jika user memiliki profil kesehatan lengkap, dapatkan rekomendasi
        $personalRecommendations = null;
        if ($hasCompleteProfile) {
            $dietService = new DietRecommendationService();
            $personalRecommendations = $dietService->getRecommendedMenus($user, 6);
        }
        
        return view('menus.diet', compact(
            'menus',
            'categories',
            'dietTags',
            'hasCompleteProfile',
            'personalRecommendations'
        ));
    }
    
    /**
     * Menampilkan detail menu diet
     */
    public function showDietMenu($id)
    {
        $menu = Menu::with(['category', 'cateringPartner', 'dietTags', 'reviews.user'])
                    ->where('is_diet_menu', true)
                    ->findOrFail($id);
        
        // Menghitung rata-rata rating
        $averageRating = $menu->reviews->avg('rating') ?? 0;
        
        // Dapatkan user yang sedang login
        $user = Auth::user();
        
        // Cek kesesuaian dengan kondisi kesehatan jika user login dan memiliki profil kesehatan
        $suitability = [];
        $calorieTarget = null;
        $caloriePercentage = null;
        
        if ($user && $user->healthProfile) {
            $suitability = [
                'diabetes' => $user->healthProfile->has_diabetes ? $menu->isSuitableForDiabetic() : true,
                'hypertension' => $user->healthProfile->has_hypertension ? $menu->isSuitableForHypertension() : true,
                'heart_disease' => $user->healthProfile->has_heart_disease ? $menu->isSuitableForHeartDisease() : true,
            ];
            
            // Jika user punya preferensi diet, hitung persentase kalori dari target harian
            $calorieTarget = $user->dietPreference->daily_calorie_target ?? null;
            $caloriePercentage = $calorieTarget && $menu->calories ? round(($menu->calories / $calorieTarget) * 100, 1) : null;
        }
        
        // Dapatkan menu-menu terkait dengan kategori yang sama
        $relatedMenus = Menu::where('id', '!=', $menu->id)
                           ->where('is_diet_menu', true)
                           ->where('category_id', $menu->category_id)
                           ->limit(4)
                           ->get();
        
        // Cek apakah user sudah memberikan review
        $userReview = null;
        if (auth()->check()) {
            $userReview = Review::where('menu_id', $menu->id)
                               ->where('user_id', auth()->id())
                               ->first();
        }
        
        return view('menus.diet-detail', compact(
            'menu',
            'averageRating',
            'suitability',
            'calorieTarget',
            'caloriePercentage',
            'relatedMenus',
            'userReview'
        ));
    }
}