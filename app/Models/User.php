<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'phone_number',
        'address',
        'profile_picture',
        'birthdate',
        'gender',
        'height',
        'weight',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function cateringPartner()
    {
        return $this->hasOne(CateringPartner::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function healthProfile()
    {
        return $this->hasOne(HealthProfile::class);
    }

    public function dietPreference()
    {
        return $this->hasOne(DietPreference::class);
    }

    // Method helper untuk informasi kesehatan
    public function getAge()
    {
        return $this->birthdate ? Carbon::parse($this->birthdate)->age : null;
    }

    public function getBmi()
    {
        if ($this->height && $this->weight) {
            $heightInMeters = $this->height / 100;
            return round($this->weight / ($heightInMeters * $heightInMeters), 1);
        }
        return null;
    }

    public function getBmiCategory()
    {
        $bmi = $this->getBmi();
        if (!$bmi) {
            return null;
        }

        if ($bmi < 18.5) {
            return 'Underweight';
        } elseif ($bmi < 25) {
            return 'Normal';
        } elseif ($bmi < 30) {
            return 'Overweight';
        } else {
            return 'Obese';
        }
    }

    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    public function isPartner()
    {
        return $this->role->name === 'partner';
    }

    public function isCustomer()
    {
        return $this->role->name === 'customer';
    }

    // Method untuk cek kelengkapan profil kesehatan
    public function hasCompleteHealthProfile()
    {
        return $this->healthProfile && $this->birthdate && $this->gender && $this->height && $this->weight;
    }
}

