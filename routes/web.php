<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return redirect()->route('posts.index');
})->name('home');

Volt::route('posts', 'pages.posts.index')->name('posts.index');
Volt::route('shops', 'pages.shops.index')->name('shops.index');
Volt::route('shops/create', 'pages.shops.create')->name('shops.create')->middleware('auth');
Volt::route('shops/{shop}', 'pages.shops.show')->name('shops.show');
Volt::route('shops/{shop}/edit', 'pages.shops.edit')->name('shops.edit')->middleware('auth');
Volt::route('events', 'pages.events.index')->name('events.index');
Volt::route('events/{event}', 'pages.events.show')->name('events.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    // 投稿関連のルート
    Route::get('posts/create', App\Livewire\PostCreate::class)->name('posts.create');
    Volt::route('posts/{post}', 'pages.posts.show')->name('posts.show');
    // マイページ関連のルート
    Volt::route('mypage', 'pages.mypage')->name('mypage');
    Volt::route('mypage/profile/edit', 'pages.mypage.profile-edit')->name('mypage.profile.edit');
    Volt::route('mypage/posts', 'pages.mypage.posts')->name('mypage.posts');
    Volt::route('mypage/events', 'pages.mypage.events')->name('mypage.events');

    // 設定関連のルート
    Route::redirect('settings', 'settings/profile');
    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
