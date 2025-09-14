<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Shop;
use Livewire\WithPagination;

// 検索条件のstate
state([
    'search' => '',
    'address' => '',
    'morningTime' => '08:00',
    'perPage' => 12,
]);

// 朝から営業しているお店を取得
$shops = computed(function () {
    return Shop::with(['images', 'menus'])
        ->searchByName($this->search)
        ->searchByAddress($this->address)
        ->morningOpen($this->morningTime)
        ->orderBy('opening_time')
        ->paginate($this->perPage);
});

// 検索条件をリセット
$resetFilters = function () {
    $this->search = '';
    $this->address = '';
    $this->morningTime = '08:00';
};

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ページヘッダー -->
        <div class="text-center mb-8 relative">
            <!-- マイページに戻るボタン -->
            <div class="absolute left-0 top-0">
                <a href="{{ route('mypage') }}"
                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    マイページに戻る
                </a>
            </div>

            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                    </path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">朝活お店検索</h1>
            <p class="text-lg text-gray-600">朝から営業しているお店を見つけて、素敵な朝活を始めましょう！</p>
        </div>

        <!-- 検索フィルター -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-orange-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- 店舗名検索 -->
                <div>
                    <label for="search" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        店舗名で検索
                    </label>
                    <input type="text" id="search" wire:model.live="search" placeholder="店舗名を入力..."
                        class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                </div>

                <!-- 住所検索 -->
                <div>
                    <label for="address" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        住所で検索
                    </label>
                    <input type="text" id="address" wire:model.live="address" placeholder="住所を入力..."
                        class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                </div>

                <!-- 朝の時間指定 -->
                <div>
                    <label for="morningTime" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        朝の営業時間
                    </label>
                    <select id="morningTime" wire:model.live="morningTime"
                        class="w-full px-4 py-3 border-2 border-orange-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white transition-all duration-200 hover:border-orange-300 text-gray-900">
                        <option value="09:00">9:00まで</option>
                        <option value="10:00">10:00まで</option>
                        <option value="11:00">11:00まで</option>
                    </select>
                </div>

                <!-- リセットボタン -->
                <div class="flex items-end">
                    <button type="button" wire:click="resetFilters"
                        class="w-full px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        リセット
                    </button>
                </div>
            </div>
        </div>

        <!-- 検索結果 -->
        <div class="mb-8">
            <div class="bg-white rounded-xl p-6 shadow-lg border border-orange-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-gradient-to-r from-orange-400 to-amber-500 rounded-full mr-3"></div>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $this->shops->total() }}件のお店が見つかりました
                        </p>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        営業時間順に表示
                    </div>
                </div>
            </div>
        </div>

        <!-- お店一覧 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-8">
            @forelse ($this->shops as $shop)
                <div
                    class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border border-orange-100 hover:border-orange-200">
                    <!-- 店舗画像 -->
                    <div class="relative">
                        @php
                            $mainImage = $shop->getMainImage();
                        @endphp
                        @if ($mainImage)
                            <img src="{{ $mainImage->image_url }}" alt="{{ $shop->name }}"
                                class="w-full h-48 object-cover"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div
                                class="w-full h-48 flex-col items-center justify-center bg-gradient-to-br from-orange-400 via-amber-500 to-yellow-500 hidden">
                                <svg class="w-12 h-12 text-white drop-shadow-lg mb-2" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                                <span class="text-white text-xs">画像読み込みエラー</span>
                            </div>
                        @else
                            <div
                                class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-orange-400 via-amber-500 to-yellow-500">
                                <svg class="w-12 h-12 text-white drop-shadow-lg" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                        @endif
                        <!-- 営業中バッジ -->
                        @if ($shop->isOpen())
                            <div class="absolute top-3 left-3">
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                    <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                                    営業中
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- 店舗情報 -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $shop->name }}</h3>

                        <!-- 営業時間 -->
                        <div class="flex items-center text-sm text-gray-600 mb-3 bg-orange-50 p-3 rounded-lg">
                            <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">{{ \Carbon\Carbon::parse($shop->opening_time)->format('H:i') }}
                                - {{ \Carbon\Carbon::parse($shop->closing_time)->format('H:i') }}</span>
                        </div>

                        <!-- 住所 -->
                        <div class="flex items-start text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0 text-orange-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="line-clamp-2">{{ $shop->address }}</span>
                        </div>

                        <!-- 説明 -->
                        <p class="text-sm text-gray-600 mb-6 line-clamp-2 leading-relaxed">{{ $shop->description }}</p>

                        <!-- アクションボタン -->
                        <div class="flex space-x-3">
                            <a href="{{ route('shops.show', $shop) }}"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-center">
                                詳細を見る
                            </a>
                            @if ($shop->contact)
                                <a href="tel:{{ $shop->contact }}"
                                    class="px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="bg-white rounded-2xl shadow-lg p-12 border border-orange-100">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">お店が見つかりませんでした</h3>
                        <p class="text-gray-600 mb-6">検索条件を変更して再度お試しください。</p>
                        <button wire:click="resetFilters"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            検索条件をリセット
                        </button>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- ページネーション -->
        @if ($this->shops->hasPages())
            <div class="flex justify-center">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-orange-100">
                    {{ $this->shops->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
