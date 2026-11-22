<?php

use App\Http\Controllers\Authentication\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\EnsureUserHasRole;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::redirect('/', '/dashboard');

// Guest Routes
Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'login'])
        ->name('login');

    Route::post('/login', [LoginController::class, 'process_login'])
        ->name('process_login');

});

// Authenticated Routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');

    // User Management: Super Admin and Admin only.
    Route::resource('users', UserController::class)
        ->except('show')
        ->middleware(EnsureUserHasRole::class.':super_admin,admin');

    // Employee Management: Super Admin, Admin and Super User.
    Route::resource('employees', EmployeeController::class)
        ->except('show')
        ->middleware(EnsureUserHasRole::class.':super_admin,admin,super_user');

        // Student Management: Super Admin, Admin and Super User.
    Route::resource('students', StudentController::class)
        ->except('show')
        ->middleware(EnsureUserHasRole::class.':super_admin,admin,super_user');

});