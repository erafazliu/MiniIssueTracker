<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\IssueMemberController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
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

    Route::resource('projects', ProjectController::class);
    Route::resource('issues', IssueController::class);

    Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
    Route::post('/tags', [TagController::class, 'store'])->name('tags.store');
    Route::delete('/tags/{tag}', [TagController::class, 'destroy'])->name('tags.destroy');

    Route::post('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'store'])
        ->name('issues.tags.store');

    Route::delete('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'destroy'])
        ->name('issues.tags.destroy');

    Route::post('/issues/{issue}/members/{user}', [IssueMemberController::class, 'attach'])
        ->name('issues.members.attach');

    Route::delete('/issues/{issue}/members/{user}', [IssueMemberController::class, 'detach'])
        ->name('issues.members.detach');

    Route::get('/issues/{issue}/comments', [CommentController::class, 'index'])
        ->name('issues.comments.index');

    Route::post('/issues/{issue}/comments', [CommentController::class, 'store'])
        ->name('issues.comments.store');

    Route::post('/logout', [AuthController::class, 'destroy'])
        ->name('logout');
});
