<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerBookingController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StaffScheduleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Guest Routes
Route::get('/', function () {
    return view('landing');
})->name('landing');

// Static Pages
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

// Custom Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Guest can book without login
Route::post('/guest-book', [CustomerBookingController::class, 'store'])->name('guest.book');

// Notification Routes
Route::middleware(['auth'])->group(function () {
    // Notification routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
    });
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {

    Route::middleware(['role:customer'])->prefix('customer')->name('customer.')->group(function () {

        // Customer Dashboard    
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        // Staff Listing
        Route::get('/staff', [CustomerDashboardController::class, 'staff'])->name('staff');
        Route::get('/staff/{id}', [CustomerDashboardController::class, 'showStaff'])->name('staff.show');
        // Appointments
        Route::prefix('appointments')->name('appointments.')->group(function () {
            Route::get('/', [AppointmentController::class, 'customerIndex'])->name('index');
            Route::get('/create', [AppointmentController::class, 'create'])->name('create');
            Route::post('/', [AppointmentController::class, 'store'])->name('store');
            Route::get('/{appointment}', [AppointmentController::class, 'show'])->name('show');
            Route::get('/{appointment}/cancel', [AppointmentController::class, 'showCustomerCancelForm'])->name('cancel.form');
            Route::post('/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('cancel');
            Route::post('/check-availability', [AppointmentController::class, 'checkAvailability'])->name('checkAvailability');
        });

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Home redirect based on role
    Route::get('/home', function () {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && ($user->isAdmin() || $user->isStaff())) {
            return redirect()->route('dashboard.index');
        }
        return view('landing');
    })->name('home');

    // Messaging routes (accessible by all authenticated users)
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/{user}', [MessageController::class, 'getConversation'])->name('conversation');
        Route::post('/{user}/send', [MessageController::class, 'sendMessage'])->name('send');
        Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unreadCount');
        Route::post('/mark-all-read', [MessageController::class, 'markAllAsRead'])->name('markAllRead');
        Route::get('/notifications', [MessageController::class, 'getNotifications'])->name('notifications');
        Route::get('/refresh/{user}', [MessageController::class, 'refreshConversation'])->name('refresh');
    });

    // Staff-specific routes
    Route::middleware(['role:staff'])->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        Route::get('/appointments', [StaffController::class, 'appointments'])->name('appointments');
        Route::get('/appointments/{appointment}', [StaffController::class, 'showAppointment'])->name('appointments.show');
        Route::get('/commission', [StaffController::class, 'commissionReport'])->name('commission');
        Route::post('/service-report', [StaffController::class, 'submitServiceReport'])->name('service-report');
        Route::get('/statistics', [StaffController::class, 'getStatistics'])->name('statistics');
        
        // Weekly Schedule routes
        Route::get('/schedule', [StaffScheduleController::class, 'index'])->name('schedule');
        Route::post('/schedule', [StaffScheduleController::class, 'store'])->name('schedule.store');
        Route::get('/schedule/{schedule}', [StaffScheduleController::class, 'show'])->name('schedule.show');
        Route::put('/schedule/{schedule}', [StaffScheduleController::class, 'update'])->name('schedule.update');
        Route::delete('/schedule/{schedule}', [StaffScheduleController::class, 'destroy'])->name('schedule.destroy');
    });

    // Admin-only routes
    Route::middleware(['role:admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserManagementController::class, 'show'])->name('users.show');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role.update');
        Route::patch('/users/{user}/role', [UserManagementController::class, 'updateRole'])->name('users.role');
        Route::patch('/users/{user}/toggle-active', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });

    // Routes accessible by both admin and staff
    Route::middleware(['role:admin,staff'])->prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        // Appointments
        // Define specific routes BEFORE resource routes to avoid conflicts
        Route::post('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::get('appointments/{appointment}/cancel', [AppointmentController::class, 'showCancelForm'])->name('appointments.cancel.form');
        Route::post('appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
        Route::post('appointments/check-availability', [AppointmentController::class, 'checkAvailability'])->name('appointments.checkAvailability');
        // Resource route must come after specific routes
        Route::resource('appointments', AppointmentController::class)->except(['destroy']);

        // Services
        Route::resource('services', ServiceController::class);

        // Payments
        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::get('appointments/{appointment}/payment/create', [PaymentController::class, 'createForAppointment'])->name('payments.create');
        Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::post('payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');
        Route::get('payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
        Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/appointments', [ReportsController::class, 'appointments'])->name('appointments');
            Route::get('/revenue', [ReportsController::class, 'revenue'])->name('revenue');
            Route::get('/inventory', [ReportsController::class, 'inventory'])->name('inventory');
            Route::post('/download', [ReportsController::class, 'download'])->name('download');
            Route::get('/financial', [ReportsController::class, 'financial'])->name('financial');
        });

        // Commissions (Admin only)
        Route::middleware(['role:admin'])->prefix('commissions')->name('commissions.')->group(function () {
            Route::get('/', [CommissionController::class, 'index'])->name('index');
            // Specific routes must come before parameterized routes
            Route::get('/settings', [CommissionController::class, 'showSettings'])->name('settings');
            Route::post('/settings', [CommissionController::class, 'updateSettings'])->name('settings.update');
            Route::get('/report', [CommissionController::class, 'showReportForm'])->name('report');
            Route::post('/report/generate', [CommissionController::class, 'generateReport'])->name('report.generate');
            Route::post('/bulk-pay', [CommissionController::class, 'payCommissions'])->name('bulk-pay');
            // Parameterized routes come last
            Route::get('/{commission}', [CommissionController::class, 'show'])->name('show');
            Route::post('/{commission}/pay', [CommissionController::class, 'paySingle'])->name('pay');
        });

        // Salon Settings (Admin only)
        Route::middleware(['role:admin'])->prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::post('/', [SettingsController::class, 'update'])->name('update');
        });

        // Inventory
        Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/daily-update', [InventoryController::class, 'dailyUpdate'])->name('inventory.daily-update');
        Route::post('/inventory/daily-update-save', [InventoryController::class, 'saveDailyUpdates'])->name('inventory.daily-update.save');
        Route::get('/inventory/items/{item}', [InventoryController::class, 'getItem'])->name('inventory.items.show');
        Route::post('/inventory/items', [InventoryController::class, 'storeItem'])->name('inventory.items.store');
        Route::post('/inventory/items/{item}/update-stock', [InventoryController::class, 'updateStock'])->name('inventory.items.update-stock');
    });

    // AJAX route for dashboard filtering (accessible to admin & staff)
    Route::post('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');
});

// Legacy routes (kept for backward compatibility or external links)
Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('dashboard.appointments.create');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('dashboard.appointments.store');
Route::get('/staff', [StaffController::class, 'index'])->name('dashboard.staff.index');
Route::get('/reports/financial', [ReportsController::class, 'financial'])->name('dashboard.reports.financial');