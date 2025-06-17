<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Category;
use App\Models\CateringPartner;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $query = Menu::with(['category', 'cateringPartner']);
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        // Filter by partner
        if ($request->has('partner') && $request->partner) {
            $query->where('catering_partner_id', $request->partner);
        }
        
        // Filter by availability
        if ($request->has('availability')) {
            if ($request->availability === 'available') {
                $query->where('is_available', true);
            } elseif ($request->availability === 'unavailable') {
                $query->where('is_available', false);
            }
        }
        
        // Search by name
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $menus = $query->orderBy('name')->paginate(10);
        $categories = Category::all();
        $partners = CateringPartner::where('is_active', true)->get();
        
        return view('admin.menus.index', compact('menus', 'categories', 'partners'));
    }

    public function show(Menu $menu)
    {
        $menu->load(['category', 'cateringPartner', 'reviews.user']);
        return view('admin.menus.show', compact('menu'));
    }
}