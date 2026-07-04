<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::view('/dashboard', 'dashboard.index')->middleware('auth')->name('dashboard');

Route::view('/categories', 'categories.index')->middleware('auth')->name('categories.index');
Route::view('/categories/create', 'categories.create')->middleware('auth')->name('categories.create');

Route::view('/posts', 'posts.index')->middleware('auth')->name('posts.index');
Route::view('/posts/create', 'posts.create')->middleware('auth')->name('posts.create');

Route::middleware('auth')->group(function (): void {
    Route::get('/users', function () {
        abort_unless(auth()->user()?->role === 'admin', 403);

        return view('users.index');
    })->name('users.index');
});
