<?php

use function Livewire\Volt\{state, mount, rules, with, uses};
use App\Models\Shop;
use App\Models\ShopImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

uses([WithFileUploads::class]);

// 管理者チェック
mount(function ($shop) {
    if (!Auth::user()->isAdmin()) {
        abort(403, 'このページにアクセスする権限がありません。');
    }

    // お店の詳細を取得
    $shopModel = Shop::with('images')->findOrFail($shop);

    $this->shop = $shopModel;
    $this->name = $shopModel->name;
    $this->description = $shopModel->description;
    $this->address = $shopModel->address;
    $this->contact = $shopModel->contact;
    $this->sns_link = $shopModel->sns_links['sns'] ?? '';
    $this->opening_time = \Carbon\Carbon::parse($shopModel->opening_time)->format('H:i');
    $this->closing_time = \Carbon\Carbon::parse($shopModel->closing_time)->format('H:i');

    // 既存の画像を表示用に設定
    $this->existingImages = $shopModel->images;
});

// フォームの状態
state([
    'shop' => null,
    'name' => '',
    'description' => '',
    'address' => '',
    'contact' => '',
    'sns_link' => '',
    'opening_time' => '06:00',
    'closing_time' => '21:00',
    'images' => [],
    'imageTypes' => [],
    'existingImages' => collect(),
    'imagesToDelete' => [],
]);

// バリデーションルール
rules([
    'name' => 'required|string|max:255',
    'description' => 'required|string',
    'address' => 'required|string|max:500',
    'contact' => 'required|string|max:50',
    'sns_link' => 'nullable|string|max:255',
    'opening_time' => 'required|date_format:H:i',
    'closing_time' => 'required|date_format:H:i|after:opening_time',
    'images.*' => 'nullable|image|max:10240',
    'imageTypes.*' => 'nullable|string|in:exterior,interior,menu,atmosphere',
]);

// 画像を追加
$addImage = function () {
    $this->images[] = null;
    $this->imageTypes[] = 'exterior';
};

// 新しい画像を削除
$removeImage = function ($index) {
    array_splice($this->images, $index, 1);
    array_splice($this->imageTypes, $index, 1);
};

// 既存の画像を削除マークする
$markImageForDeletion = function ($imageId) {
    if (!in_array($imageId, $this->imagesToDelete)) {
        $this->imagesToDelete[] = $imageId;
    }
};

// 既存の画像の削除マークを取り消す
$unmarkImageForDeletion = function ($imageId) {
    $this->imagesToDelete = array_filter($this->imagesToDelete, function ($id) use ($imageId) {
        return $id !== $imageId;
    });
};

// お店を更新
$updateShop = function () {
    $this->validate();

    $snsLinks = [];
    if ($this->sns_link) {
        $snsLinks['sns'] = $this->sns_link;
    }

    // お店情報を更新
    $this->shop->update([
        'name' => $this->name,
        'description' => $this->description,
        'address' => $this->address,
        'contact' => $this->contact,
        'sns_links' => $snsLinks,
        'opening_time' => $this->opening_time,
        'closing_time' => $this->closing_time,
    ]);

    // 削除マークされた画像を削除
    foreach ($this->imagesToDelete as $imageId) {
        $image = ShopImage::find($imageId);
        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }

    // 新しい画像を保存
    foreach ($this->images as $index => $image) {
        if ($image) {
            $path = $image->store('shop-images', 'public');

            ShopImage::create([
                'shop_id' => $this->shop->id,
                'image_path' => $path,
                'image_type' => $this->imageTypes[$index] ?? 'exterior',
            ]);
        }
    }

    session()->flash('success', 'お店の情報を更新しました！');

    return redirect()->route('shops.show', $this->shop);
};

// 画像タイプの選択肢を提供
with([
    'availableImageTypes' => [
        'exterior' => '外観',
        'interior' => '内装',
        'menu' => 'メニュー',
        'atmosphere' => '雰囲気',
    ],
]);

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ページヘッダー -->
        <div class="text-center mb-8">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                    </path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">お店情報編集</h1>
            <p class="text-lg text-gray-600">{{ $shop->name }}の情報を編集してください</p>
        </div>

        <!-- 編集フォーム -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-orange-100">
            <form wire:submit="updateShop" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- 店舗名 -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            店舗名 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" id="name" wire:model="name" placeholder="店舗名を入力してください"
                            class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                        @error('name')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 住所 -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            住所 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" id="address" wire:model="address" placeholder="住所を入力してください"
                            class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                        @error('address')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 連絡先 -->
                    <div>
                        <label for="contact" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            連絡先 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" id="contact" wire:model="contact" placeholder="電話番号を入力してください"
                            class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                        @error('contact')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 営業時間 -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            営業時間 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="flex items-center space-x-3">
                            <select wire:model="opening_time"
                                class="flex-1 px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
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
                            </select>
                            <span class="text-gray-500 font-medium">〜</span>
                            <select wire:model="closing_time"
                                class="flex-1 px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="12:00">12:00</option>
                                <option value="12:30">12:30</option>
                                <option value="13:00">13:00</option>
                                <option value="13:30">13:30</option>
                                <option value="14:00">14:00</option>
                                <option value="14:30">14:30</option>
                                <option value="15:00">15:00</option>
                                <option value="15:30">15:30</option>
                                <option value="16:00">16:00</option>
                                <option value="16:30">16:30</option>
                                <option value="17:00">17:00</option>
                                <option value="17:30">17:30</option>
                                <option value="18:00">18:00</option>
                                <option value="18:30">18:30</option>
                                <option value="19:00">19:00</option>
                                <option value="19:30">19:30</option>
                                <option value="20:00">20:00</option>
                                <option value="20:30">20:30</option>
                                <option value="21:00">21:00</option>
                                <option value="21:30">21:30</option>
                                <option value="22:00">22:00</option>
                                <option value="22:30">22:30</option>
                                <option value="23:00">23:00</option>
                                <option value="23:30">23:30</option>
                                <option value="00:00">24:00</option>
                                <option value="00:30">24:30</option>
                                <option value="01:00">25:00</option>
                                <option value="01:30">25:30</option>
                                <option value="02:00">26:00</option>
                                <option value="02:30">26:30</option>
                                <option value="03:00">27:00</option>
                                <option value="03:30">27:30</option>
                                <option value="04:00">28:00</option>
                                <option value="04:30">28:30</option>
                                <option value="05:00">29:00</option>
                                <option value="05:30">29:30</option>
                                <option value="06:00">30:00</option>
                            </select>
                        </div>
                        @error('opening_time')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        @error('closing_time')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 店舗説明 -->
                    <div class="md:col-span-2">
                        <label for="description"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            店舗説明 <span class="text-red-500 ml-1">*</span>
                        </label>
                        <textarea id="description" wire:model="description" rows="4" placeholder="お店の特徴や朝活におすすめのポイントを入力してください"
                            class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900 resize-none"></textarea>
                        @error('description')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- SNSリンク -->
                    <div class="md:col-span-2">
                        <label for="sns_link"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                </path>
                            </svg>
                            SNSリンク
                        </label>
                        <input type="url" id="sns_link" wire:model="sns_link"
                            placeholder="https://twitter.com/username または https://instagram.com/username など"
                            class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                        @error('sns_link')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 既存の画像 -->
                    @if ($existingImages->count() > 0)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                既存の画像
                            </label>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach ($existingImages as $image)
                                    <div class="relative group">
                                        <div
                                            class="border-2 rounded-xl overflow-hidden {{ in_array($image->id, $imagesToDelete) ? 'border-red-300 opacity-50' : 'border-gray-200' }}">
                                            <img src="{{ $image->image_url }}" alt="{{ $shop->name }}"
                                                class="w-full h-32 object-cover">
                                            <div class="p-2 bg-gray-50">
                                                <p class="text-xs text-gray-600 text-center">
                                                    {{ \App\Models\ShopImage::$availableTypes[$image->image_type] ?? $image->image_type }}
                                                </p>
                                            </div>
                                        </div>

                                        @if (in_array($image->id, $imagesToDelete))
                                            <div
                                                class="absolute inset-0 bg-red-500 bg-opacity-20 flex items-center justify-center">
                                                <span class="text-red-600 font-semibold text-sm">削除予定</span>
                                            </div>
                                            <button type="button"
                                                wire:click="unmarkImageForDeletion({{ $image->id }})"
                                                class="absolute top-2 right-2 w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-full flex items-center justify-center shadow-lg transition-all duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                    </path>
                                                </svg>
                                            </button>
                                        @else
                                            <button type="button"
                                                wire:click="markImageForDeletion({{ $image->id }})"
                                                class="absolute top-2 right-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow-lg transition-all duration-200 opacity-0 group-hover:opacity-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- 新しい画像を追加 -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            新しい画像を追加
                        </label>

                        <!-- 画像アップロードエリア -->
                        <div class="space-y-4">
                            @forelse($images as $index => $image)
                                <div
                                    class="border-2 border-dashed border-orange-200 rounded-xl p-6 bg-orange-50/50 hover:border-orange-300 transition-all duration-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                        <!-- 画像タイプ選択 -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">画像の種類</label>
                                            <select wire:model="imageTypes.{{ $index }}"
                                                class="w-full px-3 py-2 border-2 border-orange-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 text-gray-900">
                                                @foreach ($availableImageTypes as $key => $label)
                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- 画像選択 -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">画像ファイル</label>
                                            <input type="file" wire:model="images.{{ $index }}"
                                                accept="image/*"
                                                class="w-full px-3 py-2 border-2 border-orange-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 text-gray-900 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                                        </div>

                                        <!-- 削除ボタン -->
                                        <div>
                                            <button type="button" wire:click="removeImage({{ $index }})"
                                                class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition-all duration-200 transform hover:scale-105">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                削除
                                            </button>
                                        </div>
                                    </div>

                                    @if ($image)
                                        <div class="mt-4">
                                            <div class="text-sm text-gray-600 mb-2">プレビュー:</div>
                                            <img src="{{ $image->temporaryUrl() }}" alt="プレビュー"
                                                class="h-24 w-24 object-cover rounded-lg border-2 border-orange-200">
                                        </div>
                                    @endif

                                    @error('images.' . $index)
                                        <div class="text-red-500 text-sm mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            @empty
                                <div
                                    class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center bg-gray-50/50">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="text-gray-600 mb-4">新しい画像を追加する場合は下のボタンを押してください</p>
                                </div>
                            @endforelse

                            <!-- 画像追加ボタン -->
                            <div class="text-center">
                                <button type="button" wire:click="addImage"
                                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-400 to-amber-400 hover:from-orange-500 hover:to-amber-500 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-md hover:shadow-lg">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    画像を追加
                                </button>
                            </div>

                            <div class="text-sm text-gray-500 text-center">
                                <p>※ 1つのファイルにつき最大10MBまで</p>
                                <p>※ JPG、PNG、GIF形式に対応</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- アクションボタン -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('shops.show', $shop) }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        戻る
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        更新する
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
