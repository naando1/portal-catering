<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update tabel menus dengan field baru
        Schema::table('menus', function (Blueprint $table) {
            // Field yang kurang dari requirement
            $table->decimal('fiber', 8, 2)->nullable()->after('sodium');
            $table->enum('carbohydrate_type', ['kompleks', 'olahan'])->nullable()->after('carbohydrates');
            $table->string('cooking_method')->nullable()->after('fiber'); // goreng, rebus, kukus, bakar, panggang
            $table->json('ingredient_tags')->nullable()->after('cooking_method'); // jeroan, daging, telur, susu, udang, kacang
            $table->json('taste_tags')->nullable()->after('ingredient_tags'); // pedas, gurih, manis, asin
        });

        // 2. Update tabel health_profiles dengan field ambeien
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->boolean('has_hemorrhoids')->default(false)->after('has_cholesterol'); // ambeien
        });

        // 3. Update tabel users dengan field aktivitas fisik
        Schema::table('users', function (Blueprint $table) {
            $table->enum('activity_level', ['sedentari', 'ringan', 'sedang', 'berat', 'sangat_berat'])->nullable()->after('weight');
        });

        // 4. Update tabel diet_preferences dengan tujuan diet dan field baru
        Schema::table('diet_preferences', function (Blueprint $table) {
            $table->enum('diet_goal', ['turun_bb', 'naik_bb', 'jaga_bb'])->nullable()->after('diet_type');
            $table->integer('deficit_surplus_percentage')->nullable()->after('daily_calorie_target'); // 10-20%
            $table->json('food_allergies')->nullable()->after('dairy_free'); // udang, telur, kacang, dll
            $table->json('taste_preferences')->nullable()->after('food_allergies'); // pedas, manis, gurih, asin
            $table->json('cooking_method_preferences')->nullable()->after('taste_preferences'); // rebus, goreng, bakar, kukus, panggang
        });

        // 5. Tabel baru untuk user body metrics (BMR, TDEE, dll)
        Schema::create('user_body_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('bmi', 5, 2)->nullable();
            $table->integer('bmr')->nullable(); // Basal Metabolic Rate
            $table->integer('tdee')->nullable(); // Total Daily Energy Expenditure
            $table->integer('target_calories')->nullable(); // Target kalori sesuai tujuan diet
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_body_metrics');
        
        Schema::table('diet_preferences', function (Blueprint $table) {
            $table->dropColumn([
                'diet_goal', 'deficit_surplus_percentage', 'food_allergies', 
                'taste_preferences', 'cooking_method_preferences'
            ]);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('activity_level');
        });
        
        Schema::table('health_profiles', function (Blueprint $table) {
            $table->dropColumn('has_hemorrhoids');
        });
        
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn([
                'fiber', 'carbohydrate_type', 'cooking_method', 
                'ingredient_tags', 'taste_tags'
            ]);
        });
    }
};