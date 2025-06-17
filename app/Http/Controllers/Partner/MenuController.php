<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DietTag;
use App\Models\Menu;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $partner = auth()->user()->cateringPartner;
        $menus = Menu::with('category')
            ->where('catering_partner_id', $partner->id)
            ->orderBy('name')
            ->paginate(10);

        return view('partner.menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::all();
        $dietTags = DietTag::all();
        return view('partner.menus.create', compact('categories', 'dietTags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|max:2048',
            'calories' => 'nullable|numeric|min:0',
            'carbohydrates' => 'nullable|numeric|min:0',
            'proteins' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            'sugars' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'is_diet_menu' => 'boolean',
            'diet_tags' => 'nullable|array',
            'diet_tags.*' => 'exists:diet_tags,id',
            'is_available' => 'boolean'
        ]);

        $partner = auth()->user()->cateringPartner;
        
        $imagePath = $request->file('image')->store('menu_images', 'public');
        
        Menu::create([
            'catering_partner_id' => $partner->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'is_available' => $request->has('is_available'),
            'calories' => $request->calories,
        'carbohydrates' => $request->carbohydrates,
        'proteins' => $request->proteins,
        'fats' => $request->fats,
        'sugars' => $request->sugars,
        'sodium' => $request->sodium,
        'is_diet_menu' => $request->has('is_diet_menu'),
        ]);

        // Sync diet tags jika ada
    if ($request->has('diet_tags')) {
        $menu->dietTags()->sync($request->diet_tags);
    }

        return redirect()->route('partner.menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function show(Menu $menu)
    {
        $partner = auth()->user()->cateringPartner;
        
        if ($menu->catering_partner_id !== $partner->id) {
            abort(403);
        }
        
        $menu->load(['category', 'reviews.user']);
        
        return view('partner.menus.show', compact('menu'));
    }

    public function edit(Menu $menu)
    {
        $partner = auth()->user()->cateringPartner;
        $this->authorize('update', $menu);
        
        if ($menu->catering_partner_id !== $partner->id) {
            abort(403);
        }
        
        $categories = Category::all();
        $dietTags = DietTag::all();
        $menuDietTags = $menu->dietTags->pluck('id')->toArray();
        
        return view('partner.menus.edit', compact('menu', 'categories', 'dietTags', 'menuDietTags'));
    }

    public function update(Request $request, Menu $menu)
    {
        $partner = auth()->user()->cateringPartner;
        $this->authorize('update', $menu);
        
        if ($menu->catering_partner_id !== $partner->id) {
            abort(403);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'calories' => 'nullable|numeric|min:0',
            'carbohydrates' => 'nullable|numeric|min:0',
            'proteins' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            'sugars' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'is_diet_menu' => 'boolean',
            'diet_tags' => 'nullable|array',
            'diet_tags.*' => 'exists:diet_tags,id',
            'is_available' => 'boolean'
        ]);

        $data = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'calories' => $request->calories,
            'carbohydrates' => $request->carbohydrates,
            'proteins' => $request->proteins,
            'fats' => $request->fats,
            'sugars' => $request->sugars,
            'sodium' => $request->sodium,
            'is_diet_menu' => $request->has('is_diet_menu'),
            'is_available' => $request->has('is_available')
        ];

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menu_images', 'public');
        }

        $menu->update($data);

         // Sync diet tags jika ada
    if ($request->has('diet_tags')) {
        $menu->dietTags()->sync($request->diet_tags);
    } else {
        $menu->dietTags()->detach();
    }

        return redirect()->route('partner.menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    /**
 * Menampilkan daftar menu diet partner
 */
    public function dietMenus()
    {
        $partner = Auth::user()->cateringPartner;

        $menus = Menu::where('catering_partner_id', $partner->id)
                     ->where('is_diet_menu', true)
                     ->with(['category', 'dietTags'])
                     ->orderBy('created_at', 'desc')
                     ->paginate(10);

        return view('partner.menus.diet', compact('menus'));
    }  
    
    /**
 * Menampilkan form untuk edit informasi nutrisi menu
 */
public function editNutrition(Menu $menu)
{
    $this->authorize('update', $menu);
    
    $dietTags = DietTag::all();
    $menuDietTags = $menu->dietTags->pluck('id')->toArray();
    
    return view('partner.menus.edit-nutrition', compact('menu', 'dietTags', 'menuDietTags'));
}

/**
 * Memperbarui informasi nutrisi menu
 */
public function updateNutrition(Request $request, Menu $menu)
{
    $this->authorize('update', $menu);
    
    // Validasi input
    $request->validate([
        'calories' => 'nullable|numeric|min:0',
        'carbohydrates' => 'nullable|numeric|min:0',
        'proteins' => 'nullable|numeric|min:0',
        'fats' => 'nullable|numeric|min:0',
        'sugars' => 'nullable|numeric|min:0',
        'sodium' => 'nullable|numeric|min:0',
        'is_diet_menu' => 'boolean',
        'diet_tags' => 'nullable|array',
        'diet_tags.*' => 'exists:diet_tags,id',
    ]);
    
    // Update data nutrisi menu
    $menu->update([
        'calories' => $request->calories,
        'carbohydrates' => $request->carbohydrates,
        'proteins' => $request->proteins,
        'fats' => $request->fats,
        'sugars' => $request->sugars,
        'sodium' => $request->sodium,
        'is_diet_menu' => $request->has('is_diet_menu'),
    ]);
    
    // Sync diet tags
    if ($request->has('diet_tags')) {
        $menu->dietTags()->sync($request->diet_tags);
    } else {
        $menu->dietTags()->detach();
    }
    
    return redirect()->route('partner.menus.edit-nutrition', $menu->id)
        ->with('success', 'Informasi nutrisi menu berhasil diperbarui.');
}

    public function destroy(Menu $menu)
    {
        $partner = auth()->user()->cateringPartner;
        
        if ($menu->catering_partner_id !== $partner->id) {
            abort(403);
        }
        
        // Check if menu has related orders
        if ($menu->orderItems()->count() > 0) {
            return redirect()->route('partner.menus.index')->with('error', 'Menu tidak dapat dihapus karena terkait dengan pesanan.');
        }
        
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }
        
        $menu->delete();

        return redirect()->route('partner.menus.index')->with('success', 'Menu berhasil dihapus.');
    }
}