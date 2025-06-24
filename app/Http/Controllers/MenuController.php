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
use App\Models\RecommendationFeedback;

class MenuController extends Controller
{
    protected $recommendationService;

    public function __construct(DietRecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

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
     * Menampilkan halaman rekomendasi diet untuk user yang login
     */
    public function diet()
    {
        $user = auth()->user();
        
        // Pastikan user memiliki profil kesehatan dan preferensi diet
        if (!$user->hasCompleteHealthProfile()) {
            return redirect()->route('customer.profile.edit', ['tab' => 'health'])
                ->with('warning', 'Silakan lengkapi profil kesehatan Anda terlebih dahulu untuk mendapatkan rekomendasi diet.');
        }
        
        // Hitung atau update metrics user jika diperlukan
        $dietService = app(DietRecommendationService::class);
        $dietService->calculateAndStoreUserMetrics($user);
        
        // Dapatkan rekomendasi menu berdasarkan profil user
        $recommendedMenus = $dietService->getRecommendedMenus($user, 8);
        
        // Dapatkan rekomendasi berdasarkan feedback pengguna lain (opsional)
        $feedbackRecommendations = $dietService->getRecommendationsByFeedback($user, 4);
        
        // Dapatkan metrics user untuk ditampilkan
        $userMetrics = [
            'bmi' => $user->bodyMetrics->bmi ?? 0,
            'bmi_category' => $user->getBmiCategory() ?? 'Tidak diketahui',
            'bmr' => $user->bodyMetrics->bmr ?? 0,
            'tdee' => $user->bodyMetrics->tdee ?? 0,
            'target_calories' => $user->bodyMetrics->target_calories ?? 0,
        ];
        
        return view('menus.diet', compact('recommendedMenus', 'feedbackRecommendations', 'userMetrics'));
    }
    
    /**
     * Menyimpan feedback pengguna terhadap rekomendasi menu
     */
    public function submitFeedback(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        
        $validated = $request->validate([
            'is_relevant' => 'required|boolean',
            'comment' => 'nullable|string|max:500',
        ]);
        
        // Simpan atau update feedback
        RecommendationFeedback::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'menu_id' => $menu->id,
            ],
            [
                'is_relevant' => $validated['is_relevant'],
                'comment' => $validated['comment'],
            ]
        );
        
        return redirect()->back()->with('success', 'Terima kasih atas feedback Anda! Feedback ini akan membantu kami meningkatkan rekomendasi menu untuk Anda.');
    }
}