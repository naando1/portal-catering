<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_description' => 'nullable|string',
            'about_content' => 'nullable|string',
            'contact_info' => 'nullable|string',
            'site_logo' => 'nullable|image|max:2048',
            'site_favicon' => 'nullable|image|max:2048',
            'footer_text' => 'nullable|string',
        ]);
        
        // Update text settings
        $textSettings = [
            'site_name',
            'site_description',
            'about_content',
            'contact_info',
            'footer_text'
        ];
        
        foreach ($textSettings as $key) {
            if ($request->has($key)) {
                Setting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }
        
        // Update site logo if uploaded
        if ($request->hasFile('site_logo')) {
            $logoSetting = Setting::where('key', 'site_logo')->first();
            
            if ($logoSetting && $logoSetting->value) {
                Storage::disk('public')->delete($logoSetting->value);
            }
            
            $logoPath = $request->file('site_logo')->store('settings', 'public');
            
            Setting::updateOrCreate(
                ['key' => 'site_logo'],
                ['value' => $logoPath]
            );
        }
        
        // Update site favicon if uploaded
        if ($request->hasFile('site_favicon')) {
            $faviconSetting = Setting::where('key', 'site_favicon')->first();
            
            if ($faviconSetting && $faviconSetting->value) {
                Storage::disk('public')->delete($faviconSetting->value);
            }
            
            $faviconPath = $request->file('site_favicon')->store('settings', 'public');
            
            Setting::updateOrCreate(
                ['key' => 'site_favicon'],
                ['value' => $faviconPath]
            );
        }
        
        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil diperbarui.');
    }
}