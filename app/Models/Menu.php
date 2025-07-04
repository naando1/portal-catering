<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'calories',
        'proteins',
        'carbohydrates',
        'fats',
        'sugars',
        'sodium',
        'fiber',
        'cooking_method',
        'carbohydrate_type',
        'is_available',
        'is_diet_menu',
        'category_id',
        'catering_partner_id',
        'ingredient_tags',
        'taste_tags'
    ];

    protected $casts = [
        'price' => 'float',
        'calories' => 'integer',
        'proteins' => 'float',
        'carbohydrates' => 'float',
        'fats' => 'float',
        'sugars' => 'float',
        'sodium' => 'float',
        'fiber' => 'float',
        'is_available' => 'boolean',
        'is_diet_menu' => 'boolean',
        'ingredient_tags' => 'json',
        'taste_tags' => 'json'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cateringPartner()
    {
        return $this->belongsTo(CateringPartner::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relasi dengan diet tags
     */
    public function dietTags()
    {
        return $this->belongsToMany(DietTag::class, 'diet_tag_menu', 'menu_id', 'diet_tag_id');
    }

    /**
     * Mendapatkan rating rata-rata menu
     */
    public function getAverageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    /**
     * Mendapatkan jumlah pesanan menu
     */
    public function getOrderCount()
    {
        return $this->orderItems()->count();
    }

    /**
     * Mendapatkan label metode memasak
     */
    public function getCookingMethodLabel(): string
    {
        $labels = [
            'rebus' => 'Rebus',
            'goreng' => 'Goreng',
            'bakar' => 'Bakar',
            'kukus' => 'Kukus',
            'panggang' => 'Panggang',
        ];

        return $labels[$this->cooking_method] ?? 'Tidak ditentukan';
    }

    /**
     * Mendapatkan label jenis karbohidrat
     */
    public function getCarbohydrateTypeLabel(): string
    {
        $labels = [
            'kompleks' => 'Kompleks',
            'olahan' => 'Olahan',
        ];

        return $labels[$this->carbohydrate_type] ?? 'Tidak ditentukan';
    }

    /**
     * Cek apakah menu mengandung alergen tertentu
     */
    public function containsAllergen($allergen)
    {
        $ingredientTags = json_decode($this->ingredient_tags ?? '[]', true);
        return in_array($allergen, $ingredientTags);
    }

    /**
     * Mendapatkan tag bahan dalam format yang mudah dibaca
     */
    public function getIngredientTagsString()
    {
        $ingredientTags = json_decode($this->ingredient_tags ?? '[]', true);
        if (empty($ingredientTags)) {
            return 'Tidak ada';
        }
        
        $labels = [
            'jeroan' => 'Jeroan',
            'daging' => 'Daging',
            'telur' => 'Telur',
            'susu' => 'Susu',
            'udang' => 'Udang',
            'kacang' => 'Kacang-kacangan',
            'kulit_ayam' => 'Kulit Ayam',
            'kuning_telur' => 'Kuning Telur',
        ];
        
        $result = [];
        foreach ($ingredientTags as $tag) {
            $result[] = $labels[$tag] ?? $tag;
        }
        
        return implode(', ', $result);
    }

    /**
     * Mendapatkan tag rasa dalam format yang mudah dibaca
     */
    public function getTasteTagsString()
    {
        $tasteTags = json_decode($this->taste_tags ?? '[]', true);
        if (empty($tasteTags)) {
            return 'Tidak ada';
        }
        
        $labels = [
            'pedas' => 'Pedas',
            'manis' => 'Manis',
            'gurih' => 'Gurih',
            'asin' => 'Asin',
            'asam' => 'Asam',
            'saus_asin' => 'Saus Asin',
        ];
        
        $result = [];
        foreach ($tasteTags as $tag) {
            $result[] = $labels[$tag] ?? $tag;
        }
        
        return implode(', ', $result);
    }

    /**
     * Mendapatkan daftar tag diet yang cocok untuk kondisi kesehatan tertentu
     */
    public function getDietTagsForHealthCondition($condition)
    {
        $conditionTagMap = [
            'diabetes' => 'diabetes-friendly',
            'hipertensi' => 'hipertensi-friendly',
            'jantung' => 'jantung-friendly',
            'kolesterol' => 'kolesterol-friendly',
            'ambeien' => 'ambeien-friendly',
        ];
        
        $tagSlug = $conditionTagMap[$condition] ?? null;
        
        if (!$tagSlug) {
            return collect([]);
        }
        
        return $this->dietTags()->where('slug', $tagSlug)->get();
    }

    /**
     * Cek apakah menu cocok untuk kondisi kesehatan tertentu
     */
    public function isSuitableFor($condition)
    {
        return $this->getDietTagsForHealthCondition($condition)->isNotEmpty();
    }

    /**
     * Mendapatkan skor kesesuaian dengan kondisi kesehatan
     * Semakin tinggi skor, semakin cocok menu ini untuk kondisi tersebut
     */
    public function getHealthSuitabilityScore($healthProfile)
    {
        $score = 0;
        $conditions = [];
        
        if ($healthProfile->has_diabetes) $conditions[] = 'diabetes';
        if ($healthProfile->has_hypertension) $conditions[] = 'hipertensi';
        if ($healthProfile->has_heart_disease) $conditions[] = 'jantung';
        if ($healthProfile->has_cholesterol) $conditions[] = 'kolesterol';
        if ($healthProfile->has_hemorrhoids) $conditions[] = 'ambeien';
        
        if (empty($conditions)) {
            return 100; // Tidak ada kondisi khusus, semua menu cocok
        }
        
        foreach ($conditions as $condition) {
            if ($this->isSuitableFor($condition)) {
                $score += (100 / count($conditions));
            }
        }
        
        return $score;
    }

    /**
     * Menghitung skor kesesuaian dengan target kalori pengguna
     * Semakin tinggi skor, semakin sesuai menu ini dengan target kalori
     */
    public function getCalorieMatchScore($targetCalories, $mealType = 'lunch')
    {
        // Persentase target kalori berdasarkan waktu makan
        $mealPercentages = [
            'breakfast' => 0.25, // 25% dari target harian
            'lunch' => 0.35,     // 35% dari target harian
            'dinner' => 0.30,    // 30% dari target harian
            'snack' => 0.10,     // 10% dari target harian
        ];
        
        $percentage = $mealPercentages[$mealType] ?? 0.35; // Default ke makan siang jika tidak ditentukan
        
        // Kalori ideal untuk waktu makan ini
        $idealCalories = $targetCalories * $percentage;
        
        // Hitung deviasi (perbedaan) dari kalori ideal
        $deviation = abs($this->calories - $idealCalories) / $idealCalories;
        
        // Konversi ke skor (0-100), semakin kecil deviasi semakin tinggi skor
        $score = max(0, 100 - ($deviation * 100));
        
        // Jika deviasi lebih dari 50%, skor minimal 0
        return max(0, $score);
    }
}