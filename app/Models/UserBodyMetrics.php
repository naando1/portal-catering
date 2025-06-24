<?php
// app/Models/UserBodyMetrics.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBodyMetrics extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bmi',
        'bmr',
        'tdee',
        'target_calories',
        'last_calculated_at'
    ];

    protected $casts = [
        'bmi' => 'float',
        'bmr' => 'integer',
        'tdee' => 'integer',
        'target_calories' => 'integer',
        'last_calculated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menghitung BMI berdasarkan tinggi dan berat badan
     */
    public function calculateBMI(float $heightCm, float $weight): float
    {
        $heightM = $heightCm / 100;
        return round($weight / ($heightM * $heightM), 1);
    }

    /**
     * Menghitung BMR menggunakan rumus Mifflin-St Jeor
     */
    public function calculateBMR(string $gender, float $weight, float $heightCm, int $age): int
    {
        if ($gender == 'male' || $gender == 'pria') {
            return round((10 * $weight) + (6.25 * $heightCm) - (5 * $age) + 5);
        } else {
            return round((10 * $weight) + (6.25 * $heightCm) - (5 * $age) - 161);
        }
    }

    /**
     * Menghitung TDEE berdasarkan BMR dan level aktivitas
     */
    public function calculateTDEE(int $bmr, string $activityLevel): int
    {
        $activityFactors = [
            'sedentari' => 1.2,
            'ringan' => 1.375,
            'sedang' => 1.55,
            'berat' => 1.725,
            'sangat_berat' => 1.9,
        ];
        
        $factor = $activityFactors[$activityLevel] ?? 1.2;
        return round($bmr * $factor);
    }

    /**
     * Menghitung target kalori berdasarkan TDEE dan tujuan diet
     */
    public function calculateTargetCalories(int $tdee, string $dietGoal, int $percentage = 15): int
    {
        switch ($dietGoal) {
            case 'turun_bb':
                return round($tdee * (1 - ($percentage / 100)));
            case 'naik_bb':
                return round($tdee * (1 + ($percentage / 100)));
            case 'jaga_bb':
            default:
                return $tdee;
        }
    }

    /**
     * Mendapatkan kategori BMI
     */
    public function getBmiCategory(): string
    {
        if (!$this->bmi) {
            return 'Tidak diketahui';
        }
        
        if ($this->bmi < 18.5) {
            return 'Kurus (Underweight)';
        } elseif ($this->bmi < 25) {
            return 'Normal';
        } elseif ($this->bmi < 30) {
            return 'Kelebihan Berat Badan (Overweight)';
        } else {
            return 'Obesitas';
        }
    }
}