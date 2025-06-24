<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'has_diabetes',
        'has_hypertension',
        'has_heart_disease',
        'has_cholesterol',
        'has_hemorrhoids',
        'has_food_allergies',
        'food_allergies_detail',
        'other_conditions',
    ];

    protected $casts = [
        'has_diabetes' => 'boolean',
        'has_hypertension' => 'boolean',
        'has_heart_disease' => 'boolean',
        'has_cholesterol' => 'boolean',
        'has_hemorrhoids' => 'boolean',
        'has_food_allergies' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get list of active health conditions
     */
    public function getActiveConditions()
    {
        $conditions = [];
        
        if ($this->has_diabetes) $conditions[] = 'Diabetes Mellitus';
        if ($this->has_hypertension) $conditions[] = 'Hipertensi';
        if ($this->has_heart_disease) $conditions[] = 'Penyakit Jantung';
        if ($this->has_cholesterol) $conditions[] = 'Kolesterol Tinggi';
        if ($this->has_hemorrhoids) $conditions[] = 'Ambeien';
        
        return $conditions;
    }

    /**
     * Get active conditions as formatted string
     */
    public function getActiveConditionsString()
    {
        $conditions = $this->getActiveConditions();
        
        if (empty($conditions)) {
            return 'Tidak ada kondisi kesehatan khusus';
        }
        
        return implode(', ', $conditions);
    }

    /**
     * Check if has any health condition
     */
    public function hasAnyHealthCondition()
    {
        return $this->has_diabetes || 
               $this->has_hypertension || 
               $this->has_heart_disease || 
               $this->has_cholesterol || 
               $this->has_hemorrhoids;
    }

    /**
     * Get dietary restrictions based on health conditions
     */
    public function getDietaryRestrictions()
    {
        $restrictions = [];
        
        if ($this->has_diabetes) {
            $restrictions[] = 'Hindari gula tinggi (>10g)';
            $restrictions[] = 'Hindari karbohidrat olahan';
            $restrictions[] = 'Batasi lemak (≤10g)';
        }
        
        if ($this->has_hypertension) {
            $restrictions[] = 'Batasi sodium (≤600mg)';
            $restrictions[] = 'Hindari saus asin dan MSG';
        }
        
        if ($this->has_heart_disease) {
            $restrictions[] = 'Batasi lemak jenuh (≤10g)';
            $restrictions[] = 'Batasi sodium (≤600mg)';
            $restrictions[] = 'Batasi gula (≤10g)';
            $restrictions[] = 'Hindari jeroan dan kuning telur';
        }
        
        if ($this->has_cholesterol) {
            $restrictions[] = 'Batasi lemak jenuh (≤10g)';
            $restrictions[] = 'Hindari gorengan';
            $restrictions[] = 'Hindari jeroan dan kulit ayam';
        }
        
        if ($this->has_hemorrhoids) {
            $restrictions[] = 'Perbanyak serat (≥3g)';
            $restrictions[] = 'Hindari makanan pedas';
            $restrictions[] = 'Hindari gorengan';
        }
        
        return array_unique($restrictions);
    }

    /**
     * Get recommended foods based on health conditions
     */
    public function getRecommendedFoods()
    {
        $recommendations = [];
        
        if ($this->has_diabetes) {
            $recommendations[] = 'Karbohidrat kompleks (nasi merah, oat)';
            $recommendations[] = 'Protein rendah lemak';
            $recommendations[] = 'Sayuran berserat tinggi';
        }
        
        if ($this->has_hypertension) {
            $recommendations[] = 'Makanan rendah sodium';
            $recommendations[] = 'Buah-buahan segar';
            $recommendations[] = 'Sayuran hijau';
        }
        
        if ($this->has_heart_disease) {
            $recommendations[] = 'Ikan salmon dan sarden';
            $recommendations[] = 'Kacang-kacangan';
            $recommendations[] = 'Minyak zaitun';
            $recommendations[] = 'Oatmeal';
        }
        
        if ($this->has_cholesterol) {
            $recommendations[] = 'Makanan dikukus atau direbus';
            $recommendations[] = 'Protein nabati';
            $recommendations[] = 'Serat larut tinggi';
        }
        
        if ($this->has_hemorrhoids) {
            $recommendations[] = 'Makanan tinggi serat';
            $recommendations[] = 'Buah-buahan';
            $recommendations[] = 'Sayuran berdaun hijau';
            $recommendations[] = 'Air putih yang cukup';
        }
        
        return array_unique($recommendations);
    }

    /**
     * Check if specific condition exists
     */
    public function hasCondition($condition)
    {
        $conditionMap = [
            'diabetes' => 'has_diabetes',
            'hipertensi' => 'has_hypertension',
            'jantung' => 'has_heart_disease',
            'kolesterol' => 'has_cholesterol',
            'ambeien' => 'has_hemorrhoids',
        ];
        
        $field = $conditionMap[$condition] ?? null;
        return $field ? $this->$field : false;
    }

    /**
     * Get health risk level based on conditions
     */
    public function getHealthRiskLevel()
    {
        $conditionCount = collect([
            $this->has_diabetes,
            $this->has_hypertension,
            $this->has_heart_disease,
            $this->has_cholesterol,
            $this->has_hemorrhoids
        ])->filter()->count();
        
        if ($conditionCount === 0) return 'Rendah';
        if ($conditionCount <= 2) return 'Sedang';
        return 'Tinggi';
    }

    /**
     * Get health risk color for UI
     */
    public function getHealthRiskColor()
    {
        $level = $this->getHealthRiskLevel();
        
        return match($level) {
            'Rendah' => 'success',
            'Sedang' => 'warning',
            'Tinggi' => 'danger',
            default => 'secondary'
        };
    }
}