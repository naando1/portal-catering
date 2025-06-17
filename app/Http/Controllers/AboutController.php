<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $aboutContent = Setting::where('key', 'about_content')->first()?->value;
        $contactInfo = Setting::where('key', 'contact_info')->first()?->value;

        return view('about', compact('aboutContent', 'contactInfo'));
    }
}