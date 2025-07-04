<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecommendationFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'menu_id',
        'is_relevant',
        'comment'
    ];

    protected $casts = [
        'is_relevant' => 'boolean',
    ];

    protected $table = 'recommendation_feedbacks';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}
