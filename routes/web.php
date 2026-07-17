<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'auth.login')->middleware('guest')->name('home');
Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::get('/dashboard', function () {
        $year = now()->year;
        $monthlyPostCounts = Post::query()
            ->whereYear('created_at', $year)
            ->get(['created_at'])
            ->countBy(fn (Post $post): int => $post->created_at->month);

        return view('dashboard.index', [
            'categoryCount' => Category::query()->count(),
            'postCount' => Post::query()->count(),
            'userCount' => User::query()->count(),
            'chartYear' => $year,
            'postChartLabels' => collect(range(1, 12))
                ->map(fn (int $month): string => now()->month($month)->format('M'))
                ->all(),
            'postChartSeries' => collect(range(1, 12))
                ->map(fn (int $month): int => (int) ($monthlyPostCounts[$month] ?? 0))
                ->all(),
        ]);
    })->name('dashboard');

    Route::view('/categories', 'categories.index')->name('categories.index');
    Route::view('/categories/create', 'categories.create')->name('categories.create');

    Route::view('/posts', 'posts.index')->name('posts.index');
    Route::view('/posts/create', 'posts.create')->name('posts.create');

    Route::view('/profile', 'profile.index')->name('profile.index');

    Route::get('/users', function () {
        abort_unless(auth()->user()?->role === 'admin', 403);

        return view('users.index');
    })->name('users.index');
});
