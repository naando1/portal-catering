<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    // Tambahkan field baru ke fillable
    protected $fillable = [
        'catering_partner_id',
        'category_id',
        'name',
        'description',
        'price',
        'image',
        'is_available',
        'calories',
        'carbohydrates',
        'proteins',
        'fats',
        'sugars',
        'sodium',
        'is_diet_menu',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_diet_menu' => 'boolean',
        'price' => 'decimal:2',
        'calories' => 'decimal:2',
        'carbohydrates' => 'decimal:2',
        'proteins' => 'decimal:2',
        'fats' => 'decimal:2',
        'sugars' => 'decimal:2',
        'sodium' => 'decimal:2',
    ];

    
    public function cateringPartner()
    {
        return $this->belongsTo(CateringPartner::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relasi baru untuk fitur diet
    public function dietTags()
    {
        return $this->belongsToMany(DietTag::class, 'diet_tag_menu');
    }

    // Method untuk menghitung rating rata-rata
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    // Method untuk mengecek kesesuaian makanan dengan kondisi kesehatan
    public function isSuitableForDiabetic()
    {
        return $this->is_diet_menu && $this->sugars <= 5; // Contoh: makanan dengan kandungan gula <= 5g
    }

    public function isSuitableForHypertension()
    {
        return $this->is_diet_menu && $this->sodium <= 400; // Contoh: makanan dengan kandungan sodium <= 400mg
    }

    public function isSuitableForHeartDisease()
    {
        return $this->is_diet_menu && $this->fats <= 10 && $this->sodium <= 400; // Contoh: rendah lemak dan sodium
    }

    public function isLowCalorie()
    {
        return $this->calories <= 400; // Contoh: makanan dengan kalori <= 400
    }

    public function isMediumCalorie()
    {
        return $this->calories > 400 && $this->calories <= 600; // Contoh: makanan dengan kalori 400-600
    }

    public function isHighCalorie()
    {
        return $this->calories > 600; // Contoh: makanan dengan kalori > 600
    }
}