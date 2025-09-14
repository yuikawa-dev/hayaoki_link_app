<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Event;
use Livewire\WithPagination;

// 検索条件のstate
state([
    'search' => '',
    'location' => '',
    'dateFilter' => 'all',
    'feeRange' => 'all',
    'perPage' => 12,
]);

// イベントを取得
$events = computed(function () {
    $query = Event::with(['registrations'])
        ->searchByName($this->search)
        ->searchByLocation($this->location)
        ->upcoming(); // 今後のイベントのみ

    // 日付フィルター
    switch ($this->dateFilter) {
        case 'today':
            $query->today();
            break;
        case 'week':
            $query->thisWeek();
            break;
        case 'all':
        default:
            // すべて（upcoming既に適用済み）
            break;
    }

    // 参加費フィルター
    $query->byFeeRange($this->feeRange);

    return $query->orderBy('start_time')->paginate($this->perPage);
});

// 検索条件をリセット
$resetFilters = function () {
    $this->search = '';
    $this->location = '';
    $this->dateFilter = 'all';
    $this->feeRange = 'all';
};

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
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
                class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                    </path>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">イベント検索</h1>
            <p class="text-lg text-gray-600">朝活イベントを見つけて、新しい体験を始めましょう！</p>
        </div>

        <!-- 検索フィルター -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8 border border-blue-100">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <!-- イベント名検索 -->
                <div>
                    <label for="search" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        イベント名で検索
                    </label>
                    <input type="text" id="search" wire:model.live="search" placeholder="イベント名を入力..."
                        class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                </div>

                <!-- 開催場所検索 -->
                <div>
                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        開催場所で検索
                    </label>
                    <input type="text" id="location" wire:model.live="location" placeholder="開催場所を入力..."
                        class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                </div>

                <!-- 日付フィルター -->
                <div>
                    <label for="dateFilter" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        開催日
                    </label>
                    <select id="dateFilter" wire:model.live="dateFilter"
                        class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                        <option value="all">すべて</option>
                        <option value="today">今日</option>
                        <option value="week">今週</option>
                    </select>
                </div>

                <!-- 参加費フィルター -->
                <div>
                    <label for="feeRange" class="block text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                            </path>
                        </svg>
                        参加費
                    </label>
                    <select id="feeRange" wire:model.live="feeRange"
                        class="w-full px-4 py-3 border-2 border-blue-200 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 hover:border-blue-300 text-gray-900">
                        <option value="all">すべて</option>
                        <option value="free">無料</option>
                        <option value="low">〜1,000円</option>
                        <option value="medium">1,001〜3,000円</option>
                        <option value="high">3,001円〜</option>
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
            <div class="bg-white rounded-xl p-6 shadow-lg border border-blue-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full mr-3"></div>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $this->events->total() }}件のイベントが見つかりました
                        </p>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        開催日時順に表示
                    </div>
                </div>
            </div>
        </div>

        <!-- イベント一覧 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-8">
            @forelse ($this->events as $event)
                <div
                    class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:scale-105 border border-blue-100 hover:border-blue-200">
                    <!-- イベントのヘッダー部分 -->
                    <div class="relative">
                        <div
                            class="w-full h-48 flex items-center justify-center bg-gradient-to-br from-blue-400 via-indigo-500 to-purple-500">
                            <svg class="w-16 h-16 text-white drop-shadow-lg" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>

                        <!-- 参加費バッジ -->
                        <div class="absolute top-3 left-3">
                            <span
                                class="inline-flex items-center px-3 py-1 {{ $event->fee > 0 ? 'bg-yellow-500' : 'bg-green-500' }} text-white text-xs font-semibold rounded-full shadow-lg">
                                {{ $event->getFormattedFee() }}
                            </span>
                        </div>

                        <!-- 空き状況バッジ -->
                        @if ($event->hasAvailableSlots())
                            <div class="absolute top-3 right-3">
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-blue-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                    <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                                    申込可能
                                </span>
                            </div>
                        @else
                            <div class="absolute top-3 right-3">
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded-full shadow-lg">
                                    満席
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- イベント情報 -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $event->name }}</h3>

                        <!-- 開催日時 -->
                        <div class="flex items-center text-sm text-gray-600 mb-3 bg-blue-50 p-3 rounded-lg">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <div class="font-semibold">{{ $event->start_time->format('m月d日(D) H:i') }}</div>
                                <div class="text-xs text-gray-500">〜{{ $event->end_time->format('H:i') }}</div>
                            </div>
                        </div>

                        <!-- 開催場所 -->
                        <div class="flex items-start text-sm text-gray-600 mb-3">
                            <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0 text-blue-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="line-clamp-2">{{ $event->location }}</span>
                        </div>

                        <!-- 参加者情報 -->
                        <div class="flex items-center text-sm text-gray-600 mb-4 bg-gray-50 p-3 rounded-lg">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <span
                                class="font-semibold">{{ $event->getConfirmedParticipantsCount() }}/{{ $event->capacity }}人</span>
                        </div>

                        <!-- 説明 -->
                        <p class="text-sm text-gray-600 mb-6 line-clamp-2 leading-relaxed">{{ $event->description }}
                        </p>

                        <!-- アクションボタン -->
                        <div class="flex space-x-3">
                            <a href="{{ route('events.show', $event) }}"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl text-center">
                                詳細を見る
                            </a>
                            @if ($event->contact)
                                <a href="mailto:{{ $event->contact }}"
                                    class="px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="bg-white rounded-2xl shadow-lg p-12 border border-blue-100">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-3">イベントが見つかりませんでした</h3>
                        <p class="text-gray-600 mb-6">検索条件を変更して再度お試しください。</p>
                        <button wire:click="resetFilters"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
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
        @if ($this->events->hasPages())
            <div class="flex justify-center">
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-blue-100">
                    {{ $this->events->links() }}
                </div>
            </div>
        @endif
    </div>
</div>
