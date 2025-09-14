<?php

use function Livewire\Volt\{state, computed, mount};
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;

// イベントのIDを受け取る
state(['event']);

// マウント時にイベントの詳細を取得
mount(function ($event) {
    $this->event = Event::findOrFail($event);
});

// 現在のユーザーが管理者かどうかを判定
$isAdmin = computed(function () {
    return Auth::check() && Auth::user()->isAdmin();
});

// 既に申し込み済みかチェック
$hasApplied = computed(function () {
    if (!Auth::check()) {
        return false;
    }

    return EventRegistration::where('event_id', $this->event->id)
        ->where('user_id', Auth::id())
        ->whereIn('status', [EventRegistration::STATUS_PENDING, EventRegistration::STATUS_CONFIRMED])
        ->exists();
});

// イベントを削除する
$deleteEvent = function () {
    if (!Auth::user()->isAdmin()) {
        abort(403, 'このアクションを実行する権限がありません。');
    }

    $this->event->delete();

    session()->flash('success', 'イベントを削除しました。');

    return redirect()->route('events.index');
};

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- 戻るボタン -->
        <div class="mb-6">
            <a href="{{ route('events.index') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                イベント一覧に戻る
            </a>
        </div>

        <!-- イベント詳細カード -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-orange-100">
            <!-- メイン画像 -->
            <div class="relative h-64 sm:h-80 lg:h-96">
                @if ($event->hasImage())
                    <img src="{{ $event->getImageUrl() }}" alt="{{ $event->name }}" class="w-full h-full object-cover"
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
                                d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-6 0a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2m-6 0V7">
                            </path>
                        </svg>
                    </div>
                @endif

                <!-- イベント状態バッジ -->
                <div class="absolute top-4 left-4">
                    @if ($event->isInProgress())
                        <span
                            class="inline-flex items-center px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-full shadow-lg">
                            <div class="w-2 h-2 bg-white rounded-full mr-2 animate-pulse"></div>
                            開催中
                        </span>
                    @elseif ($event->isFinished())
                        <span
                            class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-semibold rounded-full shadow-lg">
                            終了
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-semibold rounded-full shadow-lg">
                            開催予定
                        </span>
                    @endif
                </div>

                <!-- 管理者アクションボタン -->
                @if ($this->isAdmin)
                    <div class="absolute top-4 right-4 space-x-2">
                        <a href="{{ route('events.edit', $event->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            編集
                        </a>
                        <button wire:click="deleteEvent" wire:confirm="本当にこのイベントを削除しますか？"
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

            <!-- イベント詳細情報 -->
            <div class="p-8">
                <!-- 成功メッセージ -->
                @if (session('success'))
                    <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <p class="ml-3 text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                <!-- イベント名 -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $event->name }}</h1>
                    <div class="w-16 h-1 bg-gradient-to-r from-orange-400 to-amber-500 rounded-full"></div>
                </div>

                <!-- 基本情報グリッド -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- 開催日時 -->
                    <div class="bg-orange-50 p-6 rounded-xl border border-orange-100">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-orange-400 to-amber-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0h8m-6 0a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2m-6 0V7">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">開催日時</h3>
                        </div>
                        <div class="space-y-2">
                            <p class="text-lg font-bold text-gray-800">
                                {{ $event->start_time->format('Y年m月d日 (D)') }}
                            </p>
                            <p class="text-lg font-semibold text-gray-700">
                                {{ $event->start_time->format('H:i') }}
                                <span class="text-gray-500 mx-2">〜</span>
                                {{ $event->end_time->format('H:i') }}
                            </p>
                        </div>
                    </div>

                    <!-- 参加費 -->
                    <div class="bg-green-50 p-6 rounded-xl border border-green-100">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">参加費</h3>
                        </div>
                        <p class="text-2xl font-bold text-gray-800">{{ $event->getFormattedFee() }}</p>
                    </div>
                </div>

                <!-- 定員と連絡先 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- 定員 -->
                    <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">定員</h3>
                        </div>
                        <div class="flex items-center justify-between">
                            <p class="text-lg font-medium text-gray-800">
                                {{ $event->getConfirmedParticipantsCount() }} / {{ $event->capacity }}名
                            </p>
                            @if (!$event->hasAvailableSlots())
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-semibold rounded-full">
                                    満員
                                </span>
                            @elseif ($event->getConfirmedParticipantsCount() > $event->capacity * 0.8)
                                <span
                                    class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-semibold rounded-full">
                                    残りわずか
                                </span>
                            @else
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-semibold rounded-full">
                                    空きあり
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- 連絡先 -->
                    <div class="bg-purple-50 p-6 rounded-xl border border-purple-100">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-purple-400 to-purple-500 rounded-full flex items-center justify-center mr-3">
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
                            <p class="text-lg font-medium text-gray-800">{{ $event->contact }}</p>

                        </div>
                    </div>
                </div>

                <!-- 開催場所 -->
                <div class="mb-8">
                    <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100">
                        <div class="flex items-center mb-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-indigo-400 to-indigo-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">開催場所</h3>
                        </div>
                        <p class="text-lg text-gray-800 leading-relaxed">{{ $event->location }}</p>
                    </div>
                </div>

                <!-- イベント説明 -->
                <div class="mb-8">
                    <div class="bg-yellow-50 p-6 rounded-xl border border-yellow-100">
                        <div class="flex items-center mb-4">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">イベントについて</h3>
                        </div>
                        <p class="text-gray-700 leading-relaxed text-lg">{{ $event->description }}</p>
                    </div>
                </div>

                <!-- 参加条件 -->
                @if ($event->requirements)
                    <div class="mb-8">
                        <div class="bg-red-50 p-6 rounded-xl border border-red-100">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-red-400 to-red-500 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">参加条件・注意事項</h3>
                            </div>
                            <p class="text-gray-700 leading-relaxed text-lg">{{ $event->requirements }}</p>
                        </div>
                    </div>
                @endif

                <!-- 参加申込ボタン -->
                @if ($this->hasApplied)
                    <div class="text-center">
                        <div
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-lg font-bold rounded-xl shadow-lg cursor-default">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            申し込み済み
                        </div>
                        <p class="text-sm text-gray-600 mt-2">このイベントに申し込み済みです</p>
                    </div>
                @elseif ($event->isFinished())
                    <div class="text-center">
                        <p class="text-lg text-gray-500 font-semibold">このイベントは終了しました</p>
                    </div>
                @elseif (!$event->hasAvailableSlots())
                    <div class="text-center">
                        <p class="text-lg text-red-600 font-semibold">満員のため参加申込を受け付けていません</p>
                    </div>
                @else
                    <div class="text-center">
                        <a href="{{ route('events.apply', $event->id) }}"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-lg font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            このイベントに参加申込する
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
