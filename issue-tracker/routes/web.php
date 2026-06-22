<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Minimal pages so navigation links resolve
    Route::get('/projects', function () {
        return view('projects.index');
    })->name('projects.index');

    Route::get('/issues', function () {
        return view('issues.index');
    })->name('issues.index');

    Route::get('/tags', function () {
        return view('tags.index');
    })->name('tags.index');

    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('logout');
});
