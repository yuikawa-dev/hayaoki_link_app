<?php

use function Livewire\Volt\{state, computed};
use App\Models\Event;
use App\Models\EventRegistration;
use Livewire\WithPagination;

state(['currentPage' => 1]);
state(['perPage' => 10]);

// ページ変更時のイベントハンドラ
$updatedCurrentPage = function ($value) {
    $this->currentPage = $value;
};

// イベントキャンセル後のイベントハンドラ
$eventCancelled = function () {
    $this->currentPage = 1;
};

$registeredEvents = computed(function () {
    return auth()->user()->registeredEvents()->orderBy('start_time')->paginate($this->perPage);
});

$cancelRegistration = function ($eventId) {
    $registration = EventRegistration::where('event_id', $eventId)
        ->where('user_id', auth()->id())
        ->first();

    if ($registration) {
        $registration->update(['status' => 'cancelled']);
        $this->dispatch('event-cancelled');
    }
};

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- ヘッダー部分 -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-orange-100 mb-8">
            <div class="bg-gradient-to-r from-orange-400 via-amber-500 to-yellow-500 p-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-opacity-20 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-white mb-1">
                                🌅 あなたの朝活イベント
                            </h1>
                            <p class="text-orange-100">素敵な朝を一緒に迎えましょう</p>
                        </div>
                    </div>
                    <a href="{{ route('mypage') }}"
                        class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-100 text-orange-600 hover:text-orange-700 font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        マイページに戻る
                    </a>
                </div>
            </div>
        </div>

        <!-- イベント一覧 -->
        @if ($this->registeredEvents->isEmpty())
            <div class="rounded-2xl shadow-xl p-12 text-center border border-orange-100">
                <div
                    class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">まだ参加予定のイベントはありません</h3>
                <p class="text-gray-600 mb-6">素敵な朝活イベントを見つけて、新しい一日を始めましょう！</p>
                <a href="{{ route('events.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    イベントを探す
                </a>
            </div>
        @else
            <div class="grid gap-6">
                @foreach ($this->registeredEvents as $event)
                    <div
                        class="rounded-2xl shadow-xl overflow-hidden border border-orange-100 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <!-- イベントカードヘッダー -->
                        <div class="bg-gradient-to-r from-orange-400 via-amber-500 to-yellow-500 p-4">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-6 0a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2m-6 0V7">
                                            </path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white">{{ $event->name }}</h3>
                                </div>
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold
                                    {{ $event->pivot->status === 'cancelled'
                                        ? 'bg-gray-100 bg-opacity-80 text-gray-800'
                                        : ($event->start_time->isPast()
                                            ? 'bg-green-100 bg-opacity-80 text-green-800'
                                            : 'bg-blue-100 bg-opacity-80 text-blue-800') }}">
                                    {{ $event->pivot->status === 'cancelled'
                                        ? '🚫 キャンセル済み'
                                        : ($event->start_time->isPast()
                                            ? '✅ 参加済み'
                                            : '⏰ 参加予定') }}
                                </span>
                            </div>
                        </div>

                        <!-- イベント詳細情報 -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <!-- 日時 -->
                                <div class="flex items-center bg-orange-50 p-4 rounded-xl border border-orange-100">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-6 0a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2m-6 0V7">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-orange-800">開催日時</p>
                                        <p class="text-sm text-gray-700">
                                            {{ $event->start_time->format('Y/m/d H:i') }} 〜
                                            {{ $event->end_time->format('H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- 場所 -->
                                <div class="flex items-center bg-blue-50 p-4 rounded-xl border border-blue-100">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-blue-800">開催場所</p>
                                        <p class="text-sm text-gray-700">{{ $event->location }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- 参加費と参加条件 -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                @if ($event->fee > 0)
                                    <div class="flex items-center bg-green-50 p-4 rounded-xl border border-green-100">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-green-800">参加費</p>
                                            <p class="text-sm text-gray-700">{{ number_format($event->fee) }}円</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($event->requirements)
                                    <div
                                        class="flex items-start bg-red-50 p-4 rounded-xl border border-red-100 {{ $event->fee > 0 ? '' : 'md:col-span-2' }}">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-500 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-red-800">参加条件・注意事項</p>
                                            <p class="text-sm text-gray-700">{{ $event->requirements }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- アクションボタン -->
                            <div class="flex flex-wrap gap-3 justify-end">
                                <!-- 詳細を見るボタン -->
                                <a href="{{ route('events.show', $event->id) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    詳細を見る
                                </a>

                                @if ($event->pivot->status !== 'cancelled' && $event->start_time->isFuture())
                                    <!-- 参加をキャンセルボタン -->
                                    <button wire:click="cancelRegistration({{ $event->id }})"
                                        wire:confirm="このイベントの参加をキャンセルしてもよろしいですか？"
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        キャンセル
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- ページネーション -->
            @if ($this->registeredEvents->hasPages())
                <div class="mt-8 bg-white rounded-2xl shadow-xl p-6 border border-orange-100">
                    {{ $this->registeredEvents->links(data: ['current-page' => $this->currentPage]) }}
                </div>
            @endif
        @endif
    </div>
</div>
