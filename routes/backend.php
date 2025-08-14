<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\AnalyticsController;
use App\Http\Controllers\Backend\EnquiryController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () { Inertia::render('dashboard');})->name('dashboard');
});
 */

// Dashboard route - redirects to admin dashboard
// -------------------------------------------------------------------------------------------------------- ::
/* Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('admin.dashboard.index');
    })->name('dashboard');
}); */

// -------------------------------------------------------------------------------------------------------- ::
Route::middleware(['auth', 'verified', 'admin', 'preventBackHistory'])->group(function () {

    // Main dashboard route - direct access
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin prefix for all backend routes
    // -------------------------------------------------------------------------------------------------------- ::
    Route::prefix('admin')->name('admin.')->group(function () {

        // Users
        Route::resource('users', UserController::class);
        Route::post('users/data', [UserController::class, 'data'])->name('users.data');
        Route::post('users/list', [UserController::class, 'list'])->name('users.list');
        Route::post('users/change-status', [UserController::class, 'changeStatus'])->name('users.change.status');

        // Roles
        Route::resource('roles', RoleController::class);
        Route::post('roles/data', [RoleController::class, 'data'])->name('roles.data');
        Route::post('roles/list', [RoleController::class, 'list'])->name('roles.list');

        // Permissions
        Route::get('roles/{role}/permission/show', [RoleController::class, 'permissionsShow'])->name('roles.permissions.show');
        Route::post('roles/{role}/permission/update', [RoleController::class, 'permissionsUpdate'])->name('roles.permissions.update');

        // Employees
        Route::resource('employees', EmployeeController::class);
        Route::post('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
        Route::post('employees/list', [EmployeeController::class, 'list'])->name('employees.list');
        Route::post('employees/change-status', [EmployeeController::class, 'changeStatus'])->name('employees.change.status');

        // Enquiries
        Route::resource('enquiries', EnquiryController::class);
        Route::post('enquiries/data', [EnquiryController::class, 'data'])->name('enquiries.data');
        Route::post('enquiries/list', [EnquiryController::class, 'list'])->name('enquiries.list');
        Route::post('enquiries/{enquiry}/remark', [EnquiryController::class, 'updateRemark'])->name('enquiries.update.remark');

        // Analytics
        Route::resource('analytics', AnalyticsController::class);
        // End of File
    });
});
