<?php

namespace App\Http\Controllers\Customer;

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
        
        // List pilihan diet untuk dropdown
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
        
        return view('customer.profile.edit', compact(
            'user', 
            'healthProfile', 
            'dietPreference',
            'dietTypes'
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
            'gender' => 'required|in:male,female,other',
            'height' => 'required|numeric|min:50|max:250',
            'weight' => 'required|numeric|min:20|max:300',
        ]);
        
        // Perbarui data dasar pengguna
        $user->update([
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'height' => $request->height,
            'weight' => $request->weight,
        ]);
        
        // Perbarui atau buat profil kesehatan
        $healthProfile = $user->healthProfile ?? new HealthProfile();
        $healthProfile->user_id = $user->id;
        $healthProfile->has_diabetes = $request->has('has_diabetes');
        $healthProfile->has_hypertension = $request->has('has_hypertension');
        $healthProfile->has_heart_disease = $request->has('has_heart_disease');
        $healthProfile->has_cholesterol = $request->has('has_cholesterol');
        $healthProfile->has_food_allergies = $request->has('has_food_allergies');
        $healthProfile->food_allergies_detail = $request->food_allergies_detail;
        $healthProfile->other_conditions = $request->other_conditions;
        $healthProfile->save();
        
        // Validasi data preferensi diet
        $request->validate([
            'diet_type' => 'required|in:regular,low_carb,low_fat,low_sugar,low_sodium,high_protein,vegetarian,vegan',
            'daily_calorie_target' => 'nullable|integer|min:1000|max:5000',
        ]);
        
        // Gunakan service untuk menghitung kebutuhan kalori jika tidak disediakan
        $recommendationService = new DietRecommendationService();
        $calculatedCalories = $recommendationService->calculateDailyCalorieNeeds($user);
        
        // Perbarui atau buat preferensi diet
        $dietPreference = $user->dietPreference ?? new DietPreference();
        $dietPreference->user_id = $user->id;
        $dietPreference->diet_type = $request->diet_type;
        $dietPreference->daily_calorie_target = $request->daily_calorie_target ?? $calculatedCalories;
        $dietPreference->gluten_free = $request->has('gluten_free');
        $dietPreference->dairy_free = $request->has('dairy_free');
        $dietPreference->save();
        
        return redirect()->route('customer.profile.edit', ['tab' => 'health'])
            ->with('success', 'Profil kesehatan Anda berhasil diperbarui.');
    }
}