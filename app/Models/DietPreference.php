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
        'daily_calorie_target',
        'gluten_free',
        'dairy_free',
    ];

    protected $casts = [
        'gluten_free' => 'boolean',
        'dairy_free' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}