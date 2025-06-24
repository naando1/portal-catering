<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DietTag;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Menampilkan daftar menu
     */
    public function index(Request $request)
    {
        $partner = Auth::user()->cateringPartner;
        
        // Tambahkan pencarian jika ada
        $query = Menu::where('catering_partner_id', $partner->id);
        
        // Jika ada parameter pencarian
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }
        
        // Gunakan paginate() alih-alih get()
        $menus = $query->paginate(10); // Jumlah item per halaman (bisa disesuaikan)
        
        return view('partner.menus.index', compact('menus'));
    }

    /**
     * Menampilkan form tambah menu
     */
    public function create()
    {
        $categories = Category::all();
        $dietTags = DietTag::all();
        
        // Tambahkan definisi $carbohydrateTypes
        $carbohydrateTypes = [
            'kompleks' => 'Karbohidrat Kompleks',
            'olahan' => 'Karbohidrat Olahan'
        ];
        
        // Tambahkan juga definisi untuk variabel lain yang digunakan di view
        $cookingMethods = [
            'rebus' => 'Rebus',
            'goreng' => 'Goreng',
            'bakar' => 'Bakar',
            'kukus' => 'Kukus',
            'panggang' => 'Panggang',
            'tumis' => 'Tumis'
        ];
        
        // Definisikan juga variabel lain yang mungkin digunakan
        $ingredientOptions = [
            'gluten_free' => 'Bebas Gluten',
            'dairy_free' => 'Bebas Susu',
            'nut_free' => 'Bebas Kacang',
            'vegetarian' => 'Vegetarian',
            'vegan' => 'Vegan',
            // Tambahkan opsi lainnya sesuai kebutuhan
        ];
        
        $tasteOptions = [
            'manis' => 'Manis',
            'asin' => 'Asin',
            'asam' => 'Asam',
            'pedas' => 'Pedas',
            // Tambahkan opsi lainnya sesuai kebutuhan
        ];
        
        return view('partner.menus.create', compact(
            'categories', 
            'dietTags', 
            'carbohydrateTypes',
            'cookingMethods',
            'ingredientOptions',
            'tasteOptions'
        ));
    }

    /**
     * Menyimpan menu baru
     */
    public function store(Request $request)
    {
        $partner = Auth::user()->cateringPartner;
        
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'calories' => 'required|integer|min:0',
            'proteins' => 'required|numeric|min:0',
            'carbs' => 'required|numeric|min:0',
            'fats' => 'required|numeric|min:0',
            'sugars' => 'required|numeric|min:0',
            'sodium' => 'required|integer|min:0',
            'fiber' => 'required|numeric|min:0',
            'cooking_method' => 'required|string',
            'carbohydrate_type' => 'required|string',
            'ingredient_tags' => 'nullable|array',
            'taste_tags' => 'nullable|array',
        ]);
        
        // Upload gambar
        $imagePath = $request->file('image')->store('menus', 'public');
        
        // Simpan menu
        $menu = new Menu([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
            'category_id' => $request->category_id,
            'catering_partner_id' => $partner->id,
            'calories' => $request->calories,
            'proteins' => $request->proteins,
            'carbohydrates' => $request->carbs,
            'fats' => $request->fats,
            'sugars' => $request->sugars,
            'sodium' => $request->sodium,
            'fiber' => $request->fiber,
            'cooking_method' => $request->cooking_method,
            'carbohydrate_type' => $request->carbohydrate_type,
            'ingredient_tags' => json_encode($request->ingredient_tags ?? []),
            'taste_tags' => json_encode($request->taste_tags ?? []),
            'is_available' => $request->has('is_available'),
            'is_diet_menu' => $request->has('is_diet_menu'),
        ]);
        
        $menu->save();
        
        // Assign diet tags berdasarkan kandungan nutrisi
        $this->assignDietTags($menu);
        
        return redirect()->route('partner.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit menu
     */
    public function edit(Menu $menu)
    {
        $partner = Auth::user()->cateringPartner;
        
        // Pastikan menu milik partner ini
        if ($menu->catering_partner_id != $partner->id) {
            return redirect()->route('partner.menus.index')
                ->with('error', 'Anda tidak memiliki akses ke menu ini.');
        }
        
        $categories = Category::all();
        $dietTags = DietTag::all();
        $menuDietTags = $menu->dietTags->pluck('id')->toArray();
        
        return view('partner.menus.edit', compact('menu', 'categories', 'dietTags', 'menuDietTags'));
    }

    /**
     * Memperbarui menu
     */
    public function update(Request $request, Menu $menu)
    {
        $partner = Auth::user()->cateringPartner;
        
        // Pastikan menu milik partner ini
        if ($menu->catering_partner_id != $partner->id) {
            return redirect()->route('partner.menus.index')
                ->with('error', 'Anda tidak memiliki akses ke menu ini.');
        }
        
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'category_id' => 'required|exists:categories,id',
            'calories' => 'required|integer|min:0',
            'proteins' => 'required|numeric|min:0',
            'carbs' => 'required|numeric|min:0',
            'fats' => 'required|numeric|min:0',
            'sugars' => 'required|numeric|min:0',
            'sodium' => 'required|integer|min:0',
            'fiber' => 'required|numeric|min:0',
            'cooking_method' => 'required|string',
            'carbohydrate_type' => 'required|string',
            'ingredient_tags' => 'nullable|array',
            'taste_tags' => 'nullable|array',
        ]);
        
        // Update data menu
        $menu->name = $request->name;
        $menu->description = $request->description;
        $menu->price = $request->price;
        $menu->category_id = $request->category_id;
        $menu->calories = $request->calories;
        $menu->proteins = $request->proteins;
        $menu->carbohydrates = $request->carbs;
        $menu->fats = $request->fats;
        $menu->sugars = $request->sugars;
        $menu->sodium = $request->sodium;
        $menu->fiber = $request->fiber;
        $menu->cooking_method = $request->cooking_method;
        $menu->carbohydrate_type = $request->carbohydrate_type;
        $menu->ingredient_tags = json_encode($request->ingredient_tags ?? []);
        $menu->taste_tags = json_encode($request->taste_tags ?? []);
        $menu->is_available = $request->has('is_available');
        $menu->is_diet_menu = $request->has('is_diet_menu');
        
        // Upload gambar baru jika ada
        if ($request->hasFile('image')) {
            // Hapus gambar lama
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            
            // Simpan gambar baru
            $imagePath = $request->file('image')->store('menus', 'public');
            $menu->image = $imagePath;
        }
        
        $menu->save();
        
        // Assign diet tags berdasarkan kandungan nutrisi
        $this->assignDietTags($menu);
        
        return redirect()->route('partner.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    /**
     * Menghapus menu
     */
    public function destroy(Menu $menu)
    {
        $partner = Auth::user()->cateringPartner;
        
        // Pastikan menu milik partner ini
        if ($menu->catering_partner_id != $partner->id) {
            return redirect()->route('partner.menus.index')
                ->with('error', 'Anda tidak memiliki akses ke menu ini.');
        }
        
        // Hapus gambar
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }
        
        $menu->delete();
        
        return redirect()->route('partner.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    /**
     * Menampilkan daftar menu diet partner
     */
    public function dietMenus()
    {
        $partner = auth()->user()->cateringPartner;

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
        $partner = auth()->user()->cateringPartner;
        
        if ($menu->catering_partner_id !== $partner->id) {
            abort(403);
        }
        
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
        $partner = auth()->user()->cateringPartner;
        
        if ($menu->catering_partner_id !== $partner->id) {
            abort(403);
        }
        
        $this->authorize('update', $menu);
        
        // Validasi input
        $request->validate([
            'calories' => 'nullable|numeric|min:0',
            'carbohydrates' => 'nullable|numeric|min:0',
            'proteins' => 'nullable|numeric|min:0',
            'fats' => 'nullable|numeric|min:0',
            'sugars' => 'nullable|numeric|min:0',
            'sodium' => 'nullable|numeric|min:0',
            'fiber' => 'nullable|numeric|min:0',
            
            // New fields untuk nutrition
            'carbohydrate_type' => 'nullable|in:kompleks,olahan',
            'cooking_method' => 'nullable|in:rebus,goreng,bakar,kukus,panggang,tumis',
            'ingredient_tags' => 'nullable|array',
            'taste_tags' => 'nullable|array',
            
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
            'fiber' => $request->fiber,
            
            // New fields
            'carbohydrate_type' => $request->carbohydrate_type,
            'cooking_method' => $request->cooking_method,
            'ingredient_tags' => json_encode($request->ingredient_tags ?? []),
            'taste_tags' => json_encode($request->taste_tags ?? []),
            
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

    /**
     * Otomatisasi penandaan diet tag berdasarkan kandungan nutrisi
     */
    private function assignDietTags($menu)
    {
        // Reset diet tags
        $menu->dietTags()->detach();
        
        $tagsToAssign = [];
        
        // Rendah Gula
        if ($menu->sugars <= 10) {
            $tagsToAssign[] = 'rendah-gula';
        }
        
        // Rendah Sodium
        if ($menu->sodium <= 600) {
            $tagsToAssign[] = 'rendah-sodium';
        }
        
        // Rendah Lemak
        if ($menu->fats <= 10) {
            $tagsToAssign[] = 'rendah-lemak';
        }
        
        // Tinggi Serat
        if ($menu->fiber >= 3) {
            $tagsToAssign[] = 'tinggi-serat';
        }
        
        // Karbohidrat Kompleks
        if ($menu->carbohydrate_type == 'kompleks') {
            $tagsToAssign[] = 'karbo-kompleks';
        }
        
        // Diabetes-Friendly
        if ($menu->sugars <= 10 && $menu->carbohydrate_type == 'kompleks' && $menu->fats <= 10) {
            $tagsToAssign[] = 'diabetes-friendly';
        }
        
        // Hipertensi-Friendly
        if ($menu->sodium <= 600 && !in_array('saus_asin', json_decode($menu->taste_tags ?? '[]', true))) {
            $tagsToAssign[] = 'hipertensi-friendly';
        }
        
        // Jantung-Friendly
        if ($menu->fats <= 10 && $menu->sodium <= 600 && $menu->sugars <= 10 && 
            !in_array('jeroan', json_decode($menu->ingredient_tags ?? '[]', true)) &&
            !in_array('kuning_telur', json_decode($menu->ingredient_tags ?? '[]', true))) {
            $tagsToAssign[] = 'jantung-friendly';
        }
        
        // Kolesterol-Friendly
        if ($menu->fats <= 10 && $menu->cooking_method != 'goreng' && 
            !in_array('jeroan', json_decode($menu->ingredient_tags ?? '[]', true)) &&
            !in_array('kulit_ayam', json_decode($menu->ingredient_tags ?? '[]', true))) {
            $tagsToAssign[] = 'kolesterol-friendly';
        }
        
        // Ambeien-Friendly
        if ($menu->fiber >= 3 && 
            !in_array('pedas', json_decode($menu->taste_tags ?? '[]', true)) &&
            $menu->cooking_method != 'goreng') {
            $tagsToAssign[] = 'ambeien-friendly';
        }
        
        // Assign tags
        $dietTags = \App\Models\DietTag::whereIn('slug', $tagsToAssign)->get();
        $menu->dietTags()->attach($dietTags->pluck('id')->toArray());
    }
}