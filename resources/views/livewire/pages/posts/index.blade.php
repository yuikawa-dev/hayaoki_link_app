<?php

use function Livewire\Volt\{state, mount, computed};
use App\Models\Post;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

state(['currentPage' => 1]);
state(['perPage' => 5]);
state(['refreshCounter' => 0]); // リフレッシュ用カウンター

// ページ変更時のイベントハンドラ
$updatedCurrentPage = function ($value) {
    $this->currentPage = $value;
};

// 投稿削除後のイベントハンドラ
$postDeleted = function () {
    $this->currentPage = 1;
};

$posts = computed(function () {
    // refreshCounterを参照して強制的に再計算させる
    $this->refreshCounter;

    $posts = Post::with(['user', 'comments', 'likes'])
        ->withCount(['comments', 'likes'])
        ->latest()
        ->paginate($this->perPage);

    // 各投稿に対して現在のユーザーのいいね状態を追加
    foreach ($posts as $post) {
        $post->is_liked_by_user = $post
            ->likes()
            ->where('user_id', auth()->id())
            ->exists();
    }

    return $posts;
});

$deletePost = function (Post $post) {
    if (Auth::id() !== $post->user_id) {
        return;
    }
    $post->delete();
    $this->dispatch('post-deleted');
};

// いいね機能
$toggleLike = function (Post $post) {
    $userId = auth()->id();
    $postId = $post->id;

    try {
        DB::transaction(function () use ($userId, $postId) {
            // 現在のいいね状態を確認
            $likeExists = Like::where('user_id', $userId)->where('post_id', $postId)->exists();

            if ($likeExists) {
                // いいね済みの場合は削除（WHERE条件で安全に削除）
                Like::where('user_id', $userId)->where('post_id', $postId)->delete();
            } else {
                // いいねしていない場合は新規作成
                // firstOrCreateを使用して安全に作成
                Like::firstOrCreate([
                    'user_id' => $userId,
                    'post_id' => $postId,
                ]);
            }
        });

        // いいね処理後、投稿データを再読み込みしてLivewireの状態を更新
        $this->refreshCounter++;
    } catch (\Exception $e) {
        // エラーが発生した場合はログに記録
        \Log::error('いいね処理でエラーが発生しました: ' . $e->getMessage(), [
            'user_id' => $userId,
            'post_id' => $postId,
            'trace' => $e->getTraceAsString(),
        ]);

        // ユーザーには何も表示せず、静かに失敗させる
        return;
    }
};
?>

<div class="min-h-screen bg-gradient-to-br from-orange-50 via-yellow-50 to-pink-50">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-2xl border border-orange-100">
            <div class="p-8 text-gray-800">
                <!-- ヘッダー部分 -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-orange-400 to-yellow-400 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                            </svg>
                        </div>
                        <div>
                            <h1
                                class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-yellow-600 bg-clip-text text-transparent">
                                みんなの朝
                            </h1>
                            <p class="text-sm text-orange-600 font-medium">今日も1日頑張ろう！</p>
                        </div>
                    </div>
                    <a href="{{ route('mypage') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-400 to-yellow-400 hover:from-orange-500 hover:to-yellow-500 text-white font-medium rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        マイページに戻る
                    </a>
                </div>

                @if ($this->posts->isEmpty())
                    <div class="text-center py-16">
                        <div
                            class="w-24 h-24 bg-gradient-to-r from-orange-200 to-yellow-200 rounded-full mx-auto mb-6 flex items-center justify-center">
                            <svg class="w-12 h-12 text-orange-400" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                            </svg>
                        </div>
                        <p class="text-orange-600 text-lg font-medium mb-2">
                            まだ朝の投稿がありません
                        </p>
                        <p class="text-orange-500">
                            みんなの素敵な朝をシェアしてみませんか？
                        </p>
                    </div>
                @else
                    <div class="space-y-8">
                        @foreach ($this->posts as $post)
                            <div
                                class="bg-gradient-to-r from-white to-orange-50/30 rounded-2xl shadow-md hover:shadow-lg transition-all duration-300 p-6 border border-orange-100/50">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="relative">
                                            <img src="{{ $post->user->profile_image_url }}"
                                                alt="{{ $post->user->name }}"
                                                class="w-12 h-12 rounded-full ring-2 ring-orange-200 ring-offset-2">
                                            <div
                                                class="absolute -bottom-1 -right-1 w-4 h-4 bg-gradient-to-r from-orange-400 to-yellow-400 rounded-full border-2 border-white">
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-800 text-lg">{{ $post->user->name }}</h3>
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-orange-400" fill="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                                </svg>
                                                <p class="text-sm text-orange-600 font-medium">
                                                    {{ $post->created_at->format('Y/m/d H:i') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @if (Auth::id() === $post->user_id)
                                        <button wire:click="deletePost({{ $post->id }})"
                                            class="text-red-400 hover:text-red-600 hover:bg-red-50 p-2 rounded-full transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                <div class="mt-6 bg-white/60 rounded-xl p-4 border border-orange-100/50">
                                    <p class="text-gray-700 leading-relaxed">{{ $post->content }}</p>
                                </div>

                                <div class="mt-6 flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <!-- いいねボタン -->
                                        <button wire:click="toggleLike({{ $post->id }})"
                                            class="flex items-center space-x-2 px-3 py-1 bg-gradient-to-r {{ $post->is_liked_by_user ? 'from-red-400 to-pink-400 text-white' : 'from-orange-100 to-yellow-100 text-orange-600 hover:from-red-100 hover:to-pink-100 hover:text-red-600' }} rounded-full shadow-sm hover:shadow-md transition-all duration-200 transform hover:scale-105">
                                            <svg class="w-4 h-4 {{ $post->is_liked_by_user ? 'fill-current' : '' }}"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                                </path>
                                            </svg>
                                            <span class="text-sm font-medium">{{ $post->likes_count }}</span>
                                        </button>

                                        <!-- コメント数 -->
                                        <div
                                            class="flex items-center space-x-2 bg-orange-100/50 px-3 py-1 rounded-full">
                                            <svg class="w-4 h-4 text-orange-500" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path
                                                    d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.068.157 2.148.279 3.238.364.466.037.893.281 1.153.671L12 21l2.652-3.978c.26-.39.687-.634 1.153-.67 1.09-.086 2.17-.208 3.238-.365 1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                                            </svg>
                                            <span class="text-sm text-orange-700 font-medium">
                                                {{ $post->comments_count }} コメント
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('posts.show', $post) }}"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-400 to-yellow-400 hover:from-orange-500 hover:to-yellow-500 text-white font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                        <span>詳細を見る</span>
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- ページネーション -->
                    <div class="mt-8 flex justify-center">
                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-md p-4 border border-orange-100">
                            {{ $this->posts->links(data: ['current-page' => $this->currentPage]) }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- デコレーション要素 -->
        <div class="fixed top-10 right-10 opacity-20 pointer-events-none">
            <div class="w-32 h-32 bg-gradient-to-r from-yellow-300 to-orange-300 rounded-full blur-2xl"></div>
        </div>
        <div class="fixed bottom-10 left-10 opacity-20 pointer-events-none">
            <div class="w-24 h-24 bg-gradient-to-r from-pink-300 to-yellow-300 rounded-full blur-2xl"></div>
        </div>
    </div>
</div>
