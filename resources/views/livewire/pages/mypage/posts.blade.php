<?php

use function Livewire\Volt\{state, computed};
use App\Models\Post;

state([
    'posts' => fn() => auth()
        ->user()
        ->posts()
        ->withCount(['reactions', 'comments'])
        ->latest()
        ->paginate(10),
]);

$deletePost = function ($postId) {
    $post = Post::findOrFail($postId);
    if ($post->user_id === auth()->id()) {
        $post->delete();
        $this->posts = auth()
            ->user()
            ->posts()
            ->withCount(['reactions', 'comments'])
            ->latest()
            ->paginate(10);
    }
};

?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">
                    投稿一覧
                </h2>

                @if ($posts->isEmpty())
                    <p class="text-gray-500 text-center py-4">まだ投稿がありません</p>
                @else
                    <div class="space-y-6">
                        @foreach ($posts as $post)
                            <div class="border-b pb-6 last:border-b-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <p class="text-gray-600">{{ $post->content }}</p>
                                    <button wire:click="deletePost({{ $post->id }})"
                                        wire:confirm="この投稿を削除してもよろしいですか？" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <div class="flex items-center justify-between mt-4">
                                    <span
                                        class="text-sm text-gray-500">{{ $post->posted_at->format('Y/m/d H:i') }}</span>
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
                        {{ $posts->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
