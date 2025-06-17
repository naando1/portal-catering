<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CateringPartner extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'description',
        'logo',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }
}