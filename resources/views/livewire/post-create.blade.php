<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

        <!-- 成功アラート -->
        @if ($showSuccessAlert)
            <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90" x-init="setTimeout(() => { show = false;
                    $wire.call('hideAlert'); }, 3000)"
                class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-md shadow-md">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <!-- チェックマークアイコン -->
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">
                            🎉 投稿が完了しました！
                        </p>
                        <p class="text-xs text-green-600 mt-1">
                            投稿がデータベースに保存されました。
                        </p>
                        <div class="mt-2">
                            <a href="{{ route('mypage.posts') }}"
                                class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded-md hover:bg-green-700 transition duration-150 ease-in-out">
                                マイページで確認 →
                            </a>
                        </div>
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

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Good Morning!</h2>

                <!-- エラーメッセージ表示 -->
                @if ($message && !$showSuccessAlert)
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 rounded text-black">
                        <p class="text-red-800">{{ $message }}</p>
                    </div>
                @endif

                <form wire:submit="save" class="space-y-4">
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700">
                            さぁ、朝を始めましょう。
                        </label>
                        <div class="mt-1 relative">
                            <textarea wire:model.live="content" id="content" required rows="4"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black"
                                placeholder="おはようございます。今日はどんな朝ですか？"></textarea>

                            <!-- 文字数カウンター -->
                            <div class="flex justify-between items-center mt-2 text-sm">
                                <div class="text-gray-500">
                                    @if ($this->characterCount > 0)
                                        {{ $this->characterCount }}/1000文字
                                    @endif
                                </div>
                                <div class="text-right">
                                    @if ($this->remainingCharacters < 100)
                                        <span
                                            class="{{ $this->remainingCharacters < 0 ? 'text-red-500' : 'text-yellow-500' }}">
                                            残り{{ $this->remainingCharacters }}文字
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" {{ $this->remainingCharacters < 0 ? 'disabled' : '' }}
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                            投稿
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
