<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Menu $menu)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);

        // Check if user already reviewed this menu
        $existingReview = Review::where('user_id', auth()->id())
            ->where('menu_id', $menu->id)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
        } else {
            Review::create([
                'user_id' => auth()->id(),
                'menu_id' => $menu->id,
                'rating' => $request->rating,
                'comment' => $request->comment
            ]);
        }

        return redirect()->back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}