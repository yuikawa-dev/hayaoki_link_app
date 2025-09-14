<?php

use function Livewire\Volt\{state, mount, rules, with, uses};
use App\Models\Shop;
use App\Models\ShopImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

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
    'address' => '',
    'contact' => '',
    'twitter' => '',
    'instagram' => '',
    'opening_time' => '06:00',
    'closing_time' => '21:00',
    'images' => [],
    'imageTypes' => [],
]);

// バリデーションルール
rules([
    'name' => 'required|string|max:255',
    'description' => 'required|string',
    'address' => 'required|string|max:500',
    'contact' => 'required|string|max:50',
    'twitter' => 'nullable|string|max:255',
    'instagram' => 'nullable|string|max:255',
    'opening_time' => 'required|date_format:H:i',
    'closing_time' => 'required|date_format:H:i|after:opening_time',
    'images.*' => 'nullable|image|max:2048',
    'imageTypes.*' => 'nullable|string|in:exterior,interior,menu,atmosphere',
]);

// 画像を追加
$addImage = function () {
    $this->images[] = null;
    $this->imageTypes[] = 'exterior';
};

// 画像を削除
$removeImage = function ($index) {
    array_splice($this->images, $index, 1);
    array_splice($this->imageTypes, $index, 1);
};

// お店を登録
$createShop = function () {
    $this->validate();

    $snsLinks = [];
    if ($this->twitter) {
        $snsLinks['twitter'] = $this->twitter;
    }
    if ($this->instagram) {
        $snsLinks['instagram'] = $this->instagram;
    }

    $shop = Shop::create([
        'name' => $this->name,
        'description' => $this->description,
        'address' => $this->address,
        'contact' => $this->contact,
        'sns_links' => $snsLinks,
        'opening_time' => $this->opening_time,
        'closing_time' => $this->closing_time,
    ]);

    // 画像を保存
    foreach ($this->images as $index => $image) {
        if ($image) {
            $path = $image->store('shop-images', 'public');

            ShopImage::create([
                'shop_id' => $shop->id,
                'image_path' => $path,
                'image_type' => $this->imageTypes[$index] ?? 'exterior',
            ]);
        }
    }

    session()->flash('success', 'お店を登録しました！');

    return redirect()->route('shops.index');
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
                class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                    </path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">朝活お店登録</h1>
            <p class="text-lg text-gray-600">朝から営業するお店の情報を登録してください</p>
        </div>

        <!-- 登録フォーム -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border border-orange-100">
            <form wire:submit="createShop" enctype="multipart/form-data">
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
                            </select>
                            <span class="text-gray-500 font-medium">〜</span>
                            <select wire:model="closing_time"
                                class="flex-1 px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                                <option value="17:00">17:00</option>
                                <option value="18:00">18:00</option>
                                <option value="19:00">19:00</option>
                                <option value="20:00">20:00</option>
                                <option value="21:00">21:00</option>
                                <option value="22:00">22:00</option>
                                <option value="23:00">23:00</option>
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
                    <div>
                        <label for="twitter"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                            Twitter URL
                        </label>
                        <input type="url" id="twitter" wire:model="twitter"
                            placeholder="https://twitter.com/username"
                            class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                        @error('twitter')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="instagram"
                            class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987c6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297L3.323 14.27C2.49 13.394 2 12.243 2 10.946s.49-2.448 1.323-3.323L4.746 6.2c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297l1.423 1.423c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323l-1.423 1.423c-.875.807-2.026 1.297-3.323 1.297z" />
                            </svg>
                            Instagram URL
                        </label>
                        <input type="url" id="instagram" wire:model="instagram"
                            placeholder="https://instagram.com/username"
                            class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                        @error('instagram')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- 店舗画像 -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            店舗画像
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
                                    <p class="text-gray-600 mb-4">まだ画像が追加されていません</p>
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
                                <p>※ 1つのファイルにつき最大2MBまで</p>
                                <p>※ JPG、PNG、GIF形式に対応</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- アクションボタン -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('shops.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        戻る
                    </a>

                    <button type="submit"
                        class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        お店を登録する
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
