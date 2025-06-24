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
        
        // List pilihan untuk dropdown
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

        // Opsi alergi makanan sesuai requirement
        $allergyOptions = [
            'udang' => 'Udang',
            'telur' => 'Telur',
            'kacang' => 'Kacang-kacangan',
            'susu' => 'Susu/Dairy',
            'ikan' => 'Ikan',
            'seafood' => 'Seafood',
            'gluten' => 'Gluten',
        ];

        // Opsi preferensi rasa sesuai requirement
        $tasteOptions = [
            'pedas' => 'Pedas',
            'manis' => 'Manis',
            'gurih' => 'Gurih',
            'asin' => 'Asin',
        ];

        // Opsi teknik masak sesuai requirement
        $cookingMethods = [
            'rebus' => 'Rebus',
            'goreng' => 'Goreng',
            'bakar' => 'Bakar',
            'kukus' => 'Kukus',
            'panggang' => 'Panggang',
        ];
        
        return view('customer.profile.edit', compact(
            'user', 
            'healthProfile', 
            'dietPreference',
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
        $user = auth()->user();
        
        // Validasi input dasar
        $validated = $request->validate([
            'birthdate' => 'required|date|before:today',
            'gender' => 'required|in:pria,wanita',
            'height' => 'required|numeric|min:100|max:250',
            'weight' => 'required|numeric|min:30|max:300',
            'activity_level' => 'required|in:sedentari,ringan,sedang,berat,sangat_berat',
            
            // Kondisi kesehatan
            'has_diabetes' => 'nullable|boolean',
            'has_hypertension' => 'nullable|boolean',
            'has_heart_disease' => 'nullable|boolean',
            'has_cholesterol' => 'nullable|boolean',
            'has_hemorrhoids' => 'nullable|boolean',
            
            // Preferensi diet
            'diet_goal' => 'required|in:turun_bb,naik_bb,jaga_bb',
            'deficit_surplus_percentage' => 'nullable|numeric|min:10|max:20',
            'food_allergies' => 'nullable|array',
            'taste_preferences' => 'nullable|array',
            'cooking_method_preferences' => 'nullable|array',
        ]);
        
        // Update data user
        $user->update([
            'birthdate' => $validated['birthdate'],
            'gender' => $validated['gender'],
            'height' => $validated['height'],
            'weight' => $validated['weight'],
            'activity_level' => $validated['activity_level'],
        ]);
        
        // Update atau buat profil kesehatan
        $user->healthProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'has_diabetes' => $request->has('has_diabetes'),
                'has_hypertension' => $request->has('has_hypertension'),
                'has_heart_disease' => $request->has('has_heart_disease'),
                'has_cholesterol' => $request->has('has_cholesterol'),
                'has_hemorrhoids' => $request->has('has_hemorrhoids'),
                'has_food_allergies' => !empty($validated['food_allergies']),
                'food_allergies_detail' => !empty($validated['food_allergies']) ? implode(', ', $validated['food_allergies']) : null,
            ]
        );
        
        // Update atau buat preferensi diet
        $user->dietPreference()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'diet_goal' => $validated['diet_goal'],
                'deficit_surplus_percentage' => $validated['deficit_surplus_percentage'] ?? 15,
                'food_allergies' => $validated['food_allergies'] ?? [],
                'taste_preferences' => $validated['taste_preferences'] ?? [],
                'cooking_method_preferences' => $validated['cooking_method_preferences'] ?? [],
            ]
        );
        
        // Hitung dan simpan metrics user (BMI, BMR, TDEE, Target Calories)
        $dietService = app(DietRecommendationService::class);
        $dietService->calculateAndStoreUserMetrics($user);
        
        return redirect()->back()->with('success', 'Profil kesehatan berhasil diperbarui.');
    }
}