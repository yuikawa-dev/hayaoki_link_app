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

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    起床時間: {{ $post->wake_up_time->format('H:i') }}
                                </span>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                                <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                                    ハッシュタグ: {{ $post->hashtags->pluck('name')->implode(', ') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if (auth()->id() === $post->user_id)
                        <div class="flex justify-end space-x-4">
                            <x-button.secondary href="{{ route('posts.edit', $post) }}">
                                編集
                            </x-button.secondary>
                            <x-danger-button wire:click="delete" wire:confirm="本当にこの投稿を削除しますか？">
                                削除
                            </x-danger-button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
