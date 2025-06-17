<?php
// app/Http/Controllers/Partner/ProfileController.php
namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\CateringPartner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $partner = $user->cateringPartner;
        return view('partner.profile.edit', compact('user', 'partner'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $partner = $user->cateringPartner;

        $request->validate([
            'name' => 'required|string|max:255',
            'business_name' => 'required|string|max:255',
            'description' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'profile_picture' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed'
        ]);

        // Update user data
        $userData = [
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ];

        // Update profile picture if uploaded
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Update password if requested
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
            }

            $userData['password'] = Hash::make($request->password);
        }

        $user->update($userData);

        // Update partner data
        $partnerData = [
            'business_name' => $request->business_name,
            'description' => $request->description,
        ];

        // Update logo if uploaded
        if ($request->hasFile('logo')) {
            if ($partner->logo) {
                Storage::disk('public')->delete($partner->logo);
            }
            $partnerData['logo'] = $request->file('logo')->store('partner_logos', 'public');
        }

        $partner->update($partnerData);

        return redirect()->route('partner.profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }
}