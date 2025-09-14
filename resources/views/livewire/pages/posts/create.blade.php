<?php

use function Livewire\Volt\{state};
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

state([
    'content' => '',
    'message' => null,
    'showSuccessAlert' => false,
]);

// $test = function () {
//     $this->message = 'テストボタンが動作しました！ユーザーID: ' . Auth::id();
// };

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

        // 成功アラートを表示
        $this->showSuccessAlert = true;
        $this->message = null; // メッセージをクリアして重複表示を防ぐ
        $this->content = '';

        // 3秒後にアラートを自動で非表示にする
        $this->dispatch('show-success-alert');
    } catch (\Exception $e) {
        $this->message = 'エラー: ' . $e->getMessage();
    }
};

$hideAlert = function () {
    $this->showSuccessAlert = false;
    $this->message = null; // メッセージも完全にクリアして赤い枠の表示を防ぐ
};

?>

<x-app-layout>
    <x-slot name="header">
        <div class="bg-gradient-to-r from-orange-50 to-yellow-50 border-b border-orange-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center py-4">
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
                            新しい投稿を作成
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-gradient-to-br from-orange-50 via-yellow-50 to-pink-50 py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <!-- 成功アラート -->
            @if ($showSuccessAlert)
                <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform scale-90"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => {
                        show = false;
                        $wire.call('hideAlert');
                    }, 3000)"
                    class="mb-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 text-green-700 p-4 rounded-2xl shadow-lg border border-green-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <!-- チェックマークアイコン -->
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">
                                🌅 投稿が完了しました！素晴らしい朝の始まりですね！
                            </p>
                        </div>
                        <div class="ml-auto pl-3">
                            <div class="-mx-1.5 -my-1.5">
                                <button wire:click="hideAlert"
                                    class="inline-flex bg-green-100 rounded-md p-1.5 text-green-500 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-100 focus:ring-green-600">
                                    <span class="sr-only">閉じる</span>
                                    <!-- X アイコン -->
                                    <svg class="h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-2xl border border-orange-100">
                <div class="p-8">
                    <div class="text-center mb-8">
                        <div
                            class="w-16 h-16 bg-gradient-to-r from-orange-400 to-yellow-400 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z" />
                            </svg>
                        </div>
                        <h2
                            class="text-2xl font-bold bg-gradient-to-r from-orange-600 to-yellow-600 bg-clip-text text-transparent mb-2">
                            Good Morning!</h2>
                        <p class="text-orange-600">今日の素晴らしい朝をみんなとシェアしましょう</p>
                    </div>

                    <!-- デバッグ情報 -->
                    {{-- <div class="mb-4 p-3 bg-yellow-100 border border-yellow-400 rounded text-black">
                    <h3 class="font-semibold text-yellow-800">デバッグ情報:</h3>
                    <p class="text-sm text-yellow-700">
                        ログイン状態: {{ Auth::check() ? 'ログイン済み' : '未ログイン' }}<br>
                        @if (Auth::check())
                            ユーザーID: {{ Auth::id() }}<br>
                            ユーザー名: {{ Auth::user()->name }}<br>
                            メールアドレス: {{ Auth::user()->email }}
                        @endif
                    </p>
                </div> --}}

                    <!-- エラーメッセージ表示（成功メッセージと成功アラートは除外） -->
                    {{-- @if (isset($this->message) && !empty($this->message) && !$showSuccessAlert && !str_contains($this->message, '投稿が完了'))
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 rounded text-black">
                        <p class="text-red-800">{{ $this->message }}</p>
                    </div>
                @endif --}}

                    <form wire:submit="save" class="space-y-6">
                        <div
                            class="bg-gradient-to-r from-white to-orange-50/30 rounded-2xl p-6 border border-orange-100/50">
                            <label for="content" class="block text-lg font-semibold text-orange-800 mb-4">
                                🌅 さぁ、朝を始めましょう。
                            </label>
                            <textarea wire:model="content" id="content" required rows="6"
                                class="block w-full rounded-xl border-orange-200 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50 bg-white/80 backdrop-blur-sm text-orange-700 placeholder-orange-400 text-lg"
                                placeholder="🌞 おはようございます！今日はどんな素晴らしい朝ですか？あなたの朝の感想をシェアしてください..."></textarea>
                        </div>

                        <div class="flex items-center justify-between mt-8">
                            <!-- マイページに戻るボタン -->
                            <button type="button" onclick="window.location.href='{{ route('mypage') }}'"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-100 to-yellow-100 hover:from-orange-200 hover:to-yellow-200 text-orange-700 font-medium rounded-full shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105 border border-orange-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                マイページに戻る
                            </button>

                            <button type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-orange-400 to-yellow-400 hover:from-orange-500 hover:to-yellow-500 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                投稿する
                            </button>
                        </div>
                    </form>
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
