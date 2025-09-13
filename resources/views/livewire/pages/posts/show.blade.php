<?php

use function Livewire\Volt\{state};
use App\Models\Post;

state(['post' => fn() => Post::findOrFail(request()->route('post'))]);

?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            投稿詳細
        </h2>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
