<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeAuthController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\EmployeeTaskController;
use App\Http\Controllers\UserTaskHistoryExportController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

Route::get('/employee/login', [EmployeeAuthController::class, 'showLoginForm'])->name('employee.login');
Route::post('/employee/login', [EmployeeAuthController::class, 'login'])->name('employee.login.submit');

Route::get('/employee', function () {
    return redirect('/employee/dashboard');
});

Route::middleware(['auth', 'employee'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
    Route::get('/employee/tasks', [EmployeeDashboardController::class, 'allTasks'])->name('employee.tasks.index');
    Route::get('/employee/history', [EmployeeDashboardController::class, 'history'])->name('employee.history');
    Route::get('/employee/profile', [EmployeeDashboardController::class, 'profile'])->name('employee.profile');
    Route::get('/employee/settings', [EmployeeDashboardController::class, 'settings'])->name('employee.settings');
    
    Route::post('/employee/logout', [EmployeeAuthController::class, 'logout'])->name('employee.logout');

    Route::get('/admin/task-history/pdf', [UserTaskHistoryExportController::class, 'pdf'])
        ->name('user-task-history.pdf');
    
    
    // Task actions
    Route::post('/employee/tasks/create', [EmployeeTaskController::class, 'store'])->name('employee.tasks.store');
    Route::post('/employee/tasks/{task}/status', [EmployeeTaskController::class, 'updateStatus'])->name('employee.tasks.status');
});
