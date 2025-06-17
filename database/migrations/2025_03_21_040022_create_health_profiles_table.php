<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambahkan kolom terkait umur dan metadata fisik pada tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->after('profile_picture');
            $table->string('gender', 20)->nullable()->after('birthdate');
            $table->decimal('height', 5, 2)->nullable()->after('gender'); // tinggi dalam cm
            $table->decimal('weight', 5, 2)->nullable()->after('height'); // berat dalam kg
        });

        // Membuat tabel health_profiles untuk informasi kesehatan
        Schema::create('health_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('has_diabetes')->default(false);
            $table->boolean('has_hypertension')->default(false);
            $table->boolean('has_heart_disease')->default(false);
            $table->boolean('has_cholesterol')->default(false);
            $table->boolean('has_food_allergies')->default(false);
            $table->text('food_allergies_detail')->nullable();
            $table->text('other_conditions')->nullable();
            $table->timestamps();
        });

        // Menambahkan kolom informasi nutrisi pada tabel menus
        Schema::table('menus', function (Blueprint $table) {
            $table->decimal('calories', 8, 2)->nullable()->after('is_available');
            $table->decimal('carbohydrates', 8, 2)->nullable()->after('calories');
            $table->decimal('proteins', 8, 2)->nullable()->after('carbohydrates');
            $table->decimal('fats', 8, 2)->nullable()->after('proteins');
            $table->decimal('sugars', 8, 2)->nullable()->after('fats');
            $table->decimal('sodium', 8, 2)->nullable()->after('sugars');
            $table->boolean('is_diet_menu')->default(false)->after('sodium');
        });

        // Membuat tabel diet_preferences untuk preferensi diet pengguna
        Schema::create('diet_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('diet_type', [
                'regular', 'low_carb', 'low_fat', 'low_sugar', 
                'low_sodium', 'high_protein', 'vegetarian', 'vegan'
            ])->default('regular');
            $table->integer('daily_calorie_target')->nullable();
            $table->boolean('gluten_free')->default(false);
            $table->boolean('dairy_free')->default(false);
            $table->timestamps();
        });

        // Membuat tabel diet_tags untuk label makanan diet
        Schema::create('diet_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Membuat tabel pivot untuk relasi many-to-many antara menu dan diet_tags
        Schema::create('diet_tag_menu', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->foreignId('diet_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_tag_menu');
        Schema::dropIfExists('diet_tags');
        Schema::dropIfExists('diet_preferences');
        
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn([
                'calories', 'carbohydrates', 'proteins', 
                'fats', 'sugars', 'sodium', 'is_diet_menu'
            ]);
        });
        
        Schema::dropIfExists('health_profiles');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['birthdate', 'gender', 'height', 'weight']);
        });
    }
};