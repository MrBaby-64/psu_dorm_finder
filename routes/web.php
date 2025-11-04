<?php

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

Route::get('/', [PropertyController::class, 'home'])->name('home');

Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');
Route::get('/how-it-works', [App\Http\Controllers\PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/safety-guidelines', [App\Http\Controllers\PageController::class, 'safetyGuidelines'])->name('safety-guidelines');

Route::get('/rentals/browse', [PropertyController::class, 'browse'])->name('properties.browse');

// API endpoints for property search and navigation
Route::get('/api/properties/search-suggestions', [PropertyController::class, 'searchSuggestions'])->name('properties.search-suggestions');
Route::get('/api/routing/get-routes', [\App\Http\Controllers\RoutingController::class, 'getRoutes'])->name('api.routing.get-routes');

Route::get('/properties/{property:slug}', [PropertyController::class, 'show'])->name('properties.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Breeze provides these)
|--------------------------------------------------------------------------
*/

// Google OAuth authentication
Route::get('auth/google', [App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


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
    Route::get('/messages/conversation/{userId}/{propertyId}', [MessageController::class, 'conversation'])->name('messages.conversation');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::post('/messages/direct', [MessageController::class, 'sendDirectMessage'])->name('messages.direct');
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

    // Room management routes
    Route::get('/rooms/{room}/data', [App\Http\Controllers\RoomController::class, 'getData'])->name('rooms.data');
    Route::put('/rooms/{room}/update', [App\Http\Controllers\RoomController::class, 'update'])->name('rooms.update');
});

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/account', [App\Http\Controllers\TenantController::class, 'account'])->name('account');
    
    // Notifications
    Route::get('/notifications', [App\Http\Controllers\TenantController::class, 'notifications'])->name('notifications');
    Route::get('/notifications/mark-all-read-test', [App\Http\Controllers\TenantController::class, 'markAllNotificationsReadTest'])->name('notifications.mark-all-read-test');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\TenantController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\TenantController::class, 'markNotificationRead'])->name('notifications.read');
    
    // Scheduled Visits
    Route::get('/scheduled-visits', [App\Http\Controllers\TenantController::class, 'scheduledVisits'])->name('scheduled-visits');
    Route::post('/scheduled-visits', [App\Http\Controllers\ScheduledVisitController::class, 'store'])->name('scheduled-visits.store');
    Route::put('/scheduled-visits/{visit}/cancel', [App\Http\Controllers\TenantController::class, 'cancelVisit'])->name('scheduled-visits.cancel');
    Route::put('/scheduled-visits/{visit}', [App\Http\Controllers\ScheduledVisitController::class, 'update'])->name('scheduled-visits.update');
Route::delete('/scheduled-visits/{visit}', [App\Http\Controllers\ScheduledVisitController::class, 'destroy'])->name('scheduled-visits.destroy');
    
    // Reviews (view only - creation handled by standalone ReviewController)
    Route::get('/reviews', [App\Http\Controllers\TenantController::class, 'reviews'])->name('reviews');
});

/*
|--------------------------------------------------------------------------
| Landlord Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/account', [\App\Http\Controllers\Landlord\AccountController::class, 'index'])->name('account');

    // Property Management Routes
    Route::get('/properties', [\App\Http\Controllers\Landlord\PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/create', [\App\Http\Controllers\Landlord\PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [\App\Http\Controllers\Landlord\PropertyController::class, 'store'])->name('properties.store');
    Route::post('/properties/remove-temp-image', [\App\Http\Controllers\Landlord\PropertyController::class, 'removeTempImage'])->name('properties.remove-temp-image');
    Route::post('/properties/store-map-position', [\App\Http\Controllers\Landlord\PropertyController::class, 'storeMapPosition'])->name('properties.store-map-position');
    Route::get('/properties/{property}/edit', [\App\Http\Controllers\Landlord\PropertyController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{property}', [\App\Http\Controllers\Landlord\PropertyController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{property}', [\App\Http\Controllers\Landlord\PropertyController::class, 'destroy'])->name('properties.destroy');
    Route::post('/properties/request-deletion', [\App\Http\Controllers\Landlord\PropertyController::class, 'requestDeletion'])->name('properties.request-deletion');
    Route::post('/contact-admin', [\App\Http\Controllers\Landlord\PropertyController::class, 'contactAdmin'])->name('contact-admin');
    
    // Property Image management
    Route::get('/properties/{property}/images/upload', function($property) {
        return redirect()->route('landlord.properties.edit', $property);
    });
    Route::post('/properties/{property}/images/upload', [\App\Http\Controllers\Landlord\PropertyImageController::class, 'upload'])->name('properties.images.upload');
    Route::post('/properties/{property}/images/{image}/set-cover', [\App\Http\Controllers\Landlord\PropertyImageController::class, 'setCover'])->name('properties.images.set-cover');
    Route::delete('/properties/{property}/images/{image}', [\App\Http\Controllers\Landlord\PropertyImageController::class, 'delete'])->name('properties.images.delete');

    // Room Image management
    Route::post('/properties/{property}/rooms/{room}/images/upload', [\App\Http\Controllers\Landlord\RoomImageController::class, 'upload'])->name('rooms.images.upload');
    Route::post('/properties/{property}/rooms/{room}/images/{image}/set-cover', [\App\Http\Controllers\Landlord\RoomImageController::class, 'setCover'])->name('rooms.images.set-cover');
    Route::delete('/properties/{property}/rooms/{room}/images/{image}', [\App\Http\Controllers\Landlord\RoomImageController::class, 'delete'])->name('rooms.images.delete');
    
    // Inquiries/Messages
    Route::get('/inquiries', [\App\Http\Controllers\Landlord\InquiryController::class, 'index'])->name('inquiries.index');
    Route::post('/inquiries/{inquiry}/approve', [\App\Http\Controllers\Landlord\InquiryController::class, 'approve'])->name('inquiries.approve');
    Route::post('/inquiries/{inquiry}/reject', [\App\Http\Controllers\Landlord\InquiryController::class, 'reject'])->name('inquiries.reject');
    Route::post('/inquiries/{inquiry}/reply', [\App\Http\Controllers\Landlord\InquiryController::class, 'reply'])->name('inquiries.reply');
    
    // Bookings management
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

    // Scheduled visits management for landlords
    Route::get('/scheduled-visits', [App\Http\Controllers\LandlordController::class, 'scheduledVisits'])->name('scheduled-visits');
    Route::post('/scheduled-visits/{visit}/confirm', [App\Http\Controllers\ScheduledVisitController::class, 'confirm'])->name('scheduled-visits.confirm');
    Route::post('/scheduled-visits/{visit}/complete', [App\Http\Controllers\ScheduledVisitController::class, 'markCompleted'])->name('scheduled-visits.complete');
    Route::post('/scheduled-visits/{visit}/cancel-by-landlord', [App\Http\Controllers\ScheduledVisitController::class, 'cancelByLandlord'])->name('scheduled-visits.cancel-by-landlord');

    // Admin Messages
    Route::get('/admin-messages', [App\Http\Controllers\LandlordController::class, 'adminMessages'])->name('admin-messages');
    Route::get('/admin-messages/{message}', [App\Http\Controllers\LandlordController::class, 'viewAdminMessage'])->name('admin-messages.show');

    // Notifications
    Route::get('/notifications', [App\Http\Controllers\LandlordController::class, 'notifications'])->name('notifications');
    Route::get('/admin-response', [App\Http\Controllers\LandlordController::class, 'viewAdminResponse'])->name('admin-response');
    Route::get('/notifications/mark-all-read-test', [App\Http\Controllers\LandlordController::class, 'markAllNotificationsReadTest'])->name('notifications.mark-all-read-test');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\LandlordController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{notification}/read', [App\Http\Controllers\LandlordController::class, 'markNotificationRead'])->name('notifications.read');
    // Temporary GET fallback for cached requests - redirects to POST form
    Route::get('/notifications/{notification}/read', function(\App\Models\Notification $notification) {
        // Return a simple form that auto-submits via POST
        return view('landlord.notification-redirect', compact('notification'));
    })->name('notifications.read.fallback');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Account Management
    Route::get('/account', [\App\Http\Controllers\Admin\AccountController::class, 'index'])->name('account');
    Route::post('/account/upload-picture', [\App\Http\Controllers\Admin\AccountController::class, 'uploadProfilePicture'])->name('account.upload-picture');

    // Admin Dashboard
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Property approval
    Route::get('/properties/pending', [\App\Http\Controllers\Admin\PropertyController::class, 'pending'])->name('properties.pending');
    Route::post('/properties/{property}/approve', [\App\Http\Controllers\Admin\PropertyController::class, 'approve'])->name('properties.approve');
    Route::post('/properties/{property}/reject', [\App\Http\Controllers\Admin\PropertyController::class, 'reject'])->name('properties.reject');
    Route::post('/properties/{property}/verify', [\App\Http\Controllers\Admin\PropertyController::class, 'verify'])->name('properties.verify');
    Route::post('/properties/{property}/feature', [\App\Http\Controllers\Admin\PropertyController::class, 'feature'])->name('properties.feature');

    // Property deletion requests management
    Route::get('/properties/deletion-requests', [\App\Http\Controllers\Admin\PropertyController::class, 'deletionRequests'])->name('properties.deletion-requests');
    Route::get('/properties/deletion-requests/{deletionRequest}', [\App\Http\Controllers\Admin\PropertyController::class, 'viewDeletionRequest'])->name('properties.deletion-requests.view');
    Route::post('/properties/deletion-requests/{deletionRequest}/approve', [\App\Http\Controllers\Admin\PropertyController::class, 'approveDeletion'])->name('properties.deletion-requests.approve');
    Route::post('/properties/deletion-requests/{deletionRequest}/reject', [\App\Http\Controllers\Admin\PropertyController::class, 'rejectDeletion'])->name('properties.deletion-requests.reject');

    // Admin messages management
    Route::get('/messages', [\App\Http\Controllers\Admin\MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [\App\Http\Controllers\Admin\MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/respond', [\App\Http\Controllers\Admin\MessageController::class, 'respond'])->name('messages.respond');
    Route::post('/messages/{message}/read', [\App\Http\Controllers\Admin\MessageController::class, 'markAsRead'])->name('messages.read');
    Route::post('/messages/{message}/resolve', [\App\Http\Controllers\Admin\MessageController::class, 'markAsResolved'])->name('messages.resolve');
    
    // User management
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/role', [\App\Http\Controllers\Admin\UserController::class, 'updateRole'])->name('users.role');
    Route::post('/users/{user}/verify', [\App\Http\Controllers\Admin\UserController::class, 'verify'])->name('users.verify');

    // Landlord document verification
    Route::get('/landlords/verify', [\App\Http\Controllers\Admin\LandlordController::class, 'verify'])->name('landlords.verify');
    Route::post('/landlords/{landlord}/approve-id', [\App\Http\Controllers\Admin\LandlordController::class, 'approveDocuments'])->name('landlords.approve-id');
    Route::post('/landlords/{landlord}/reject-id', [\App\Http\Controllers\Admin\LandlordController::class, 'rejectDocuments'])->name('landlords.reject-id');

    // Tenant ID verification
    Route::post('/tenants/{tenant}/approve-id', [\App\Http\Controllers\Admin\TenantController::class, 'approveId'])->name('tenants.approve-id');
    Route::post('/tenants/{tenant}/reject-id', [\App\Http\Controllers\Admin\TenantController::class, 'rejectId'])->name('tenants.reject-id');

    // Tenant Suspension
    Route::post('/users/{user}/suspend', [\App\Http\Controllers\Admin\SuspensionController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/lift-suspension', [\App\Http\Controllers\Admin\SuspensionController::class, 'lift'])->name('users.lift-suspension');

    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\ReportController::class, 'export'])->name('reports.export');
});

require __DIR__.'/auth.php';