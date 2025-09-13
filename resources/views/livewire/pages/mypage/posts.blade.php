<?php

use function Livewire\Volt\{state, computed};
use App\Models\Post;
use Livewire\WithPagination;

state(['currentPage' => 1]);
state(['perPage' => 10]);

// ページ変更時のイベントハンドラ
$updatedCurrentPage = function ($value) {
    $this->currentPage = $value;
};

// 投稿削除後のイベントハンドラ
$postDeleted = function () {
    $this->currentPage = 1;
};

$posts = computed(function () {
    return auth()
        ->user()
        ->posts()
        ->withCount(['reactions', 'comments'])
        ->latest()
        ->paginate($this->perPage);
});

$deletePost = function ($postId) {
    $post = Post::findOrFail($postId);
    if ($post->user_id === auth()->id()) {
        $post->delete();
        $this->dispatch('post-deleted');
    }
};

?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        投稿一覧
                    </h2>
                    <a href="{{ route('mypage') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        マイページに戻る
                    </a>
                </div>

                @if ($this->posts->isEmpty())
                    <p class="text-gray-500 text-center py-4">まだ投稿がありません</p>
                @else
                    <div class="space-y-6">
                        @foreach ($this->posts as $post)
                            <div class="border-b pb-6 last:border-b-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <p class="text-gray-600 flex-1 mr-4">{{ $post->content }}</p>
                                    <button wire:click="deletePost({{ $post->id }})"
                                        wire:confirm="この投稿を削除してもよろしいですか？"
                                        class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 flex-shrink-0">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                        削除
                                    </button>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <span
                                        class="text-sm text-gray-500">{{ $post->created_at->format('Y/m/d H:i') }}</span>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-heart"></i> {{ $post->reactions_count }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-comment"></i> {{ $post->comments_count }}
                                        </span>
                                    </div>
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
