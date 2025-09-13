<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
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
