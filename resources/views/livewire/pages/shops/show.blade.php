<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Shop;
use Illuminate\Support\Facades\Auth;

// お店のIDを受け取る
state(['shop']);

// マウント時にお店の詳細を取得
mount(function ($shop) {
    $this->shop = Shop::with(['images', 'menus'])->findOrFail($shop);
});

// 現在のユーザーが管理者かどうかを判定
$isAdmin = computed(function () {
    return Auth::check() && Auth::user()->isAdmin();
});

// お店を削除する
$deleteShop = function () {
    if (!Auth::user()->isAdmin()) {
        abort(403, 'このアクションを実行する権限がありません。');
    }

    $this->shop->delete();

    session()->flash('success', 'お店を削除しました。');

    return redirect()->route('shops.index');
};

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- 戻るボタン -->
        <div class="mb-6">
            <a href="{{ route('shops.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                お店一覧に戻る
            </a>
        </div>

        <!-- お店詳細カード -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-orange-100">
            <!-- メイン画像 -->
            <div class="relative h-64 sm:h-80 lg:h-96">
                @php
                    $mainImage = $shop->getMainImage();
                @endphp
                @if ($mainImage)
                    <img src="{{ $mainImage->image_url }}" alt="{{ $shop->name }}" class="w-full h-full object-cover"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div
                        class="w-full h-full flex-col items-center justify-center bg-gradient-to-br from-orange-400 via-amber-500 to-yellow-500 hidden">
                        <svg class="w-16 h-16 text-white drop-shadow-lg mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                            </path>
                        </svg>
                        <span class="text-white text-sm">画像読み込みエラー</span>
                    </div>
                @else
                    <div
                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-400 via-amber-500 to-yellow-500">
                        <svg class="w-16 h-16 text-white drop-shadow-lg" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                @endif

                <!-- 営業中バッジ -->
                @if ($shop->isOpen())
                    <div class="absolute top-4 left-4">
                        <span
                            class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-full shadow-lg">
                            <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                            営業中
                        </span>
                    </div>
                @endif

                <!-- 管理者アクションボタン -->
                @if ($this->isAdmin)
                    <div class="absolute top-4 right-4 space-x-2">
                        <a href="{{ route('shops.edit', $shop->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            編集
                        </a>
                        <button wire:click="deleteShop" wire:confirm="本当にこのお店を削除しますか？"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                            削除
                        </button>
                    </div>
                @endif
            </div>

            <!-- 店舗詳細情報 -->
            <div class="p-8">
                <!-- 店舗名 -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $shop->name }}</h1>
                    <div class="w-16 h-1 bg-gradient-to-r from-orange-400 to-amber-500 rounded-full"></div>
                </div>

                <!-- 基本情報グリッド -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- 営業時間 -->
                    <div class="bg-orange-50 p-6 rounded-xl border border-orange-100">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">営業時間</h3>
                        </div>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($shop->opening_time)->format('H:i') }}
                            <span class="text-gray-500 mx-2">〜</span>
                            {{ \Carbon\Carbon::parse($shop->closing_time)->format('H:i') }}
                        </p>
                    </div>

                    <!-- 連絡先 -->
                    <div class="bg-green-50 p-6 rounded-xl border border-green-100">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">連絡先</h3>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-lg font-medium text-gray-800">{{ $shop->contact }}</p>
                            <a href="tel:{{ $shop->contact }}"
                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                    </path>
                                </svg>
                                電話する
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 住所 -->
                <div class="mb-8">
                    <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">住所</h3>
                        </div>
                        <p class="text-lg text-gray-800 leading-relaxed">{{ $shop->address }}</p>
                    </div>
                </div>

                <!-- 店舗説明 -->
                <div class="mb-8">
                    <div class="bg-purple-50 p-6 rounded-xl border border-purple-100">
                        <div class="flex items-center mb-4">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">店舗について</h3>
                        </div>
                        <p class="text-gray-700 leading-relaxed text-lg">{{ $shop->description }}</p>
                    </div>
                </div>

                <!-- SNSリンク -->
                @if ($shop->sns_links && count($shop->sns_links) > 0)
                    <div class="mb-8">
                        <div class="bg-pink-50 p-6 rounded-xl border border-pink-100">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-pink-400 to-pink-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">SNS</h3>
                            </div>
                            <div class="space-y-2">
                                @foreach ($shop->sns_links as $platform => $link)
                                    <a href="{{ $link }}" target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl mr-2">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                            </path>
                                        </svg>
                                        {{ ucfirst($platform) }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- 店舗画像ギャラリー -->
                @if ($shop->images->count() > 0)
                    <div class="mb-8">
                        <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-gray-400 to-gray-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">画像一覧</h3>
                            </div>
                            <!-- デバッグ情報（開発時のみ表示） -->
                            @if (config('app.debug'))
                                <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-sm">
                                    <strong>デバッグ情報:</strong> {{ $shop->images->count() }}枚の画像が登録されています
                                    @foreach ($shop->images as $debugImage)
                                        <div class="mt-1">
                                            <span class="text-gray-600">画像{{ $loop->iteration }}:</span>
                                            <span class="font-mono text-xs">{{ $debugImage->image_url }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach ($shop->images as $image)
                                    <div
                                        class="group relative overflow-hidden rounded-xl shadow-md hover:shadow-lg transition-all duration-300">
                                        @if ($image->image_url)
                                            <img src="{{ $image->image_url }}"
                                                alt="{{ $shop->name }} - {{ $image->image_type }}"
                                                class="w-full h-32 object-cover group-hover:scale-110 transition-transform duration-300"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                onload="console.log('Image loaded: {{ $image->image_url }}')">
                                            <!-- 画像読み込みエラー時の代替表示 -->
                                            <div
                                                class="w-full h-32 bg-gradient-to-br from-gray-200 to-gray-300 hidden items-center justify-center">
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                    <span class="text-xs text-gray-500">読み込みエラー</span>
                                                </div>
                                            </div>
                                        @else
                                            <!-- 画像URLが存在しない場合の表示 -->
                                            <div
                                                class="w-full h-32 bg-gradient-to-br from-yellow-200 to-yellow-300 flex items-center justify-center">
                                                <div class="text-center">
                                                    <svg class="w-8 h-8 text-yellow-600 mx-auto mb-2" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                        </path>
                                                    </svg>
                                                    <span class="text-xs text-yellow-700">ファイル不存在</span>
                                                </div>
                                            </div>
                                        @endif
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-300 flex items-center justify-center">
                                            <span
                                                class="text-white text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                                {{ \App\Models\ShopImage::$availableTypes[$image->image_type] ?? $image->image_type }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
