<?php

use function Livewire\Volt\{state, layout};
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

layout('layouts.app');

state([
    'content' => '',
    'message' => null,
]);

$test = function () {
    $this->message = 'テストボタンが動作しました！ユーザーID: ' . Auth::id();
};

$save = function () {
    if (empty($this->content)) {
        $this->message = 'エラー: 内容を入力してください';
        return;
    }

    try {
        $post = Post::create([
            'user_id' => Auth::id(),
            'content' => $this->content,
        ]);

        $this->message = '投稿が作成されました！投稿ID: ' . $post->id;
        $this->content = '';
    } catch (\Exception $e) {
        $this->message = 'エラー: ' . $e->getMessage();
    }
};

?>

<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Good Morning!</h2>

                <!-- デバッグ情報 -->
                <div class="mb-4 p-3 bg-yellow-100 border border-yellow-400 rounded text-black">
                    <h3 class="font-semibold text-yellow-800">デバッグ情報:</h3>
                    <p class="text-sm text-yellow-700">
                        ログイン状態: {{ Auth::check() ? 'ログイン済み' : '未ログイン' }}<br>
                        @if (Auth::check())
                            ユーザーID: {{ Auth::id() }}<br>
                            ユーザー名: {{ Auth::user()->name }}<br>
                            メールアドレス: {{ Auth::user()->email }}
                        @endif
                    </p>
                </div>

                <!-- メッセージ表示 -->
                @if (isset($this->message) && !empty($this->message))
                    <div class="mb-4 p-3 bg-blue-100 border border-blue-400 rounded text-black">
                        <p class="text-blue-800">{{ $this->message }}</p>
                    </div>
                @endif

                <form wire:submit="save" class="space-y-4">
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700">
                            さぁ、朝を始めましょう。
                        </label>
                        <textarea wire:model="content" id="content" required rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black"
                            placeholder="おはようございます。今日はどんな朝ですか？"></textarea>
                    </div>

                    <div class="flex items-center justify-between">
                        <!-- テストボタン -->
                        <button type="button" wire:click="test"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            テスト
                        </button>

                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            投稿
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
