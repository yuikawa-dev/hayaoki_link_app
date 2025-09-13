<?php

use function Livewire\Volt\{state, mount, rules};
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

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
]);

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

    Shop::create([
        'name' => $this->name,
        'description' => $this->description,
        'address' => $this->address,
        'contact' => $this->contact,
        'sns_links' => $snsLinks,
        'opening_time' => $this->opening_time,
        'closing_time' => $this->closing_time,
    ]);

    session()->flash('success', 'お店を登録しました！');

    return redirect()->route('shops.index');
};

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
            <form wire:submit="createShop">
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
