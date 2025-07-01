<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\ServiceController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Email Verification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function () {
        auth()->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Link verifikasi telah dikirim!');
    })->name('verification.send');
});

// Public Complaint Tracking
Route::get('/track', [ComplaintController::class, 'track'])->name('complaints.track');
Route::post('/track', [ComplaintController::class, 'trackResult'])->name('complaints.track.result');

// Authenticated User Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/services', [DashboardController::class, 'services'])->name('services.index');

    // Complaints
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create/{service}', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints/create/{service}', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/documents/{document}/download', [ComplaintController::class, 'downloadDocument'])->name('documents.download');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Complaints Management
    Route::get('/complaints', [AdminController::class, 'complaints'])->name('complaints.index');
    Route::get('/complaints/{complaint}', [AdminController::class, 'showComplaint'])->name('complaints.show');
    Route::post('/complaints/{complaint}/status', [AdminComplaintController::class, 'updateStatus'])->name('complaints.update-status');
    Route::post('/complaints/{complaint}/upload-result', [AdminComplaintController::class, 'uploadResultDocument'])->name('complaints.upload-result');
    Route::delete('/complaints/documents/{document}', [AdminComplaintController::class, 'deleteResultDocument'])->name('complaints.delete-document');
    Route::post('/complaints/bulk-update', [AdminComplaintController::class, 'bulkUpdateStatus'])->name('complaints.bulk-update');
    Route::get('/complaints/export', [AdminComplaintController::class, 'export'])->name('complaints.export');
    
    // Services Management (for Super Admin)
    Route::middleware('can:manage-services')->group(function () {
        Route::resource('services', ServiceController::class);
        Route::resource('service-categories', ServiceCategoryController::class);
    });
});
