<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Middleware\ActivityLogMiddleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;


// Authentication Routes
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update'); 


Route::get('/contact', [AuthController::class, 'showRegistrationForm'])->name('contact');
Route::get('/about', [AuthController::class, 'showRegistrationForm'])->name('about');
Route::get('/event', [AuthController::class, 'showRegistrationForm'])->name('events');
Route::get('/members/directory', [AuthController::class, 'showRegistrationForm'])->name('members.directory');
Route::get('/resources', [AuthController::class, 'showRegistrationForm'])->name('resources');
Route::post('/webhooks/paystack', [FinanceController::class, 'handlePaystackWebhook'])->name('webhooks.paystack');
Route::get('/payments/callback', [FinanceController::class, 'callback'])->name('payments.callback');


//Grouped Routes with Middleware
Route::middleware([Authenticate::class, ActivityLogMiddleware::class])->group(function () {
    // Add more protected routes here
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('member.dashboard');
    Route::get('/dashboard/profile', [DashboardController::class, 'profile'])->name('member.profile');
    Route::post('/dashboard/profile', [DashboardController::class, 'updateProfileBio'])->name('member.profile.update');
    Route::post('/dashboard/profile/picture', [DashboardController::class, 'updateProfilePicture'])->name('member.profile.pix.update');
    Route::get('/dashboard/profile/picture/view/{filename?}', [DashboardController::class, 'viewProfilePicture'])
        ->where('filename', '.*')
        ->name('member.profile.picture.view');
    
    //Route::get('/dashboard/certificates', [DashboardController::class, 'certificateForm'])->name('dashboard.certificates');
    Route::middleware('dues.paid')->group(function () {
        Route::get('/dashboard/certificates', [DashboardController::class, 'certificates'])->name('dashboard.certificates');
        Route::post('/dashboard/certificates', [DashboardController::class, 'certificates'])->name('member.certificates.download');
        Route::get('/dashboard/resources', [DashboardController::class, 'resources'])->name('dashboard.resources');
        Route::get('/dashboard/resources/{resource}/download', [DashboardController::class, 'downloadResource'])->name('dashboard.resources.download');
        Route::get('/dashboard/events', [DashboardController::class, 'events'])->name('dashboard.events.index');
        Route::get('/dashboard/events/{event:slug}', [DashboardController::class, 'showEvent'])->name('dashboard.events.show');
        Route::post('/dashboard/events/{event:slug}/register', [DashboardController::class, 'registerForEvent'])->name('dashboard.events.register');
        Route::get('/dashboard/request-certificate', [DashboardController::class, 'requestCertificate'])->name('dashboard.request-certificate');
    });

    Route::get('/dashboard/interests', [DashboardController::class, 'interests'])->name('member.interests');
    Route::post('/dashboard/interests', [DashboardController::class, 'addInterest'])->name('member.interests.add');
    Route::delete('/dashboard/interests/{interest}', [DashboardController::class, 'removeInterest'])->name('member.interests.remove');
    Route::get('/dashboard/payments', [FinanceController::class, 'memberPaymentCatalog'])->name('dashboard.payments');
    Route::post('/payments/start/{code}', [FinanceController::class, 'startPaymentByCode'])->name('payments.start');
});

Route::middleware([Authenticate::class, AdminMiddleware::class, ActivityLogMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/members', [AdminController::class, 'members'])->name('members.index');
    Route::put('/members/{member}/role', [AdminController::class, 'updateRole'])->name('members.role.update');
    Route::get('/events', [AdminController::class, 'events'])->name('events.index');
    Route::get('/events/create', [AdminController::class, 'createEvent'])->name('events.create');
    Route::post('/events', [AdminController::class, 'storeEvent'])->name('events.store');
    Route::get('/events/{event}/edit', [AdminController::class, 'editEvent'])->name('events.edit');
    Route::put('/events/{event}', [AdminController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/{event}', [AdminController::class, 'destroyEvent'])->name('events.destroy');
    Route::get('/events/{event}/registrants', [AdminController::class, 'eventRegistrants'])->name('events.registrants');
    Route::get('/resources', [AdminController::class, 'resources'])->name('resources.index');
    Route::post('/resources', [AdminController::class, 'storeResource'])->name('resources.store');
    Route::delete('/resources/{resource}', [AdminController::class, 'destroyResource'])->name('resources.destroy');
    Route::get('/certificate-settings', [AdminController::class, 'certificateSettings'])->name('certificate-settings.index');
    Route::post('/certificate-settings/{type}', [AdminController::class, 'updateCertificateSetting'])->name('certificate-settings.update');
    Route::get('/certificate-settings/{setting}/signature', [AdminController::class, 'viewCertificateSignature'])->name('certificate-settings.signature');
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/export', [FinanceController::class, 'export'])->name('finance.export');
    Route::get('/finance/payment-configurations', [FinanceController::class, 'paymentConfigurations'])->name('finance.payment-configurations.index');
    Route::post('/finance/payment-configurations', [FinanceController::class, 'storePaymentConfiguration'])->name('finance.payment-configurations.store');
    Route::get('/finance/payment-configurations/{paymentConfiguration}/edit', [FinanceController::class, 'editPaymentConfiguration'])->name('finance.payment-configurations.edit');
    Route::put('/finance/payment-configurations/{paymentConfiguration}', [FinanceController::class, 'updatePaymentConfiguration'])->name('finance.payment-configurations.update');
    Route::delete('/finance/payment-configurations/{paymentConfiguration}', [FinanceController::class, 'destroyPaymentConfiguration'])->name('finance.payment-configurations.destroy');
    Route::get('/finance/activity-logs', [FinanceController::class, 'activityLogs'])->name('finance.activity-logs.index');
    Route::get('/finance/activity-logs/export', [FinanceController::class, 'exportActivityLogs'])->name('finance.activity-logs.export');
    Route::get('/finance/activity-logs/{activityLog}', [FinanceController::class, 'showActivityLog'])->name('finance.activity-logs.show');
});

Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/');
})->name('logout');