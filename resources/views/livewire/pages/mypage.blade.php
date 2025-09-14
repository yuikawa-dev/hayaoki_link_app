<?php

use function Livewire\Volt\state;
use App\Models\User;
use App\Models\Post;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

state([
    'user' => fn() => auth()->user(),
    'recentPosts' => fn() => auth()->user()->posts()->latest()->take(5)->get(),
    'upcomingEvents' => fn() => auth()->user()->registeredEvents()->where('start_time', '>', now())->orderBy('start_time')->take(3)->get(),
]);

?>

<div class="py-6">
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-4">
            <div class="bg-green-50 border border-green-200 rounded-md p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- プロフィールセクション -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
            @if ($user->isAdmin())
                <!-- 管理者専用：お店登録ボタン -->
                <div
                    class="lg:col-span-5 bg-gradient-to-r from-rose-100 to-pink-100 border-2 border-rose-200 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-rose-400 to-pink-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">管理者メニュー</h3>
                                    <p class="text-sm text-gray-600">朝活お店・イベントの登録・管理ができます</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('shops.create') }}"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    お店を登録する
                                </a>
                                <a href="{{ route('events.create') }}"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3a1 1 0 012 0v4m0 0V3a1 1 0 012 0v4m0 0h4l-4 4-4-4h8z"></path>
                                    </svg>
                                    イベントを登録する
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <!-- プロフィール情報 -->
            <div class="lg:col-span-3 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $user->profile_image ? Storage::url($user->profile_image) : asset('storage/profile-images/default-profile.svg') }}"
                            alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                            <p class="text-gray-600 mt-1">{{ $user->bio }}</p>
                            <a href="{{ route('mypage.profile.edit') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4">
                                プロフィールを編集
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- お店を探すボタン -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 h-full flex items-center justify-center">
                    <a href="{{ route('shops.index') }}"
                        class="w-full h-full min-h-[120px] flex flex-col items-center justify-center bg-gradient-to-br from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-orange-300 shadow-lg hover:shadow-2xl hover:shadow-orange-400/50 hover:brightness-110">
                        <i class="fas fa-coffee text-4xl mb-2 hover:animate-pulse"></i>
                        <span class="text-lg font-semibold">お店を探す</span>
                    </a>
                </div>
            </div>

            <!-- イベントを探すボタン -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 h-full flex items-center justify-center">
                    <a href="{{ route('events.index') }}"
                        class="w-full h-full min-h-[120px] flex flex-col items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white rounded-lg transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-purple-300 shadow-lg hover:shadow-xl hover:bg-gradient-to-br hover:from-indigo-500 hover:to-pink-600">
                        <i class="fas fa-calendar-alt text-4xl mb-2"></i>
                        <span class="text-lg font-semibold">イベントを探す</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 最近の投稿 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">最近の投稿</h3>
                        <div class="flex items-center space-x-4">
                            <!-- みんなの朝ボタン（キラキラアニメーション付き） -->
                            <div class="relative" x-data="{ sparkle: false }">
                                <a href="{{ route('posts.index') }}" @mouseenter="sparkle = true"
                                    @mouseleave="sparkle = false"
                                    class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl relative overflow-hidden">
                                    <i class="fas fa-users mr-2 text-lg"></i>
                                    みんなの朝

                                    <!-- キラキラエフェクト -->
                                    <div x-show="sparkle"
                                        class="absolute inset-0 pointer-events-none rounded-lg overflow-hidden">
                                        <div
                                            class="absolute top-1 left-2 w-1 h-1 bg-white rounded-full animate-ping opacity-75">
                                        </div>
                                        <div class="absolute top-3 right-3 w-1.5 h-1.5 bg-yellow-300 rounded-full animate-pulse opacity-80"
                                            style="animation-delay: 0.1s;"></div>
                                        <div class="absolute bottom-2 left-1/3 w-1 h-1 bg-white rounded-full animate-bounce opacity-70"
                                            style="animation-delay: 0.2s;"></div>
                                        <div class="absolute top-1/2 right-1 w-0.5 h-0.5 bg-yellow-200 rounded-full animate-ping opacity-60"
                                            style="animation-delay: 0.3s;"></div>
                                        <div class="absolute bottom-1 right-1/4 w-1 h-1 bg-white rounded-full animate-pulse opacity-75"
                                            style="animation-delay: 0.4s;"></div>
                                        <div class="absolute top-2 left-1/2 w-0.5 h-0.5 bg-yellow-400 rounded-full animate-bounce opacity-80"
                                            style="animation-delay: 0.15s;"></div>
                                    </div>

                                    <!-- オーバーレイグロー -->
                                    <div x-show="sparkle" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                        class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-pulse rounded-lg">
                                    </div>
                                </a>
                            </div>

                            <!-- 新規投稿ボタン（大きくした） -->
                            <a href="{{ route('posts.create') }}"
                                class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <i class="fas fa-plus mr-2 text-lg"></i>
                                新規投稿
                            </a>

                            <a href="{{ route('mypage.posts') }}"
                                class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">
                                すべて見る
                            </a>
                        </div>
                    </div>
                    @if ($recentPosts->isEmpty())
                        <p class="text-gray-500 text-center py-4">まだ投稿がありません</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($recentPosts as $post)
                                <div class="border-b pb-4 last:border-b-0 last:pb-0">
                                    <p class="text-gray-600">{{ $post->content }}</p>
                                    <div class="flex items-center justify-between mt-2">
                                        <span
                                            class="text-sm text-gray-500">{{ $post->created_at->format('Y/m/d H:i') }}</span>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-heart"></i> {{ $post->reactions_count ?? 0 }}
                                            </span>
                                            <span class="text-sm text-gray-500">
                                                <i class="fas fa-comment"></i> {{ $post->comments_count ?? 0 }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- 参加予定のイベント -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">参加予定のイベント</h3>
                        <a href="{{ route('mypage.events') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                            すべて見る
                        </a>
                    </div>
                    @if ($upcomingEvents->isEmpty())
                        <p class="text-gray-500 text-center py-4">参加予定のイベントはありません</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($upcomingEvents as $event)
                                <div class="border-b pb-4 last:border-b-0 last:pb-0">
                                    <h4 class="font-medium text-gray-900">{{ $event->name }}</h4>
                                    <div class="mt-1">
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-calendar"></i>
                                            {{ $event->start_time->format('Y/m/d H:i') }} 〜
                                            {{ $event->end_time->format('H:i') }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-map-marker-alt"></i>
                                            {{ $event->location }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
