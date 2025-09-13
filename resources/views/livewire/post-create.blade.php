<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Good Morning!</h2>

                <!-- エラーメッセージ表示 -->
                @if ($message)
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
