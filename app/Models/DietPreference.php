<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'diet_type',
        'diet_goal',
        'deficit_surplus_percentage',
        'daily_calorie_target',
        'gluten_free',
        'dairy_free',
        'taste_preferences',
        'cooking_method_preferences',
        'food_allergies'
    ];

    protected $casts = [
        'gluten_free' => 'boolean',
        'dairy_free' => 'boolean',
        'deficit_surplus_percentage' => 'integer',
        'daily_calorie_target' => 'integer',
        'taste_preferences' => 'json',
        'cooking_method_preferences' => 'json',
        'food_allergies' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mendapatkan label tujuan diet
     */
    public function getDietGoalLabel(): string
    {
        $labels = [
            'turun_bb' => 'Turun Berat Badan',
            'naik_bb' => 'Naik Berat Badan',
            'jaga_bb' => 'Jaga Berat Badan',
        ];

        return $labels[$this->diet_goal] ?? 'Tidak ditentukan';
    }

    /**
     * Mendapatkan label jenis diet
     */
    public function getDietTypeLabel(): string
    {
        $labels = [
            'regular' => 'Regular (Seimbang)',
            'low_carb' => 'Rendah Karbohidrat',
            'low_fat' => 'Rendah Lemak',
            'low_sugar' => 'Rendah Gula',
            'low_sodium' => 'Rendah Sodium/Garam',
            'high_protein' => 'Tinggi Protein',
            'vegetarian' => 'Vegetarian',
            'vegan' => 'Vegan',
        ];

        return $labels[$this->diet_type] ?? 'Tidak ditentukan';
    }

    /**
     * Get food allergies as formatted string
     */
    public function getFoodAllergiesString()
    {
        if (!$this->food_allergies || empty($this->food_allergies)) {
            return 'Tidak ada';
        }

        $labels = [
            'udang' => 'Udang',
            'telur' => 'Telur',
            'kacang' => 'Kacang-kacangan',
            'susu' => 'Susu/Dairy',
            'ikan' => 'Ikan',
            'seafood' => 'Seafood',
            'gluten' => 'Gluten',
        ];

        $allergyLabels = array_map(function($allergy) use ($labels) {
            return $labels[$allergy] ?? $allergy;
        }, $this->food_allergies);

        return implode(', ', $allergyLabels);
    }

    /**
     * Get taste preferences as formatted string
     */
    public function getTastePreferencesString()
    {
        if (!$this->taste_preferences || empty($this->taste_preferences)) {
            return 'Tidak ada';
        }

        $labels = [
            'pedas' => 'Pedas',
            'gurih' => 'Gurih',
            'manis' => 'Manis',
            'asin' => 'Asin',
            'asam' => 'Asam',
        ];

        $tasteLabels = array_map(function($taste) use ($labels) {
            return $labels[$taste] ?? $taste;
        }, $this->taste_preferences);

        return implode(', ', $tasteLabels);
    }

    /**
     * Get cooking method preferences as formatted string
     */
    public function getCookingMethodPreferencesString()
    {
        if (!$this->cooking_method_preferences || empty($this->cooking_method_preferences)) {
            return 'Tidak ada';
        }

        $labels = [
            'rebus' => 'Rebus',
            'goreng' => 'Goreng',
            'bakar' => 'Bakar',
            'kukus' => 'Kukus',
            'panggang' => 'Panggang',
            'tumis' => 'Tumis',
        ];

        $methodLabels = array_map(function($method) use ($labels) {
            return $labels[$method] ?? $method;
        }, $this->cooking_method_preferences);

        return implode(', ', $methodLabels);
    }

    /**
     * Check if user has specific food allergy
     */
    public function hasAllergy($allergen)
    {
        return $this->food_allergies && in_array($allergen, $this->food_allergies);
    }

    /**
     * Check if user likes specific taste
     */
    public function likesTaste($taste)
    {
        return $this->taste_preferences && in_array($taste, $this->taste_preferences);
    }

    /**
     * Check if user prefers specific cooking method
     */
    public function prefersCookingMethod($method)
    {
        return $this->cooking_method_preferences && in_array($method, $this->cooking_method_preferences);
    }

    /**
     * Get compatibility score dengan menu tertentu
     */
    public function getCompatibilityScore($menu)
    {
        $score = 0;

        // Score berdasarkan alergi (paling penting - bisa 0 jika ada alergi)
        if ($this->food_allergies) {
            foreach ($this->food_allergies as $allergy) {
                if ($menu->containsAllergen($allergy)) {
                    return 0; // Hard rejection untuk alergi
                }
            }
        }

        // Score berdasarkan preferensi rasa (25 poin)
        if ($this->taste_preferences && $menu->taste_tags) {
            $matchingTastes = array_intersect($this->taste_preferences, $menu->taste_tags);
            $score += (count($matchingTastes) / count($this->taste_preferences)) * 25;
        }

        // Score berdasarkan preferensi teknik masak (20 poin)
        if ($this->cooking_method_preferences && $menu->cooking_method) {
            if (in_array($menu->cooking_method, $this->cooking_method_preferences)) {
                $score += 20;
            }
        }

        // Score berdasarkan diet type (15 poin)
        switch ($this->diet_type) {
            case 'low_carb':
                if ($menu->carbohydrates <= 20) $score += 15;
                break;
            case 'low_fat':
                if ($menu->fats <= 5) $score += 15;
                break;
            case 'low_sugar':
                if ($menu->sugars <= 5) $score += 15;
                break;
            case 'low_sodium':
                if ($menu->sodium <= 400) $score += 15;
                break;
            case 'high_protein':
                if ($menu->proteins >= 20) $score += 15;
                break;
            case 'vegetarian':
                if (!$menu->containsAllergen('daging') && !$menu->containsAllergen('ikan')) $score += 15;
                break;
            case 'vegan':
                if (!$menu->containsAllergen('daging') && !$menu->containsAllergen('ikan') && 
                    !$menu->containsAllergen('telur') && !$menu->containsAllergen('susu')) $score += 15;
                break;
            default:
                $score += 10; // Regular diet
        }

        // Score berdasarkan gluten free (10 poin)
        if ($this->gluten_free && !$menu->containsAllergen('gluten')) {
            $score += 10;
        }

        // Score berdasarkan dairy free (10 poin)
        if ($this->dairy_free && !$menu->containsAllergen('susu')) {
            $score += 10;
        }

        return min($score, 100); // Maximum 100 poin
    }
}