<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\Partner\MenuController as PartnerMenuController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Partner\OrderController as PartnerOrderController;
use App\Http\Controllers\Partner\ReportController as PartnerReportController;
use App\Http\Controllers\Partner\ProfileController as PartnerProfileController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Menu Routes (Public)
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');

// Diet Menu Routes (Public)
Route::get('/menu-diet', [MenuController::class, 'dietMenu'])->name('menus.diet');
Route::get('/menu-diet/{id}', [MenuController::class, 'showDietMenu'])->name('menus.diet.show');

// Authentication Routes
require __DIR__.'/auth.php';

// Customer Routes (authenticated users with customer role)
Route::middleware(['auth', 'role:customer'])->group(function () {
    // Profile Routes
    Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('customer.profile.update');
    Route::put('/profile/password', [CustomerProfileController::class, 'updatePassword'])->name('customer.password.update');
    
    // Health Profile Routes
    Route::post('/profile/health', [CustomerProfileController::class, 'updateHealthProfile'])->name('customer.health-profile.update');
    
    // Cart & Checkout
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{menu}', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    
    // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('customer.orders.index');
    Route::get('/orders/{order}', [CustomerOrderController::class, 'show'])->name('customer.orders.show');
    Route::post('/orders/{order}/payment', [CustomerOrderController::class, 'uploadPayment'])->name('customer.orders.payment');
    
    // Reviews
    Route::post('/reviews/{menu}', [ReviewController::class, 'store'])->name('reviews.store');
});

// Partner Routes (authenticated users with partner role)
Route::middleware(['auth', 'role:partner'])->prefix('partner')->name('partner.')->group(function () {
    Route::get('/dashboard', [PartnerDashboardController::class, 'index'])->name('dashboard');
    
    // Menus
    Route::resource('menus', PartnerMenuController::class);
    
    // Orders
    Route::get('/orders', [PartnerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [PartnerOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [PartnerOrderController::class, 'updateStatus'])->name('orders.status.update');
    
    // Reports
    Route::get('/reports/monthly', [PartnerReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/monthly/export', [PartnerReportController::class, 'export'])->name('reports.monthly.export');
    
    // Profile
    Route::get('/profile', [PartnerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [PartnerProfileController::class, 'update'])->name('profile.update');

    // Routes untuk pengelolaan menu diet partner
    Route::get('/menus/diet', [PartnerMenuController::class, 'dietMenus'])->name('menus.diet');

    // Route untuk edit nutrisi menu
    Route::get('/menus/{menu}/nutrition', [PartnerMenuController::class, 'editNutrition'])->name('menus.edit-nutrition');
    Route::put('/menus/{menu}/nutrition', [PartnerMenuController::class, 'updateNutrition'])->name('menus.update-nutrition');
});

// Admin Routes (authenticated users with admin role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // User Management
    Route::resource('users', AdminUserController::class);
    
    // Partner Management
    Route::resource('partners', AdminPartnerController::class);
    Route::put('/partners/{id}/toggle-status', [AdminPartnerController::class, 'toggleStatus'])->name('partners.toggle-status');
    
    // Category Management
    Route::resource('categories', AdminCategoryController::class);
    
    // Menu Management
    Route::resource('menus', AdminMenuController::class);
    
    // Order Management
    Route::resource('orders', AdminOrderController::class);
    
    // Reports
    Route::get('/reports/monthly', [AdminReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/monthly/export', [AdminReportController::class, 'export'])->name('reports.monthly.export');
    
    // Settings Management
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    
    // Diet Tags Management
    Route::resource('diet-tags', App\Http\Controllers\Admin\DietTagController::class);
    
    // Health Reports
    Route::get('/health-reports', [App\Http\Controllers\Admin\HealthReportController::class, 'index'])->name('health-reports.index');
    Route::get('/health-reports/export', [App\Http\Controllers\Admin\HealthReportController::class, 'export'])->name('health-reports.export');
});

// API Routes untuk AJAX calls
Route::middleware('auth')->prefix('api')->name('api.')->group(function () {
    // Recommendation API
    Route::get('/recommendations/personal', [App\Http\Controllers\Api\RecommendationController::class, 'personal'])->name('recommendations.personal');
    Route::post('/recommendations/feedback', [App\Http\Controllers\Api\RecommendationController::class, 'feedback'])->name('recommendations.feedback');
    
    // Nutrition Calculator API
    Route::post('/nutrition/calculate-bmr', [App\Http\Controllers\Api\NutritionController::class, 'calculateBMR'])->name('nutrition.calculate-bmr');
    Route::post('/nutrition/calculate-tdee', [App\Http\Controllers\Api\NutritionController::class, 'calculateTDEE'])->name('nutrition.calculate-tdee');
    Route::post('/nutrition/calculate-target-calories', [App\Http\Controllers\Api\NutritionController::class, 'calculateTargetCalories'])->name('nutrition.calculate-target-calories');
});

// Webhooks (jika diperlukan untuk payment gateway)
Route::post('/webhooks/payment', [App\Http\Controllers\WebhookController::class, 'payment'])->name('webhooks.payment');

// Route untuk halaman diet dan rekomendasi
Route::middleware(['auth'])->group(function () {
    // Halaman diet
    Route::get('/diet', [App\Http\Controllers\MenuController::class, 'diet'])
        ->name('menus.diet');
    
    // Feedback rekomendasi
    Route::post('/menus/{id}/feedback', [App\Http\Controllers\MenuController::class, 'submitFeedback'])
        ->name('menus.feedback');
});