<?php
// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\CateringPartner;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::take(4)->get();
        $featuredMenus = Menu::with(['cateringPartner', 'category'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        $partners = CateringPartner::where('is_active', true)->take(4)->get();

        $siteName = Setting::where('key', 'site_name')->first()?->value ?? 'Portal Catering';
        $siteDescription = Setting::where('key', 'site_description')->first()?->value ?? 'Portal Catering';

        return view('home', compact('categories', 'featuredMenus', 'partners', 'siteName', 'siteDescription'));
    }
}