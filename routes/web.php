<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;


// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit'); 


Route::get('/contact', [AuthController::class, 'showRegistrationForm'])->name('contact');
Route::get('/about', [AuthController::class, 'showRegistrationForm'])->name('about');
Route::get('/event', [AuthController::class, 'showRegistrationForm'])->name('events');
Route::get('/members/directory', [AuthController::class, 'showRegistrationForm'])->name('members.directory');
Route::get('/resources', [AuthController::class, 'showRegistrationForm'])->name('resources');


//Grouped Routes with Middleware
Route::middleware(['auth'])->group(function () {
    // Add more protected routes here
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('member.dashboard');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('member.profile');
    //Route::get('/dashboard/certificates', [DashboardController::class, 'certificateForm'])->name('dashboard.certificates');
    Route::get('/dashboard/certificates', [DashboardController::class, 'certificates'])->name('dashboard.certificates');
    Route::post('/dashboard/certificates', [DashboardController::class, 'certificates'])->name('member.certificates.download');
    Route::get('/interests', [DashboardController::class, 'interests'])->name('member.interests');

    Route::get('/dashboard/request-certificate', [DashboardController::class,'requestCertificate'])->name('dashboard.request-certificate')
});

Route::get('/logout', function () {
    auth()->logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');