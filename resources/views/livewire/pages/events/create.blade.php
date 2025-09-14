<?php

use function Livewire\Volt\{state, mount, rules, with, uses};
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

// ファイルアップロード機能を有効化
uses([WithFileUploads::class]);

// 管理者チェック
mount(function () {
    if (!Auth::user()->isAdmin()) {
        abort(403, 'このページにアクセスする権限がありません。');
    }
});

// フォームの状態
state([
    'name' => '',
    'description' => '',
    'image' => null,
    'location' => '',
    'start_date' => '',
    'start_time' => '07:00',
    'end_date' => '',
    'end_time' => '09:00',
    'requirements' => '',
    'fee' => 0,
    'contact' => '',
    'capacity' => 10,
]);

// バリデーションルール
rules([
    'name' => 'required|string|max:255',
    'description' => 'required|string',
    'image' => 'nullable|image|max:2048',
    'location' => 'required|string|max:500',
    'start_date' => 'required|date|after_or_equal:today',
    'start_time' => 'required|date_format:H:i',
    'end_date' => 'required|date|after_or_equal:start_date',
    'end_time' => 'required|date_format:H:i',
    'requirements' => 'nullable|string',
    'fee' => 'required|integer|min:0',
    'contact' => 'required|string|max:50',
    'capacity' => 'required|integer|min:1|max:100',
]);

// イベントを登録
$createEvent = function () {
    $this->validate();

    // 開始日時と終了日時を組み合わせる
    $startDateTime = $this->start_date . ' ' . $this->start_time;
    $endDateTime = $this->end_date . ' ' . $this->end_time;

    // 終了時刻が開始時刻より後かチェック
    if (strtotime($endDateTime) <= strtotime($startDateTime)) {
        $this->addError('end_time', '終了時刻は開始時刻より後に設定してください。');
        return;
    }

    // 画像のアップロード処理
    $imagePath = null;
    if ($this->image) {
        $imagePath = $this->image->store('events', 'public');
    }

    Event::create([
        'name' => $this->name,
        'description' => $this->description,
        'image_path' => $imagePath,
        'location' => $this->location,
        'start_time' => $startDateTime,
        'end_time' => $endDateTime,
        'requirements' => $this->requirements,
        'fee' => $this->fee,
        'contact' => $this->contact,
        'capacity' => $this->capacity,
    ]);

    session()->flash('success', 'イベントを登録しました！');

    return redirect()->route('events.index');
};

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ページヘッダー -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3a1 1 0 012 0v4m0 0V3a1 1 0 012 0v4m0 0h4l-4 4-4-4h8z"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">朝活イベント登録</h1>
            <p class="text-lg text-gray-600">朝活イベントの情報を登録してください</p>
        </div>

        <!-- 登録フォーム -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-blue-100">
            <form wire:submit="createEvent" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- イベント名 -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 011 1v1a1 1 0 01-1 1v9a2 2 0 01-2 2H6a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h4z">
                                </path>
                            </svg>
                            イベント名 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" id="name" wire:model="name" placeholder="イベント名を入力してください"
                            class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                        @error('name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- イベント画像 -->
                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            イベント画像
                        </label>
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-blue-200 border-dashed rounded-xl hover:border-blue-300 transition-all duration-200">
                            <div class="space-y-1 text-center">
                                @if ($image)
                                    <div class="mb-4">
                                        <img src="{{ $image->temporaryUrl() }}" alt="プレビュー"
                                            class="mx-auto h-32 w-auto rounded-lg shadow-md">
                                    </div>
                                @else
                                    <svg class="mx-auto h-12 w-12 text-blue-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                @endif
                                <div class="flex text-sm text-gray-600">
                                    <label for="image"
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>画像をアップロード</span>
                                        <input id="image" name="image" type="file" wire:model="image"
                                            accept="image/*" class="sr-only">
                                    </label>
                                    <p class="pl-1">またはドラッグ&ドロップ</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF 最大2MB</p>
                            </div>
                        </div>
                        @error('image')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 開催場所 -->
                    <div class="md:col-span-2">
                        <label for="location" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            開催場所 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" id="location" wire:model="location" placeholder="開催場所を入力してください"
                            class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                        @error('location')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 開始日時 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3a1 1 0 012 0v4m0 0V3a1 1 0 012 0v4m0 0h4l-4 4-4-4h8z"></path>
                            </svg>
                            開始日時 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="space-y-3">
                            <input type="date" wire:model="start_date"
                                class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                            <select wire:model="start_time"
                                class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                                <option value="05:00">5:00</option>
                                <option value="05:30">5:30</option>
                                <option value="06:00">6:00</option>
                                <option value="06:30">6:30</option>
                                <option value="07:00">7:00</option>
                                <option value="07:30">7:30</option>
                                <option value="08:00">8:00</option>
                                <option value="08:30">8:30</option>
                                <option value="09:00">9:00</option>
                                <option value="09:30">9:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                            </select>
                        </div>
                        @error('start_date')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        @error('start_time')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 終了日時 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            終了日時 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="space-y-3">
                            <input type="date" wire:model="end_date"
                                class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                            <select wire:model="end_time"
                                class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                                <option value="06:00">6:00</option>
                                <option value="06:30">6:30</option>
                                <option value="07:00">7:00</option>
                                <option value="07:30">7:30</option>
                                <option value="08:00">8:00</option>
                                <option value="08:30">8:30</option>
                                <option value="09:00">9:00</option>
                                <option value="09:30">9:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="12:00">12:00</option>
                                <option value="12:30">12:30</option>
                                <option value="13:00">13:00</option>
                                <option value="13:30">13:30</option>
                                <option value="14:00">14:00</option>
                            </select>
                        </div>
                        @error('end_date')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        @error('end_time')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 参加費 -->
                    <div>
                        <label for="fee"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            参加費 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="fee" wire:model="fee" min="0" placeholder="0"
                                class="w-full px-4 py-3 pr-12 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">円</span>
                        </div>
                        @error('fee')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 定員 -->
                    <div>
                        <label for="capacity"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            定員 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="capacity" wire:model="capacity" min="1"
                                max="100" placeholder="10"
                                class="w-full px-4 py-3 pr-12 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500">名</span>
                        </div>
                        @error('capacity')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 連絡先 -->
                    <div class="md:col-span-2">
                        <label for="contact"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            連絡先 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" id="contact" wire:model="contact"
                            placeholder="電話番号またはメールアドレスを入力してください"
                            class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                        @error('contact')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- イベント説明 -->
                    <div class="md:col-span-2">
                        <label for="description"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            イベント説明 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <textarea id="description" wire:model="description" rows="4" placeholder="イベントの詳細や内容を入力してください"
                            class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900 resize-none"></textarea>
                        @error('description')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 参加条件・注意事項 -->
                    <div class="md:col-span-2">
                        <label for="requirements"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            参加条件・注意事項
                        </label>
                        <textarea id="requirements" wire:model="requirements" rows="3" placeholder="参加条件や注意事項があれば入力してください（任意）"
                            class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900 resize-none"></textarea>
                        @error('requirements')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- アクションボタン -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('events.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        戻る
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3a1 1 0 012 0v4m0 0V3a1 1 0 012 0v4m0 0h4l-4 4-4-4h8z"></path>
                        </svg>
                        イベントを登録する
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
