<?php

use function Livewire\Volt\{state, mount, computed};
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

state(['currentPage' => 1]);
state(['perPage' => 5]);

// ページ変更時のイベントハンドラ
$updatedCurrentPage = function ($value) {
    $this->currentPage = $value;
};

// 投稿削除後のイベントハンドラ
$postDeleted = function () {
    $this->currentPage = 1;
};

$posts = computed(function () {
    return Post::with(['user', 'comments'])
        ->withCount('comments')
        ->latest()
        ->paginate($this->perPage);
});

public function deletePost(Post $post) {
    if (Auth::id() !== $post->user_id) {
        return;
    }
    $post->delete();
    $this->dispatch('post-deleted');
};
?>

<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h1 class="text-2xl font-semibold mb-6">最近の投稿</h1>

                @if ($this->posts->isEmpty())
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                        まだ投稿がありません。
                    </p>
                @else
                    <div class="space-y-6">
                        @foreach ($this->posts as $post)
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow p-6">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $post->user->profile_image_url }}" alt="{{ $post->user->name }}"
                                            class="w-10 h-10 rounded-full">
                                        <div>
                                            <h3 class="font-semibold">{{ $post->user->name }}</h3>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $post->created_at->format('Y/m/d H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                    @if (Auth::id() === $post->user_id)
                                        <button wire:click="deletePost({{ $post->id }})"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                <div class="mt-4">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $post->content }}</p>
                                </div>

                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            コメント: {{ $post->comments_count }}
                                        </span>
                                    </div>
                                    <a href="{{ route('posts.show', $post) }}"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        詳細を見る
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $this->posts->links(data: ['current-page' => $this->currentPage]) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
