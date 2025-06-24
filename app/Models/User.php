<?php

namespace App\Models;

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
        'activity_level',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birthdate' => 'date',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    // Existing relationships
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

    public function bodyMetrics()
    {
        return $this->hasOne(UserBodyMetrics::class);
    }

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
            return 'Kurus (Underweight)';
        } elseif ($bmi < 25) {
            return 'Normal';
        } elseif ($bmi < 30) {
            return 'Kelebihan Berat Badan (Overweight)';
        } else {
            return 'Obesitas';
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

    public function hasCompleteHealthProfile()
    {
        return $this->healthProfile && 
               $this->birthdate && 
               $this->gender && 
               $this->height && 
               $this->weight &&
               $this->activity_level;
    }

    /**
     * Get BMR (Basal Metabolic Rate) menggunakan rumus Mifflin-St Jeor
     */
    public function getBmr()
    {
        if (!$this->weight || !$this->height || !$this->birthdate || !$this->gender) {
            return null;
        }

        $age = $this->getAge();
        
        if ($this->gender === 'male' || $this->gender === 'pria') {
            return (10 * $this->weight) + (6.25 * $this->height) - (5 * $age) + 5;
        } else {
            return (10 * $this->weight) + (6.25 * $this->height) - (5 * $age) - 161;
        }
    }

    /**
     * Get TDEE (Total Daily Energy Expenditure)
     */
    public function getTdee()
    {
        $bmr = $this->getBmr();
        if (!$bmr || !$this->activity_level) {
            return null;
        }

        $activityFactors = [
            'sedentari' => 1.2,
            'ringan' => 1.375,
            'sedang' => 1.55,
            'berat' => 1.725,
            'sangat_berat' => 1.9,
        ];

        $factor = $activityFactors[$this->activity_level] ?? 1.2;
        return round($bmr * $factor);
    }

    /**
     * Get target kalori berdasarkan tujuan diet
     */
    public function getTargetCalories()
    {
        $tdee = $this->getTdee();
        if (!$tdee || !$this->dietPreference) {
            return null;
        }

        $dietGoal = $this->dietPreference->diet_goal ?? 'jaga_bb';
        $percentage = $this->dietPreference->deficit_surplus_percentage ?? 15;

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
     * Get activity level dalam bahasa Indonesia
     */
    public function getActivityLevelLabel()
    {
        $labels = [
            'sedentari' => 'Sedentari (tidak aktif)',
            'ringan' => 'Ringan (olahraga ringan 1-3 hari/minggu)',
            'sedang' => 'Sedang (olahraga sedang 3-5 hari/minggu)',
            'berat' => 'Berat (olahraga berat 6-7 hari/minggu)',
            'sangat_berat' => 'Sangat Berat (pekerjaan fisik + olahraga)',
        ];

        return $labels[$this->activity_level] ?? 'Tidak ditentukan';
    }

    /**
     * Get diet goal dalam bahasa Indonesia
     */
    public function getDietGoalLabel()
    {
        if (!$this->dietPreference) {
            return 'Tidak ditentukan';
        }

        $labels = [
            'turun_bb' => 'Turun Berat Badan',
            'naik_bb' => 'Naik Berat Badan',
            'jaga_bb' => 'Jaga Berat Badan',
        ];

        return $labels[$this->dietPreference->diet_goal] ?? 'Tidak ditentukan';
    }

    /**
     * Check if user has specific health condition
     */
    public function hasHealthCondition($condition)
    {
        if (!$this->healthProfile) {
            return false;
        }

        $conditionMap = [
            'diabetes' => 'has_diabetes',
            'hipertensi' => 'has_hypertension',
            'kolesterol' => 'has_cholesterol',
            'jantung' => 'has_heart_disease',
            'ambeien' => 'has_hemorrhoids',
        ];

        $field = $conditionMap[$condition] ?? null;
        return $field ? $this->healthProfile->$field : false;
    }
}