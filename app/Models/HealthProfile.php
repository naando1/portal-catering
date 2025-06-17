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
        'has_food_allergies',
        'food_allergies_detail',
        'other_conditions',
    ];

    protected $casts = [
        'has_diabetes' => 'boolean',
        'has_hypertension' => 'boolean',
        'has_heart_disease' => 'boolean',
        'has_cholesterol' => 'boolean',
        'has_food_allergies' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}