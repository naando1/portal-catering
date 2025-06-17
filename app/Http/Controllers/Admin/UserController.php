<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role');
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role_id', $request->role);
        }
        
        // Search by name or email
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        
        $users = $query->orderBy('name')->paginate(10);
        $roles = Role::all();
        
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048'
        ]);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'phone_number' => $request->phone_number,
            'address' => $request->address
        ];
        
        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        
        User::create($data);
        
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function show(User $user)
    {
        $user->load('role');
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'profile_picture' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed'
        ]);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'phone_number' => $request->phone_number,
            'address' => $request->address
        ];
        
        if ($request->hasFile('profile_picture')) {
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            $data['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);
        
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Check if user is an admin
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')->with('error', 'Admin tidak dapat dihapus.');
        }
        
        // Check if user has related records
        if ($user->orders()->count() > 0) {
            return redirect()->route('admin.users.index')->with('error', 'Pengguna tidak dapat dihapus karena memiliki pesanan terkait.');
        }
        
        if ($user->profile_picture) {
            Storage::disk('public')->delete($user->profile_picture);
        }
        
        // If user is a partner, handle catering partner data
        if ($user->isPartner() && $user->cateringPartner) {
            $partner = $user->cateringPartner;
            
            if ($partner->logo) {
                Storage::disk('public')->delete($partner->logo);
            }
            
            $partner->delete();
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}