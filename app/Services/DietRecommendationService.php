<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\User;
use App\Models\UserBodyMetrics;
use App\Models\RecommendationFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DietRecommendationService
{
    /**
     * Menghitung kebutuhan kalori harian user
     */
    public function calculateDailyCalorieNeeds(User $user): int
    {
        $this->calculateAndStoreUserMetrics($user);
        $user->refresh();
        return $user->bodyMetrics->target_calories ?? 2000; // Default 2000 jika tidak ada
    }

    /**
     * Mendapatkan rekomendasi menu berdasarkan profil kesehatan dan preferensi diet pengguna
     * menggunakan algoritma hybrid filtering (rule-based dan content-based)
     */
    public function getRecommendedMenus($user, $limit = 10)
    {
        if (!$user->healthProfile || !$user->dietPreference) {
            return collect([]);
        }
        
        // Step 1: Rule-based filtering - menyaring menu yang tidak boleh dikonsumsi berdasarkan kondisi kesehatan
        $query = Menu::where('is_available', true)
                    ->where('is_diet_menu', true);
        
        // Filter untuk diabetes
        if ($user->healthProfile->has_diabetes) {
            $query->where('sugars', '<', 10)
                  ->where('carbohydrate_type', 'kompleks')
                  ->where('fats', '<', 10);
        }
        
        // Filter untuk hipertensi
        if ($user->healthProfile->has_hypertension) {
            $query->where('sodium', '<', 600)
                  ->whereRaw("NOT JSON_CONTAINS(taste_tags, '\"saus_asin\"')");
        }
        
        // Filter untuk penyakit jantung
        if ($user->healthProfile->has_heart_disease) {
            $query->where('fats', '<', 10)
                  ->where('sodium', '<', 600)
                  ->where('sugars', '<', 10)
                  ->whereNotIn('cooking_method', ['goreng'])
                  ->whereRaw("NOT JSON_CONTAINS(ingredient_tags, '\"jeroan\"')")
                  ->whereRaw("NOT JSON_CONTAINS(ingredient_tags, '\"kuning_telur\"')");
        }
        
        // Filter untuk kolesterol tinggi
        if ($user->healthProfile->has_cholesterol) {
            $query->where('fats', '<', 10)
                  ->whereNotIn('cooking_method', ['goreng'])
                  ->whereRaw("NOT JSON_CONTAINS(ingredient_tags, '\"jeroan\"')")
                  ->whereRaw("NOT JSON_CONTAINS(ingredient_tags, '\"kulit_ayam\"')");
        }
        
        // Filter untuk ambeien
        if ($user->healthProfile->has_hemorrhoids) {
            $query->where('fiber', '>', 3)
                  ->whereRaw("NOT JSON_CONTAINS(taste_tags, '\"pedas\"')")
                  ->whereNotIn('cooking_method', ['goreng']);
        }
        
        // Filter alergi makanan (hard filter)
        if ($user->dietPreference->food_allergies) {
            foreach ($user->dietPreference->food_allergies as $allergen) {
                $query->whereRaw("NOT JSON_CONTAINS(ingredient_tags, '\"$allergen\"')");
            }
        }
        
        // Step 2: Content-based filtering - mencocokkan preferensi pengguna dengan menu
        $eligibleMenus = $query->get();
        
        if ($eligibleMenus->isEmpty()) {
            return collect([]);
        }
        
        // Ambil preferensi pengguna
        $userPreferences = [
            'taste_preferences' => is_array($user->dietPreference->taste_preferences) 
                ? $user->dietPreference->taste_preferences 
                : json_decode($user->dietPreference->taste_preferences ?? '[]', true),
            'cooking_method_preferences' => is_array($user->dietPreference->cooking_method_preferences) 
                ? $user->dietPreference->cooking_method_preferences 
                : json_decode($user->dietPreference->cooking_method_preferences ?? '[]', true),
        ];
        
        // Ambil target kalori pengguna
        $targetCalories = $user->bodyMetrics->target_calories ?? 2000;
        
        // Hitung skor kemiripan untuk setiap menu
        $scoredMenus = $eligibleMenus->map(function ($menu) use ($userPreferences, $targetCalories, $user) {
            // 1. Skor berdasarkan kemiripan rasa (30%)
            $menuTasteTags = json_decode($menu->taste_tags ?? '[]', true);
            $tasteScore = $this->calculateOverlap($userPreferences['taste_preferences'], $menuTasteTags);
            
            // 2. Skor berdasarkan teknik masak (20%)
            $cookingMethodScore = 0;
            if (in_array($menu->cooking_method, $userPreferences['cooking_method_preferences'])) {
                $cookingMethodScore = 1;
            }
            
            // 3. Skor berdasarkan kesesuaian kalori (30%)
            // Asumsi: Menu ideal memiliki kalori sekitar 30% dari target harian
            $idealMenuCalories = $targetCalories * 0.3;
            $calorieDeviation = abs($menu->calories - $idealMenuCalories) / $idealMenuCalories;
            $calorieScore = max(0, 1 - $calorieDeviation); // 0 jika deviasi 100% atau lebih
            
            // 4. Skor berdasarkan feedback pengguna sebelumnya (20%)
            $feedbackScore = $this->calculateFeedbackScore($menu->id, $user->id);
            
            // Hitung skor total dengan pembobotan
            $totalScore = ($tasteScore * 0.3) + 
                          ($cookingMethodScore * 0.2) + 
                          ($calorieScore * 0.3) + 
                          ($feedbackScore * 0.2);
            
            $menu->similarity_score = $totalScore;
            return $menu;
        });
        
        // Urutkan berdasarkan skor kemiripan (dari tinggi ke rendah)
        $recommendedMenus = $scoredMenus->sortByDesc('similarity_score')->take($limit);
        
        return $recommendedMenus;
    }

    /**
     * Menghitung skor berdasarkan feedback pengguna sebelumnya
     */
    private function calculateFeedbackScore($menuId, $userId)
    {
        // Cek apakah pengguna pernah memberikan feedback untuk menu ini
        $feedback = RecommendationFeedback::where('menu_id', $menuId)
                                         ->where('user_id', $userId)
                                         ->first();
        
        if ($feedback) {
            return $feedback->is_relevant ? 1.0 : 0.0;
        }
        
        // Jika belum ada feedback, berikan nilai netral
        return 0.5;
    }

    /**
     * Menghitung overlap/irisan antara dua array
     */
    private function calculateOverlap($array1, $array2)
    {
        // Pastikan kedua parameter adalah array
        if (!is_array($array1)) {
            $array1 = [];
        }
        
        if (!is_array($array2)) {
            $array2 = [];
        }
        
        if (empty($array1) || empty($array2)) {
            return 0;
        }
        
        $intersection = array_intersect($array1, $array2);
        $union = array_unique(array_merge($array1, $array2));
        
        return count($intersection) / count($union);
    }

    /**
     * Hitung dan simpan metrics user (BMI, BMR, TDEE, Target Calories)
     */
    public function calculateAndStoreUserMetrics(User $user): void
    {
        if (!$user->height || !$user->weight || !$user->birthdate || !$user->gender) {
            return;
        }

        $bodyMetrics = $user->bodyMetrics ?? new UserBodyMetrics(['user_id' => $user->id]);
        
        // Hitung BMI
        $heightInMeters = $user->height / 100;
        $bmi = round($user->weight / ($heightInMeters * $heightInMeters), 1);
        
        // Hitung BMR menggunakan rumus Mifflin-St Jeor
        $age = $user->getAge();
        $bmr = 0;
        
        if ($user->gender == 'male' || $user->gender == 'pria') {
            $bmr = (10 * $user->weight) + (6.25 * $user->height) - (5 * $age) + 5;
        } else {
            $bmr = (10 * $user->weight) + (6.25 * $user->height) - (5 * $age) - 161;
        }
        $bmr = round($bmr);
        
        // Hitung TDEE berdasarkan level aktivitas
        $activityFactors = [
            'sedentari' => 1.2,
            'ringan' => 1.375,
            'sedang' => 1.55,
            'berat' => 1.725,
            'sangat_berat' => 1.9,
        ];
        
        $activityLevel = $user->activity_level ?? 'sedentari';
        $factor = $activityFactors[$activityLevel] ?? 1.2;
        $tdee = round($bmr * $factor);
        
        // Hitung target kalori berdasarkan tujuan diet
        $dietGoal = $user->dietPreference->diet_goal ?? 'jaga_bb';
        $percentage = $user->dietPreference->deficit_surplus_percentage ?? 15;
        
        $targetCalories = $tdee;
        switch ($dietGoal) {
            case 'turun_bb':
                $targetCalories = round($tdee * (1 - ($percentage / 100)));
                break;
            case 'naik_bb':
                $targetCalories = round($tdee * (1 + ($percentage / 100)));
                break;
            case 'jaga_bb':
            default:
                $targetCalories = $tdee;
                break;
        }

        // Simpan ke database
        $bodyMetrics->fill([
            'bmi' => $bmi,
            'bmr' => $bmr,
            'tdee' => $tdee,
            'target_calories' => $targetCalories,
        ]);
        
        $bodyMetrics->save();
        
        // Update relasi jika belum ada
        if (!$user->relationLoaded('bodyMetrics')) {
            $user->load('bodyMetrics');
        }
    }

    /**
     * Get menu recommendations untuk halaman diet tanpa login
     */
    public function getGeneralDietMenus(int $limit = 20): Collection
    {
        return Menu::where('is_available', true)
            ->where('is_diet_menu', true)
            ->where('calories', '<=', 500) // General low-moderate calorie
            ->orderBy('fiber', 'desc') // Prioritas serat tinggi
            ->orderBy('proteins', 'desc') // Prioritas protein tinggi
            ->take($limit)
            ->get();
    }

    /**
     * Mendapatkan rekomendasi menu berdasarkan feedback positif pengguna lain
     * dengan kondisi kesehatan serupa
     */
    public function getRecommendationsByFeedback($user, $limit = 5)
    {
        if (!$user->healthProfile) {
            return collect([]);
        }
        
        // Dapatkan pengguna dengan kondisi kesehatan serupa
        $similarUserQuery = User::where('id', '!=', $user->id)
            ->whereHas('healthProfile', function ($query) use ($user) {
                if ($user->healthProfile->has_diabetes) {
                    $query->where('has_diabetes', true);
                }
                if ($user->healthProfile->has_hypertension) {
                    $query->where('has_hypertension', true);
                }
                if ($user->healthProfile->has_heart_disease) {
                    $query->where('has_heart_disease', true);
                }
                if ($user->healthProfile->has_cholesterol) {
                    $query->where('has_cholesterol', true);
                }
                if ($user->healthProfile->has_hemorrhoids) {
                    $query->where('has_hemorrhoids', true);
                }
            });
        
        $similarUserIds = $similarUserQuery->pluck('id')->toArray();
        
        if (empty($similarUserIds)) {
            return collect([]);
        }
        
        // Dapatkan menu yang mendapat feedback positif dari pengguna serupa
        $menuIds = RecommendationFeedback::whereIn('user_id', $similarUserIds)
            ->where('is_relevant', true)
            ->groupBy('menu_id')
            ->selectRaw('menu_id, COUNT(*) as count')
            ->orderByDesc('count')
            ->limit($limit * 2) // Ambil lebih untuk difilter
            ->pluck('menu_id');
        
        if ($menuIds->isEmpty()) {
            return collect([]);
        }
        
        // Dapatkan menu dan filter sesuai kondisi kesehatan
        $menus = Menu::whereIn('id', $menuIds)->where('is_available', true)->get();
        
        // Filter menu sesuai kondisi kesehatan
        $filteredMenus = $menus->filter(function ($menu) use ($user) {
            // Filter diabetes
            if ($user->healthProfile->has_diabetes && 
                ($menu->sugars > 10 || $menu->carbohydrate_type == 'olahan' || $menu->fats > 10)) {
                return false;
            }
            
            // Filter hipertensi
            if ($user->healthProfile->has_hypertension && $menu->sodium > 600) {
                return false;
            }
            
            // Filter jantung
            if ($user->healthProfile->has_heart_disease && 
                ($menu->fats > 10 || $menu->sodium > 600 || $menu->sugars > 10)) {
                return false;
            }
            
            // Filter kolesterol
            if ($user->healthProfile->has_cholesterol && 
                ($menu->fats > 10 || $menu->cooking_method == 'goreng')) {
                return false;
            }
            
            // Filter ambeien
            if ($user->healthProfile->has_hemorrhoids && 
                ($menu->fiber < 3 || $menu->cooking_method == 'goreng')) {
                return false;
            }
            
            return true;
        });
        
        return $filteredMenus->take($limit);
    }
}