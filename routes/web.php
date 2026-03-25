<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LostItemController;
use App\Http\Controllers\FoundItemController;
use App\Http\Controllers\NotificationController;
use App\Services\MatchingService;

use Illuminate\Support\Facades\Auth;



// Authentication Routes
Auth::routes(['register' => true]); // Enable registration

// Custom Login Controller
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// Custom Registration Controller
Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Redirect based on role after login
Route::get('/home', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Authentication Routes
Auth::routes();

// Home Route - Redirect based on role
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('user.dashboard');
    }
    return redirect()->route('login');
});

// User Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    
    // Lost Items
    Route::get('/lost-form', [UserController::class, 'showLostForm'])->name('user.lost-form');
    Route::resource('lost-items', LostItemController::class);
    
    // Found Items
    Route::get('/found-form', [UserController::class, 'showFoundForm'])->name('user.found-form');
    Route::resource('found-items', FoundItemController::class);
    
    // Notifications
    // Route::get('/notifications', [UserController::class, 'notifications'])->name('user.notifications');
    // Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('user.notifications.unread-count');
    // Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('user.notifications.read');
    // Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('user.notifications.mark-all-read');
    // Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('user.notifications.destroy');
    

Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {

   Route::get('/notifications', [UserController::class, 'notifications'])
        ->name('notifications');

    Route::post('/notifications/read/{id}', [UserController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::post('/notifications/mark-all-read', [UserController::class, 'markAllRead'])
        ->name('notifications.mark-all-read');

    Route::delete('/notifications/{id}', [UserController::class, 'destroyNotification'])
        ->name('notifications.destroy');

    Route::get('/notifications/unread-count', [UserController::class, 'unreadCount'])
        ->name('notifications.unread-count');
});

    // Profile
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/profile/change-password', [UserController::class, 'changePassword'])->name('user.change-password');
    
    // My Items
    Route::match(['get','post'],'/my-lost-items', [UserController::class, 'myLostItems'])->name('user.my-lost-items');
    Route::get('/my-found-items', [UserController::class, 'myFoundItems'])->name('user.my-found-items');
    
    // Search
    Route::get('/search', [UserController::class, 'search'])->name('user.search');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Verification Queue
    Route::get('/verifications', [AdminController::class, 'verificationQueue'])->name('verifications');
    Route::post('/verify-match', [AdminController::class, 'verifyMatch'])->name('verify-match');
    
//     Route::post('/admin/verify/{id}', function($id) {
//     $match = \App\Models\ItemMatch::find($id);
//     $match->update(['status' => 'verified']);
//     return back()->with('success', 'Done!');
// });

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    
    // Category Management
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    
    // Success Stories
    Route::get('/success-stories', [AdminController::class, 'successStories'])->name('success-stories');
    
    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
});

// Public Routes for Success Stories
Route::get('/success-stories', function () {
    $stories = \App\Models\SuccessStory::with(['lostItem', 'foundItem'])
        ->where('is_published', true)
        ->orderBy('created_at', 'desc')
        ->paginate(12);
    
    return view('public.success-stories', compact('stories'));
})->name('public.success-stories');
Auth::routes();


Route::get('/run-matching', function () {
    MatchingService::generateMatches();
    return 'Matching executed';
});