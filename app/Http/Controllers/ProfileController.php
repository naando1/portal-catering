<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\HealthProfile;
use App\Models\DietPreference;
use App\Services\DietRecommendationService;

class ProfileController extends Controller
{
    /**
     * Menampilkan form edit profil customer
     */
    public function edit()
    {
        $user = Auth::user();
        $healthProfile = $user->healthProfile ?? new HealthProfile();
        $dietPreference = $user->dietPreference ?? new DietPreference();
        
        // List pilihan untuk dropdown
        $dietTypes = [
            'regular' => 'Regular (Seimbang)',
            'low_carb' => 'Rendah Karbohidrat',
            'low_fat' => 'Rendah Lemak',
            'low_sugar' => 'Rendah Gula',
            'low_sodium' => 'Rendah Sodium/Garam',
            'high_protein' => 'Tinggi Protein',
            'vegetarian' => 'Vegetarian',
            'vegan' => 'Vegan',
        ];

        $activityLevels = [
            'sedentari' => 'Sedentari (tidak aktif)',
            'ringan' => 'Ringan (olahraga ringan 1-3 hari/minggu)',
            'sedang' => 'Sedang (olahraga sedang 3-5 hari/minggu)',
            'berat' => 'Berat (olahraga berat 6-7 hari/minggu)',
            'sangat_berat' => 'Sangat Berat (pekerjaan fisik + olahraga)',
        ];

        $dietGoals = [
            'turun_bb' => 'Turun Berat Badan',
            'naik_bb' => 'Naik Berat Badan',
            'jaga_bb' => 'Jaga Berat Badan',
        ];

        // Opsi alergi makanan
        $allergyOptions = [
            'udang' => 'Udang',
            'telur' => 'Telur',
            'kacang' => 'Kacang-kacangan',
            'susu' => 'Susu/Dairy',
            'ikan' => 'Ikan',
            'seafood' => 'Seafood',
            'gluten' => 'Gluten',
        ];

        // Opsi preferensi rasa
        $tasteOptions = [
            'pedas' => 'Pedas',
            'gurih' => 'Gurih',
            'manis' => 'Manis',
            'asin' => 'Asin',
            'asam' => 'Asam',
        ];

        // Opsi teknik masak
        $cookingMethods = [
            'rebus' => 'Rebus',
            'goreng' => 'Goreng',
            'bakar' => 'Bakar',
            'kukus' => 'Kukus',
            'panggang' => 'Panggang',
            'tumis' => 'Tumis',
        ];
        
        return view('customer.profile.edit', compact(
            'user', 
            'healthProfile', 
            'dietPreference',
            'dietTypes',
            'activityLevels',
            'dietGoals',
            'allergyOptions',
            'tasteOptions',
            'cookingMethods'
        ));
    }
    
    /**
     * Memperbarui profil customer
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Update data user
        $data = $request->only(['name', 'email', 'phone_number', 'address']);
        
        // Upload profile picture jika ada
        if ($request->hasFile('profile_picture')) {
            // Hapus gambar lama jika ada
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            
            // Upload gambar baru
            $data['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        
        $user->update($data);
        
        return redirect()->route('customer.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }
    
    /**
     * Memperbarui password customer
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();
        
        return redirect()->route('customer.profile.edit', ['tab' => 'password'])
            ->with('success', 'Password berhasil diubah.');
    }
    
    /**
     * Memperbarui profil kesehatan customer
     */
    public function updateHealthProfile(Request $request)
    {
        $user = Auth::user();
        
        // Validasi data umum pengguna
        $request->validate([
            'birthdate' => 'required|date|before:today',
            'gender' => 'required|in:male,female,pria,wanita',
            'height' => 'required|numeric|min:50|max:250',
            'weight' => 'required|numeric|min:20|max:300',
            'activity_level' => 'required|in:sedentari,ringan,sedang,berat,sangat_berat',
        ]);
        
        // Perbarui data dasar pengguna
        $user->update([
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'height' => $request->height,
            'weight' => $request->weight,
            'activity_level' => $request->activity_level,
        ]);
        
        // Perbarui atau buat profil kesehatan
        $healthProfile = $user->healthProfile ?? new HealthProfile();
        $healthProfile->user_id = $user->id;
        $healthProfile->has_diabetes = $request->has('has_diabetes');
        $healthProfile->has_hypertension = $request->has('has_hypertension');
        $healthProfile->has_heart_disease = $request->has('has_heart_disease');
        $healthProfile->has_cholesterol = $request->has('has_cholesterol');
        $healthProfile->has_hemorrhoids = $request->has('has_hemorrhoids');
        $healthProfile->has_food_allergies = $request->has('has_food_allergies');
        $healthProfile->food_allergies_detail = $request->food_allergies_detail;
        $healthProfile->other_conditions = $request->other_conditions;
        $healthProfile->save();
        
        // Validasi data preferensi diet
        $request->validate([
            'diet_type' => 'required|in:regular,low_carb,low_fat,low_sugar,low_sodium,high_protein,vegetarian,vegan',
            'diet_goal' => 'required|in:turun_bb,naik_bb,jaga_bb',
            'deficit_surplus_percentage' => 'nullable|integer|min:10|max:20',
            'daily_calorie_target' => 'nullable|integer|min:1000|max:5000',
            'food_allergies' => 'nullable|array',
            'taste_preferences' => 'nullable|array',
            'cooking_method_preferences' => 'nullable|array',
        ]);
        
        // Gunakan service untuk menghitung kebutuhan kalori jika tidak disediakan
        $recommendationService = new DietRecommendationService();
        
        // Hitung kalori target berdasarkan input user atau hitung otomatis
        $targetCalories = $request->daily_calorie_target;
        if (!$targetCalories) {
            // Hitung otomatis berdasarkan BMR, TDEE, dan tujuan diet
            $bmr = $user->getBmr();
            $tdee = $user->getTdee();
            $percentage = $request->deficit_surplus_percentage ?? 15;
            
            if ($tdee) {
                switch ($request->diet_goal) {
                    case 'turun_bb':
                        $targetCalories = round($tdee * (1 - ($percentage / 100)));
                        break;
                    case 'naik_bb':
                        $targetCalories = round($tdee * (1 + ($percentage / 100)));
                        break;
                    case 'jaga_bb':
                    default:
                        $targetCalories = $tdee;
                        break;
                }
            }
        }
        
        // Perbarui atau buat preferensi diet
        $dietPreference = $user->dietPreference ?? new DietPreference();
        $dietPreference->user_id = $user->id;
        $dietPreference->diet_type = $request->diet_type;
        $dietPreference->diet_goal = $request->diet_goal;
        $dietPreference->deficit_surplus_percentage = $request->deficit_surplus_percentage;
        $dietPreference->daily_calorie_target = $targetCalories;
        $dietPreference->gluten_free = $request->has('gluten_free');
        $dietPreference->dairy_free = $request->has('dairy_free');
        
        // Simpan preferensi baru sebagai JSON
        $dietPreference->food_allergies = json_encode($request->food_allergies ?? []);
        $dietPreference->taste_preferences = json_encode($request->taste_preferences ?? []);
        $dietPreference->cooking_method_preferences = json_encode($request->cooking_method_preferences ?? []);
        
        $dietPreference->save();
        
        // Hitung dan simpan metrics user
        $recommendationService->calculateAndStoreUserMetrics($user);
        
        return redirect()->route('customer.profile.edit', ['tab' => 'health'])
            ->with('success', 'Profil kesehatan Anda berhasil diperbarui.');
    }
}