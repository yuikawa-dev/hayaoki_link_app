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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- プロフィールセクション -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 最近の投稿 -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">最近の投稿</h3>
                        <a href="{{ route('mypage.posts') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                            すべて見る
                        </a>
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
                                            class="text-sm text-gray-500">{{ $post->posted_at->format('Y/m/d H:i') }}</span>
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
