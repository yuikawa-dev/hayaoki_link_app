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
        <div class="flex items-center">
            <a href="{{ route('posts.index') }}" class="mr-4">
                <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                投稿詳細
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                @if ($post->user->profile_photo_url)
                                    <img class="h-10 w-10 rounded-full" src="{{ $post->user->profile_photo_url }}"
                                        alt="{{ $post->user->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">{{ substr($post->user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ $post->user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $post->created_at->format('Y年m月d日 H:i') }}
                                </div>
                            </div>
                        </div>

                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            {{ $post->title }}
                        </h1>

                        <div class="prose dark:prose-invert max-w-none mb-6">
                            {!! nl2br(e($post->content)) !!}
                        </div>

                        <!-- いいねボタン -->
                        <div class="flex items-center space-x-4 mb-6">
                            <button wire:click="toggleLike"
                                class="flex items-center space-x-2 text-gray-500 hover:text-red-500 transition-colors duration-200">
                                <svg class="w-6 h-6 {{ $post->likes()->where('user_id', auth()->id())->exists()? 'text-red-500 fill-current': '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                    </path>
                                </svg>
                                <span>{{ $post->likes()->count() }}</span>
                            </button>
                        </div>

                        <!-- コメントフォーム -->
                        <div class="mb-6">
                            <form wire:submit.prevent="addComment">
                                <div class="mb-4">
                                    <textarea wire:model.live="newComment"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300"
                                        rows="3" placeholder="コメントを入力してください"></textarea>
                                    @error('newComment')
                                        <span class="text-red-500 text-sm">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="flex justify-end">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition">
                                        コメントする
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- コメント一覧 -->
                        <div class="space-y-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">コメント</h3>
                            @foreach ($post->comments()->with('user')->latest()->get() as $comment)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            @if ($comment->user->profile_photo_url)
                                                <img class="h-8 w-8 rounded-full"
                                                    src="{{ $comment->user->profile_photo_url }}"
                                                    alt="{{ $comment->user->name }}">
                                            @else
                                                <div
                                                    class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                    <span
                                                        class="text-gray-500">{{ substr($comment->user->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $comment->user->name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $comment->created_at->format('Y年m月d日 H:i') }}</div>
                                            </div>
                                            <div class="mt-1 text-sm text-gray-700 dark:text-gray-300">
                                                {!! nl2br(e($comment->content)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
