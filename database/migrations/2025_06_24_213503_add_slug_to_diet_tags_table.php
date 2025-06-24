<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\DietTag;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Periksa apakah kolom slug sudah ada
        if (!Schema::hasColumn('diet_tags', 'slug')) {
            // Tambahkan kolom hanya jika belum ada
            Schema::table('diet_tags', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // Update existing records with slugs based on names
        $dietTags = DietTag::all();
        foreach ($dietTags as $tag) {
            // Pastikan tidak ada slug kosong
            if (empty($tag->name)) {
                $tag->name = 'Diet Tag ' . $tag->id;
            }
            $tag->slug = Str::slug($tag->name);
            $tag->save();
        }

        // Pastikan tidak ada slug kosong atau duplikat sebelum menambahkan constraint unique
        $emptySlugTags = DietTag::whereNull('slug')->orWhere('slug', '')->get();
        foreach ($emptySlugTags as $tag) {
            $tag->slug = 'diet-tag-' . $tag->id;
            $tag->save();
        }

        // Cek dan perbaiki slug duplikat
        $slugCounts = DietTag::select('slug')
            ->groupBy('slug')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('slug');

        foreach ($slugCounts as $duplicateSlug) {
            $duplicateTags = DietTag::where('slug', $duplicateSlug)->get();
            $counter = 1;
            foreach ($duplicateTags as $index => $tag) {
                if ($index > 0) { // Skip yang pertama
                    $tag->slug = $tag->slug . '-' . $counter;
                    $tag->save();
                    $counter++;
                }
            }
        }

        // Tambahkan unique constraint
        Schema::table('diet_tags', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diet_tags', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
