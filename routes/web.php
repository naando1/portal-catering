<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PartnerController as AdminPartnerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\MenuController as AdminMenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Partner\DashboardController as PartnerDashboardController;
use App\Http\Controllers\Partner\MenuController as PartnerMenuController;
use App\Http\Controllers\Partner\OrderController as PartnerOrderController;
use App\Http\Controllers\Partner\ReportController as PartnerReportController;
use App\Http\Controllers\Partner\ProfileController as PartnerProfileController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\ReviewController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menus', [MenuController::class, 'index'])->name('menus.index');
Route::get('/menus/{menu}', [MenuController::class, 'show'])->name('menus.show');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/menu-diet', [App\Http\Controllers\MenuController::class, 'dietMenu'])
    ->name('menus.diet');
Route::get('/menu-diet/{id}', [App\Http\Controllers\MenuController::class, 'showDietMenu'])
    ->name('menus.diet.show');

// Authentication routes (provided by Laravel Breeze)
require __DIR__.'/auth.php';

// Customer Routes (authenticated users with customer role)
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/profile', [CustomerProfileController::class, 'edit'])->name('customer.profile.edit');
    Route::put('/profile', [CustomerProfileController::class, 'update'])->name('customer.profile.update');

    // Tambahkan route untuk update password dan health profile
    Route::put('/profile/password', [App\Http\Controllers\Customer\ProfileController::class, 'updatePassword'])
        ->name('customer.password.update');
    Route::post('/profile/health', [App\Http\Controllers\Customer\ProfileController::class, 'updateHealthProfile'])
        ->name('customer.health-profile.update');
    
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
     Route::get('/menus/diet', [App\Http\Controllers\Partner\MenuController::class, 'dietMenus'])
     ->name('menus.diet');
 
    // Route untuk edit nutrisi menu
    Route::get('/menus/{menu}/nutrition', [App\Http\Controllers\Partner\MenuController::class, 'editNutrition'])
        ->name('menus.edit-nutrition');
    Route::put('/menus/{menu}/nutrition', [App\Http\Controllers\Partner\MenuController::class, 'updateNutrition'])
        ->name('menus.update-nutrition');
});

// Admin Routes (authenticated users with admin role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Users
    Route::resource('users', AdminUserController::class);
    
    // Partners
    Route::resource('partners', AdminPartnerController::class);
    Route::put('/partners/{id}/toggle-status', [AdminPartnerController::class, 'toggleStatus'])->name('partners.toggle-status');
    
    // Categories
    Route::resource('categories', AdminCategoryController::class);
    
    // Menus
    Route::get('/menus', [AdminMenuController::class, 'index'])->name('menus.index');
    Route::get('/menus/{menu}', [AdminMenuController::class, 'show'])->name('menus.show');
    
    // Orders
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    
    // Reports
    Route::get('/reports/monthly', [AdminReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('/reports/monthly/export', [AdminReportController::class, 'export'])->name('reports.monthly.export');
    
    // Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

});