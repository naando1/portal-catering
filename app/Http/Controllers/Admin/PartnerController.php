<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CateringPartner;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = CateringPartner::with('user');
        
        // Search by business name
        if ($request->has('search') && $request->search) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        
        $partners = $query->orderBy('business_name')->paginate(10);
        
        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'business_name' => 'required|string|max:255',
            'description' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'profile_picture' => 'nullable|image|max:2048'
        ]);
        
        // Get partner role
        $partnerRole = Role::where('name', 'partner')->first();
        
        // Create user
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $partnerRole->id,
            'phone_number' => $request->phone_number,
            'address' => $request->address
        ];
        
        if ($request->hasFile('profile_picture')) {
            $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        
        $user = User::create($userData);
        
        // Create catering partner
        $partnerData = [
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'description' => $request->description,
            'is_active' => true
        ];
        
        if ($request->hasFile('logo')) {
            $partnerData['logo'] = $request->file('logo')->store('partner_logos', 'public');
        }
        
        CateringPartner::create($partnerData);
        
        return redirect()->route('admin.partners.index')->with('success', 'Mitra catering berhasil ditambahkan.');
    }

    public function show(CateringPartner $partner)
    {
        $partner->load('user');
        
        // Count menus and orders
        $totalMenus = $partner->menus()->count();
        $menuIds = $partner->menus()->pluck('id');
        $totalOrders = \App\Models\OrderItem::whereIn('menu_id', $menuIds)
            ->distinct('order_id')
            ->count('order_id');
        
        return view('admin.partners.show', compact('partner', 'totalMenus', 'totalOrders'));
    }

    public function edit(CateringPartner $partner)
    {
        $partner->load('user');
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, CateringPartner $partner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $partner->user->id,
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'business_name' => 'required|string|max:255',
            'description' => 'required|string',
            'logo' => 'nullable|image|max:2048',
            'profile_picture' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed'
        ]);
        
        // Update user
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address
        ];
        
        if ($request->hasFile('profile_picture')) {
            if ($partner->user->profile_picture) {
                Storage::disk('public')->delete($partner->user->profile_picture);
            }
            $userData['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        $partner->user->update($userData);
        
        // Update partner
        $partnerData = [
            'business_name' => $request->business_name,
            'description' => $request->description
        ];
        
        if ($request->hasFile('logo')) {
            if ($partner->logo) {
                Storage::disk('public')->delete($partner->logo);
            }
            $partnerData['logo'] = $request->file('logo')->store('partner_logos', 'public');
        }
        
        $partner->update($partnerData);
        
        return redirect()->route('admin.partners.index')->with('success', 'Mitra catering berhasil diperbarui.');
    }

    public function destroy(CateringPartner $partner)
    {
        // Check if partner has related records
        if ($partner->menus()->count() > 0) {
            return redirect()->route('admin.partners.index')
                ->with('error', 'Mitra catering tidak dapat dihapus karena memiliki menu terkait.');
        }
        
        // Delete partner logo
        if ($partner->logo) {
            Storage::disk('public')->delete($partner->logo);
        }
        
        // Delete user profile picture
        if ($partner->user->profile_picture) {
            Storage::disk('public')->delete($partner->user->profile_picture);
        }
        
        // Delete user and partner (cascade)
        $partner->user->delete();
        
        return redirect()->route('admin.partners.index')->with('success', 'Mitra catering berhasil dihapus.');
    }

    public function toggleStatus(Request $request, $id)
    {
        $partner = CateringPartner::findOrFail($id);
        $partner->is_active = !$partner->is_active;
        $partner->save();
        
        $status = $partner->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->route('admin.partners.index')
            ->with('success', "Mitra catering berhasil {$status}.");
    }
}