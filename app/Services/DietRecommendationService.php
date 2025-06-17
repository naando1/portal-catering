<?php
// app/Services/DietRecommendationService.php
namespace App\Services;

use App\Models\User;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Builder;

class DietRecommendationService
{
    /**
     * Mendapatkan rekomendasi menu makanan berdasarkan profil kesehatan
     *
     * @param User $user    
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecommendedMenus(User $user, int $limit = 12)
    {
        // Jika profil kesehatan belum lengkap, kembalikan menu diet umum
        if (!$user->hasCompleteHealthProfile()) {
            return $this->getGeneralDietMenus($limit);
        }

        // Mulai query dasar
        $query = Menu::where('is_available', true)
                      ->where('is_diet_menu', true);

        // Filter berdasarkan profil kesehatan
        $healthProfile = $user->healthProfile;
        if ($healthProfile) {
            // Filter untuk diabetes (rendah gula)
            if ($healthProfile->has_diabetes) {
                $query->where('sugars', '<=', 5);
            }

            // Filter untuk hipertensi (rendah sodium)
            if ($healthProfile->has_hypertension) {
                $query->where('sodium', '<=', 400);
            }

            // Filter untuk penyakit jantung (rendah lemak dan sodium)
            if ($healthProfile->has_heart_disease) {
                $query->where('fats', '<=', 10)
                      ->where('sodium', '<=', 400);
            }

            // Filter untuk kolesterol tinggi (rendah lemak)
            if ($healthProfile->has_cholesterol) {
                $query->where('fats', '<=', 8);
            }

            // Filter untuk alergi makanan
            if ($healthProfile->has_food_allergies && $healthProfile->food_allergies_detail) {
                $allergiesText = strtolower($healthProfile->food_allergies_detail);
                
                // Filter berdasarkan alergi umum
                if (strpos($allergiesText, 'kacang') !== false || strpos($allergiesText, 'nut') !== false) {
                    $query->whereDoesntHave('dietTags', function (Builder $q) {
                        $q->where('name', 'Contains Nuts');
                    });
                }
                
                if (strpos($allergiesText, 'susu') !== false || strpos($allergiesText, 'milk') !== false 
                    || strpos($allergiesText, 'dairy') !== false) {
                    $query->whereDoesntHave('dietTags', function (Builder $q) {
                        $q->where('name', 'Contains Dairy');
                    });
                }
                
                if (strpos($allergiesText, 'gluten') !== false || strpos($allergiesText, 'wheat') !== false) {
                    $query->whereDoesntHave('dietTags', function (Builder $q) {
                        $q->where('name', 'Contains Gluten');
                    });
                }
                
                if (strpos($allergiesText, 'seafood') !== false || strpos($allergiesText, 'laut') !== false) {
                    $query->whereDoesntHave('dietTags', function (Builder $q) {
                        $q->where('name', 'Contains Seafood');
                    });
                }
            }
        }

        // Filter berdasarkan preferensi diet
        $dietPreference = $user->dietPreference;
        if ($dietPreference) {
            switch ($dietPreference->diet_type) {
                case 'low_carb':
                    $query->where('carbohydrates', '<=', 20);
                    break;
                case 'low_fat':
                    $query->where('fats', '<=', 7);
                    break;
                case 'low_sugar':
                    $query->where('sugars', '<=', 3);
                    break;
                case 'low_sodium':
                    $query->where('sodium', '<=', 300);
                    break;
                case 'high_protein':
                    $query->where('proteins', '>=', 20);
                    break;
                case 'vegetarian':
                    $query->whereHas('dietTags', function (Builder $q) {
                        $q->where('name', 'Vegetarian');
                    });
                    break;
                case 'vegan':
                    $query->whereHas('dietTags', function (Builder $q) {
                        $q->where('name', 'Vegan');
                    });
                    break;
            }

            // Filter berdasarkan target kalori harian
            if ($dietPreference->daily_calorie_target) {
                // Asumsi satu porsi makanan sekitar 30% dari target kalori harian
                $targetMealCalories = $dietPreference->daily_calorie_target * 0.3;
                $query->where('calories', '<=', $targetMealCalories);
            }

            // Filter berdasarkan preferensi gluten dan dairy
            if ($dietPreference->gluten_free) {
                $query->whereDoesntHave('dietTags', function (Builder $q) {
                    $q->where('name', 'Contains Gluten');
                });
            }

            if ($dietPreference->dairy_free) {
                $query->whereDoesntHave('dietTags', function (Builder $q) {
                    $q->where('name', 'Contains Dairy');
                });
            }
        }

        // Pertimbangkan BMI
        $bmi = $user->getBmi();
        if ($bmi) {
            if ($bmi >= 25) { // Overweight/Obese
                $query->orderBy('calories', 'asc'); // Prioritaskan makanan rendah kalori
            } elseif ($bmi < 18.5) { // Underweight
                $query->orderBy('calories', 'desc'); // Prioritaskan makanan tinggi kalori
            }
        }

        // Pertimbangkan umur
        $age = $user->getAge();
        if ($age) {
            if ($age >= 60) { // Lansia
                $query->where('sodium', '<=', 500) // Lebih rendah sodium
                      ->orderBy('fiber', 'desc'); // Prioritaskan serat tinggi
            } elseif ($age <= 18) { // Remaja
                $query->orderBy('proteins', 'desc'); // Prioritaskan protein tinggi untuk pertumbuhan
            }
        }

        // Dapatkan menu dengan eager loading untuk optimasi
        return $query->with(['category', 'cateringPartner', 'dietTags'])
                     ->inRandomOrder() // Sedikit randomisasi untuk variasi
                     ->limit($limit)
                     ->get();
    }

    /**
     * Mendapatkan menu diet umum tanpa mempertimbangkan profil kesehatan
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getGeneralDietMenus(int $limit = 12)
    {
        return Menu::where('is_available', true)
                   ->where('is_diet_menu', true)
                   ->with(['category', 'cateringPartner', 'dietTags'])
                   ->inRandomOrder()
                   ->limit($limit)
                   ->get();
    }

    /**
     * Menghitung kebutuhan kalori harian berdasarkan data pengguna
     *
     * @param User $user
     * @return int|null
     */
    public function calculateDailyCalorieNeeds(User $user)
    {
        // Harus memiliki data dasar untuk perhitungan
        if (!$user->birthdate || !$user->gender || !$user->height || !$user->weight) {
            return null;
        }

        $age = $user->getAge();
        $weight = $user->weight; // kg
        $height = $user->height; // cm
        $bmr = 0;

        // Rumus Harris-Benedict untuk menghitung Basal Metabolic Rate (BMR)
        if ($user->gender === 'male') {
            $bmr = 88.362 + (13.397 * $weight) + (4.799 * $height) - (5.677 * $age);
        } else {
            $bmr = 447.593 + (9.247 * $weight) + (3.098 * $height) - (4.330 * $age);
        }

        // Faktor aktivitas (asumsi level aktivitas sedang = 1.55)
        $calories = $bmr * 1.55;

        // Penyesuaian berdasarkan BMI
        $bmi = $user->getBmi();
        if ($bmi) {
            if ($bmi >= 25) { // Overweight/Obese - kurangi kalori untuk menurunkan berat
                $calories = $calories * 0.85; // Reduksi 15%
            } elseif ($bmi < 18.5) { // Underweight - tambah kalori untuk menambah berat
                $calories = $calories * 1.15; // Tambahan 15%
            }
        }

        // Penyesuaian berdasarkan kondisi kesehatan
        if ($user->healthProfile) {
            if ($user->healthProfile->has_diabetes || 
                $user->healthProfile->has_hypertension || 
                $user->healthProfile->has_heart_disease) {
                $calories = $calories * 0.95; // Reduksi 5% untuk kondisi kronis
            }
        }

        return round($calories);
    }
}