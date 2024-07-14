<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'check.impersonate'])->group(function () {

    Route::middleware(['can:edit-user|delete-user|create-user'])->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware(['can:edit-group|delete-group|create-group|publish-group'])->group(function () {
        Route::resource('groups', GroupController::class);
    });

    Route::resource('roles', RoleController::class);

    Route::get('impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');
    Route::get('impersonate/{user}', [ImpersonateController::class, 'takeOver'])->name('impersonate.user');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
