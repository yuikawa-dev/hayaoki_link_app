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

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-medium text-gray-900">
                        参加イベント一覧
                    </h2>
                    <a href="{{ route('mypage') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        マイページに戻る
                    </a>
                </div>

                @if ($this->registeredEvents->isEmpty())
                    <p class="text-gray-500 text-center py-4">参加予定のイベントはありません</p>
                @else
                    <div class="space-y-6">
                        @foreach ($this->registeredEvents as $event)
                            <div class="border-b pb-6 last:border-b-0 last:pb-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">{{ $event->name }}</h3>
                                        <div class="mt-2 space-y-1">
                                            <p class="text-sm text-gray-500">
                                                <i class="fas fa-calendar"></i>
                                                {{ $event->start_time->format('Y/m/d H:i') }} 〜
                                                {{ $event->end_time->format('H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                <i class="fas fa-map-marker-alt"></i>
                                                {{ $event->location }}
                                            </p>
                                            @if ($event->fee > 0)
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-yen-sign"></i>
                                                    {{ number_format($event->fee) }}円
                                                </p>
                                            @endif
                                            @if ($event->requirements)
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-list"></i>
                                                    持ち物：{{ $event->requirements }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($event->pivot->status !== 'cancelled' && $event->start_time->isFuture())
                                        <button wire:click="cancelRegistration({{ $event->id }})"
                                            wire:confirm="このイベントの参加をキャンセルしてもよろしいですか？"
                                            class="text-red-600 hover:text-red-800">
                                            参加をキャンセル
                                        </button>
                                    @endif
                                </div>
                                <div class="mt-4">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $event->pivot->status === 'cancelled'
                                            ? 'bg-gray-100 text-gray-800'
                                            : ($event->start_time->isPast()
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-blue-100 text-blue-800') }}">
                                        {{ $event->pivot->status === 'cancelled'
                                            ? 'キャンセル済み'
                                            : ($event->start_time->isPast()
                                                ? '参加済み'
                                                : '参加予定') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $this->registeredEvents->links(data: ['current-page' => $this->currentPage]) }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
