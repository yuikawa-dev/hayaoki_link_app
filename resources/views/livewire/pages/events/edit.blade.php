<?php

use function Livewire\Volt\{state, mount, rules, with, uses};
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

uses([WithFileUploads::class]);

// 管理者チェック
mount(function ($event) {
    if (!Auth::user()->isAdmin()) {
        abort(403, 'このページにアクセスする権限がありません。');
    }

    // イベントの詳細を取得
    $eventModel = Event::findOrFail($event);

    $this->event = $eventModel;
    $this->name = $eventModel->name;
    $this->description = $eventModel->description;
    $this->location = $eventModel->location;
    $this->contact = $eventModel->contact;
    $this->requirements = $eventModel->requirements;
    $this->fee = $eventModel->fee;
    $this->capacity = $eventModel->capacity;
    $this->start_time = $eventModel->start_time->format('Y-m-d\TH:i');
    $this->end_time = $eventModel->end_time->format('Y-m-d\TH:i');
});

// フォームの状態
state([
    'event' => null,
    'name' => '',
    'description' => '',
    'location' => '',
    'contact' => '',
    'requirements' => '',
    'fee' => 0,
    'capacity' => 10,
    'start_time' => '',
    'end_time' => '',
    'image' => null,
]);

// バリデーションルール
rules([
    'name' => 'required|string|max:255',
    'description' => 'required|string',
    'location' => 'required|string|max:500',
    'contact' => 'required|string|max:50',
    'requirements' => 'nullable|string',
    'fee' => 'required|integer|min:0',
    'capacity' => 'required|integer|min:1',
    'start_time' => 'required|date|after:now',
    'end_time' => 'required|date|after:start_time',
    'image' => 'nullable|image|max:10240',
]);

// イベントを更新
$updateEvent = function () {
    $this->validate();

    $updateData = [
        'name' => $this->name,
        'description' => $this->description,
        'location' => $this->location,
        'contact' => $this->contact,
        'requirements' => $this->requirements,
        'fee' => $this->fee,
        'capacity' => $this->capacity,
        'start_time' => $this->start_time,
        'end_time' => $this->end_time,
    ];

    // 画像がアップロードされた場合の処理
    if ($this->image) {
        // 既存の画像を削除
        if ($this->event->image_path && Storage::disk('public')->exists($this->event->image_path)) {
            Storage::disk('public')->delete($this->event->image_path);
        }

        // 新しい画像を保存
        $imagePath = $this->image->store('events', 'public');
        $updateData['image_path'] = $imagePath;
    }

    // イベント情報を更新
    $this->event->update($updateData);

    session()->flash('success', 'イベントを更新しました。');

    return redirect()->route('events.show', $this->event->id);
};

// 画像を削除
$deleteImage = function () {
    if ($this->event->image_path && Storage::disk('public')->exists($this->event->image_path)) {
        Storage::disk('public')->delete($this->event->image_path);
    }

    $this->event->update(['image_path' => null]);

    session()->flash('success', '画像を削除しました。');
};

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- 戻るボタン -->
        <div class="mb-6">
            <a href="{{ route('events.show', $event->id) }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                イベント詳細に戻る
            </a>
        </div>

        <!-- フォーム -->
        <form wire:submit="updateEvent" class="bg-white rounded-2xl shadow-xl border border-orange-100 overflow-hidden">
            <!-- ヘッダー -->
            <div class="bg-gradient-to-r from-orange-400 via-amber-500 to-yellow-500 px-8 py-6">
                <h1 class="text-2xl font-bold text-white flex items-center">
                    <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    イベントを編集
                </h1>
            </div>

            <div class="p-8 space-y-8">
                <!-- 基本情報 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- イベント名 -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-black mb-2">
                            イベント名 <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="name" type="text" id="name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 text-black"
                            placeholder="イベント名を入力してください">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 開始日時 -->
                    <div>
                        <label for="start_time" class="block text-sm font-semibold text-black mb-2">
                            開始日時 <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="start_time" type="datetime-local" id="start_time" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 text-black">
                        @error('start_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 終了日時 -->
                    <div>
                        <label for="end_time" class="block text-sm font-semibold text-black mb-2">
                            終了日時 <span class="text-red-500">*</span>
                        </label>
                        <input wire:model="end_time" type="datetime-local" id="end_time" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 text-black">
                        @error('end_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 参加費 -->
                    <div>
                        <label for="fee" class="block text-sm font-semibold text-black mb-2">
                            参加費 <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">¥</span>
                            <input wire:model="fee" type="number" id="fee" required min="0"
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 text-black"
                                placeholder="0">
                        </div>
                        @error('fee')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 定員 -->
                    <div>
                        <label for="capacity" class="block text-sm font-semibold text-black mb-2">
                            定員 <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input wire:model="capacity" type="number" id="capacity" required min="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 text-black"
                                placeholder="10">
                            <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">名</span>
                        </div>
                        @error('capacity')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- 開催場所 -->
                <div>
                    <label for="location" class="block text-sm font-semibold text-black mb-2">
                        開催場所 <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="location" type="text" id="location" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 text-black"
                        placeholder="開催場所を入力してください">
                    @error('location')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 連絡先 -->
                <div>
                    <label for="contact" class="block text-sm font-semibold text-black mb-2">
                        連絡先 <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="contact" type="text" id="contact" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 text-black"
                        placeholder="連絡先を入力してください（電話番号など）">
                    @error('contact')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- イベント説明 -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-black mb-2">
                        イベント説明 <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="description" id="description" rows="6" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 resize-none text-black"
                        placeholder="イベントの詳細な説明を入力してください"></textarea>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 参加条件・注意事項 -->
                <div>
                    <label for="requirements" class="block text-sm font-semibold text-black mb-2">
                        参加条件・注意事項
                    </label>
                    <textarea wire:model="requirements" id="requirements" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-300 resize-none text-black"
                        placeholder="参加条件や注意事項があれば入力してください（任意）"></textarea>
                    @error('requirements')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 画像アップロード -->
                <div>
                    <label class="block text-sm font-semibold text-black mb-2">
                        イベント画像
                    </label>

                    <!-- 既存画像の表示 -->
                    @if ($event->hasImage())
                        <div class="mb-4">
                            <div class="relative inline-block">
                                <img src="{{ $event->getImageUrl() }}" alt="{{ $event->name }}"
                                    class="w-32 h-32 object-cover rounded-xl shadow-md">
                                <button type="button" wire:click="deleteImage" wire:confirm="この画像を削除しますか？"
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors duration-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-black mt-2">現在の画像</p>
                        </div>
                    @endif

                    <!-- 新しい画像のアップロード -->
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-orange-400 transition-colors duration-300">
                        <input wire:model="image" type="file" id="image" accept="image/*" class="hidden">
                        <label for="image" class="cursor-pointer">
                            <div class="space-y-2">
                                <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <div class="text-black">
                                    <span
                                        class="font-semibold text-orange-600 hover:text-orange-500">クリックして画像を選択</span>
                                    <p class="text-sm">PNG, JPG, GIF (最大10MB)</p>
                                </div>
                            </div>
                        </label>
                    </div>

                    @if ($image)
                        <div class="mt-4">
                            <img src="{{ $image->temporaryUrl() }}" alt="プレビュー"
                                class="w-32 h-32 object-cover rounded-xl shadow-md">
                            <p class="text-sm text-black mt-2">新しい画像のプレビュー</p>
                        </div>
                    @endif

                    @error('image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- ボタン -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="flex-1 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            更新する
                        </div>
                    </button>
                    <a href="{{ route('events.show', $event->id) }}"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-4 px-8 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-center">
                        <div class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            キャンセル
                        </div>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
