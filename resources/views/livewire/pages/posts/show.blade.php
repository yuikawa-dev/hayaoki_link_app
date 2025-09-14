<?php

use function Livewire\Volt\{state, mount};
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;

state([
    'post' => null,
    'newComment' => '',
]);

mount(function () {
    $this->post = Post::with(['comments.user', 'likes'])->findOrFail(request()->route('post'));
});

public function addComment()
{
    $this->validate([
        'newComment' => 'required|min:1|max:1000',
    ]);

    Comment::create([
        'user_id' => auth()->id(),
        'post_id' => $this->post->id,
        'content' => $this->newComment,
    ]);

    $this->newComment = '';
    $this->post = Post::with(['comments.user', 'likes'])->findOrFail($this->post->id);
}

public function toggleLike()
{
    $existingLike = Like::where('user_id', auth()->id())
        ->where('post_id', $this->post->id)
        ->first();

    if ($existingLike) {
        $existingLike->delete();
    } else {
        Like::create([
            'user_id' => auth()->id(),
            'post_id' => $this->post->id,
        ]);
    }

    $this->post = Post::with(['comments.user', 'likes'])->findOrFail($this->post->id);
}

?>

<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border-b border-orange-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center py-4">
                    <a href="{{ route('posts.index') }}"
                        class="mr-4 p-2 bg-gradient-to-r from-orange-400 to-yellow-400 hover:from-orange-500 hover:to-yellow-500 text-white rounded-full shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                    </a>
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-8 h-8 bg-gradient-to-r from-orange-400 to-yellow-400 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                            </svg>
                        </div>
                        <h2
                            class="font-bold text-xl bg-gradient-to-r from-orange-600 to-yellow-600 bg-clip-text text-transparent">
                            投稿詳細
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-yellow-50 to-pink-50 py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-2xl border border-orange-100">
                <div class="p-8">
                    <!-- 投稿メイン部分 -->
                    <div
                        class="bg-gradient-to-r from-white to-orange-50/30 rounded-2xl p-6 border border-orange-100/50 mb-8">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0 relative">
                                @if ($post->user->profile_photo_url)
                                    <img class="h-14 w-14 rounded-full ring-2 ring-orange-200 ring-offset-2"
                                        src="{{ $post->user->profile_photo_url }}" alt="{{ $post->user->name }}">
                                @else
                                    <div
                                        class="h-14 w-14 rounded-full bg-gradient-to-r from-orange-200 to-yellow-200 ring-2 ring-orange-200 ring-offset-2 flex items-center justify-center">
                                        <span
                                            class="text-orange-600 font-semibold text-lg">{{ substr($post->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div
                                    class="absolute -bottom-1 -right-1 w-5 h-5 bg-gradient-to-r from-orange-400 to-yellow-400 rounded-full border-2 border-white">
                                </div>
                            </div>
                            <div class="ml-6">
                                <div class="text-lg font-semibold text-orange-800">
                                    {{ $post->user->name }}
                                </div>
                                <div class="flex items-center space-x-2 mt-1">
                                    <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                    </svg>
                                    <span class="text-sm text-orange-600 font-medium">
                                        {{ $post->created_at->format('Y年m月d日 H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if ($post->title)
                            <h1
                                class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-yellow-600 bg-clip-text text-transparent mb-6">
                                {{ $post->title }}
                            </h1>
                        @endif

                        <div class="bg-white/60 rounded-xl p-6 border border-orange-100/50">
                            <div class="text-orange-700 leading-relaxed text-lg">
                                {!! nl2br(e($post->content)) !!}
                            </div>
                        </div>
                    </div>

                    <!-- アクション部分 -->
                    <div
                        class="bg-gradient-to-r from-white to-orange-50/30 rounded-2xl p-6 border border-orange-100/50 mb-8">
                        <div class="flex items-center justify-between">
                            <button wire:click="toggleLike"
                                class="flex items-center space-x-3 px-4 py-2 bg-gradient-to-r {{ $post->likes()->where('user_id', auth()->id())->exists()? 'from-red-400 to-pink-400 text-white': 'from-orange-100 to-yellow-100 text-orange-600 hover:from-red-100 hover:to-pink-100 hover:text-red-600' }} rounded-full shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                <svg class="w-5 h-5 {{ $post->likes()->where('user_id', auth()->id())->exists()? 'fill-current': '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>
                                <span class="font-medium">{{ $post->likes()->count() }}</span>
                                <span class="text-sm">いいね</span>
                            </button>

                            <div class="flex items-center space-x-2 bg-orange-100/50 px-4 py-2 rounded-full">
                                <svg class="w-5 h-5 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                </svg>
                                <span class="text-orange-700 font-medium">{{ $post->comments()->count() }} コメント</span>
                            </div>
                        </div>
                    </div>

                    <!-- コメントフォーム -->
                    <div
                        class="bg-gradient-to-r from-white to-orange-50/30 rounded-2xl p-6 border border-orange-100/50 mb-8">
                        <div class="flex items-center space-x-3 mb-4">
                            <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-orange-800">コメントを書く</h3>
                        </div>

                        <form wire:submit.prevent="addComment">
                            <div class="mb-4">
                                <textarea wire:model.live="newComment"
                                    class="block w-full rounded-xl border-orange-200 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 bg-white/80 backdrop-blur-sm text-orange-700 placeholder-orange-400"
                                    rows="4" placeholder="あなたの朝の感想をシェアしてください..."></textarea>
                                @error('newComment')
                                    <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-400 to-yellow-400 hover:from-orange-500 hover:to-yellow-500 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                    </svg>
                                    コメントする
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- コメント一覧 -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            <h3
                                class="text-xl font-bold bg-gradient-to-r from-orange-600 to-yellow-600 bg-clip-text text-transparent">
                                みんなのコメント
                            </h3>
                        </div>

                        @if ($post->comments()->count() === 0)
                            <div
                                class="text-center py-12 bg-gradient-to-r from-white to-orange-50/30 rounded-2xl border border-orange-100/50">
                                <div
                                    class="w-16 h-16 bg-gradient-to-r from-orange-200 to-yellow-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                    </svg>
                                </div>
                                <p class="text-orange-600 font-medium">まだコメントがありません</p>
                                <p class="text-orange-500 text-sm mt-1">最初のコメントを投稿してみませんか？</p>
                            </div>
                        @else
                            @foreach ($post->comments()->with('user')->latest()->get() as $comment)
                                <div
                                    class="bg-gradient-to-r from-white to-orange-50/30 rounded-2xl p-6 border border-orange-100/50 shadow-sm hover:shadow-md transition-all duration-300">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0 relative">
                                            @if ($comment->user->profile_photo_url)
                                                <img class="h-10 w-10 rounded-full ring-2 ring-orange-200 ring-offset-1"
                                                    src="{{ $comment->user->profile_photo_url }}"
                                                    alt="{{ $comment->user->name }}">
                                            @else
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gradient-to-r from-orange-200 to-yellow-200 ring-2 ring-orange-200 ring-offset-1 flex items-center justify-center">
                                                    <span
                                                        class="text-orange-600 font-medium">{{ substr($comment->user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                            <div
                                                class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-gradient-to-r from-orange-400 to-yellow-400 rounded-full border border-white">
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="font-semibold text-orange-800">{{ $comment->user->name }}
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-3 h-3 text-orange-400" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                                    </svg>
                                                    <span class="text-sm text-orange-600 font-medium">
                                                        {{ $comment->created_at->format('Y年m月d日 H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="bg-white/60 rounded-xl p-4 border border-orange-100/50">
                                                <div class="text-orange-700 leading-relaxed">
                                                    {!! nl2br(e($comment->content)) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- デコレーション要素 -->
            <div class="fixed top-20 right-10 opacity-20 pointer-events-none">
                <div class="w-24 h-24 bg-gradient-to-r from-yellow-300 to-orange-300 rounded-full blur-2xl"></div>
            </div>
            <div class="fixed bottom-20 left-10 opacity-20 pointer-events-none">
                <div class="w-20 h-20 bg-gradient-to-r from-pink-300 to-yellow-300 rounded-full blur-2xl"></div>
            </div>
        </div>
    </div>
</x-app-layout>
