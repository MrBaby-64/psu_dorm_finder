<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\InquiryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [PropertyController::class, 'home'])->name('home');
// Add these lines after the home route
Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/how-it-works', [App\Http\Controllers\PageController::class, 'howItWorks'])->name('how-it-works');
// Browse properties
Route::get('/rentals/browse', [PropertyController::class, 'browse'])->name('properties.browse');

// Property details
Route::get('/properties/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze provides these)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Account routes - moved to tenant routes section below

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Inquiries
    Route::post('/inquiries', [InquiryController::class, 'store'])->name('inquiries.store');

    // Messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.read');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::patch('/bookings/{booking}/approve', [BookingController::class, 'approve'])->name('bookings.approve');
    Route::patch('/bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
    Route::delete('/bookings/{booking}', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Favorites
    Route::get('/favorites', [App\Http\Controllers\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites', [App\Http\Controllers\FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{property}', [App\Http\Controllers\FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::post('/favorites/toggle', [App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::post('/favorites/check', [App\Http\Controllers\FavoriteController::class, 'check'])->name('favorites.check');
});

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('tenant')->name('tenant.')->group(function () {
    // Account and dashboard
    Route::get('/account', [App\Http\Controllers\TenantController::class, 'account'])->name('account');
    
    // Transactions
    Route::get('/transactions', [App\Http\Controllers\TenantController::class, 'transactions'])->name('transactions');
    
    // Favorites - already exists, just add the route name if missing
    
    // Notifications
    Route::get('/notifications', [App\Http\Controllers\TenantController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\TenantController::class, 'notifications'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\TenantController::class, 'markNotificationRead'])->name('notifications.read');
    
    // Scheduled Visits
    Route::get('/scheduled-visits', [App\Http\Controllers\TenantController::class, 'scheduledVisits'])->name('scheduled-visits');
    Route::post('/scheduled-visits', [App\Http\Controllers\ScheduledVisitController::class, 'store'])->name('scheduled-visits.store');
    Route::put('/scheduled-visits/{visit}/cancel', [App\Http\Controllers\TenantController::class, 'cancelVisit'])->name('scheduled-visits.cancel');
    Route::put('/scheduled-visits/{visit}', [App\Http\Controllers\ScheduledVisitController::class, 'update'])->name('scheduled-visits.update');
Route::delete('/scheduled-visits/{visit}', [App\Http\Controllers\ScheduledVisitController::class, 'destroy'])->name('scheduled-visits.destroy');
    
    // Reviews
    Route::get('/reviews', [App\Http\Controllers\TenantController::class, 'reviews'])->name('reviews');
    Route::post('/reviews', [App\Http\Controllers\TenantController::class, 'storeReview'])->name('reviews.store');
});

/*
|--------------------------------------------------------------------------
| Enhanced Landlord Routes
|--------------------------------------------------------------------------
*/

// Removed duplicate landlord routes - these are defined below
/*
|--------------------------------------------------------------------------
| Landlord Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/account', [\App\Http\Controllers\Landlord\AccountController::class, 'index'])->name('account');
    
    // Property management
    Route::get('/properties', [\App\Http\Controllers\Landlord\PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [\App\Http\Controllers\Landlord\PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [\App\Http\Controllers\Landlord\PropertyController::class, 'store'])->name('properties.store');
    Route::post('/properties/remove-temp-image', [\App\Http\Controllers\Landlord\PropertyController::class, 'removeTempImage'])->name('properties.remove-temp-image');
    Route::post('/properties/store-map-position', [\App\Http\Controllers\Landlord\PropertyController::class, 'storeMapPosition'])->name('properties.store-map-position');
    Route::get('/properties/{property}/edit', [\App\Http\Controllers\Landlord\PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property}', [\App\Http\Controllers\Landlord\PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{property}', [\App\Http\Controllers\Landlord\PropertyController::class, 'destroy'])->name('properties.destroy');
    
    // Image management
    Route::post('/properties/{property}/images/upload', [\App\Http\Controllers\Landlord\PropertyImageController::class, 'upload'])->name('properties.images.upload');
    Route::post('/properties/{property}/images/{image}/set-cover', [\App\Http\Controllers\Landlord\PropertyImageController::class, 'setCover'])->name('properties.images.set-cover');
    Route::delete('/properties/{property}/images/{image}', [\App\Http\Controllers\Landlord\PropertyImageController::class, 'delete'])->name('properties.images.delete');
    
    // Inquiries/Messages
    Route::get('/inquiries', [\App\Http\Controllers\Landlord\InquiryController::class, 'index'])->name('inquiries.index');
    
    // Bookings management
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

    Route::post('/scheduled-visits/{visit}/confirm', [App\Http\Controllers\ScheduledVisitController::class, 'confirm'])->name('scheduled-visits.confirm');
    Route::post('/scheduled-visits/{visit}/complete', [App\Http\Controllers\ScheduledVisitController::class, 'markCompleted'])->name('scheduled-visits.complete');
    Route::post('/scheduled-visits/{visit}/cancel-by-landlord', [App\Http\Controllers\ScheduledVisitController::class, 'cancelByLandlord'])->name('scheduled-visits.cancel-by-landlord');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // ADMIN ACCOUNT ROUTE - MUST BE INSIDE THE GROUP
    Route::get('/account', [\App\Http\Controllers\Admin\AccountController::class, 'index'])->name('account');
    
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Property approval
    Route::get('/properties/pending', [\App\Http\Controllers\Admin\PropertyController::class, 'pending'])->name('properties.pending');
    Route::post('/properties/{property}/approve', [\App\Http\Controllers\Admin\PropertyController::class, 'approve'])->name('properties.approve');
    Route::post('/properties/{property}/reject', [\App\Http\Controllers\Admin\PropertyController::class, 'reject'])->name('properties.reject');
    Route::post('/properties/{property}/verify', [\App\Http\Controllers\Admin\PropertyController::class, 'verify'])->name('properties.verify');
    Route::post('/properties/{property}/feature', [\App\Http\Controllers\Admin\PropertyController::class, 'feature'])->name('properties.feature');
    
    // User management
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('users.role');
    Route::post('/users/{user}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verify'])->name('users.verify');
    
    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';