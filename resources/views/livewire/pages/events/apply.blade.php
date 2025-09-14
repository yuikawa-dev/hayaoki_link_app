<?php

use function Livewire\Volt\{state, computed, mount, rules};
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Support\Facades\Auth;

// イベントのIDを受け取る
state(['event', 'name', 'email', 'phone', 'message']);

// マウント時にイベントの詳細を取得
mount(function ($event) {
    $this->event = Event::findOrFail($event);

    // ログイン済みの場合は情報を自動入力
    if (Auth::check()) {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
    }
});

// バリデーションルール
rules([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'phone' => 'required|string|max:20',
    'message' => 'nullable|string|max:1000',
]);

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

// 申し込み処理
$applyEvent = function () {
    // ログイン必須
    if (!Auth::check()) {
        session()->flash('error', 'ログインが必要です。');
        return redirect()->route('login');
    }

    $this->validate();

    // イベントが終了していないかチェック
    if ($this->event->isFinished()) {
        session()->flash('error', 'このイベントは既に終了しています。');
        return;
    }

    // 定員に空きがあるかチェック
    if (!$this->event->hasAvailableSlots()) {
        session()->flash('error', '定員に達しているため申し込みできません。');
        return;
    }

    // 既に申し込み済みかチェック
    if ($this->hasApplied) {
        session()->flash('error', '既にこのイベントに申し込み済みです。');
        return;
    }

    // 申し込み登録
    EventRegistration::create([
        'event_id' => $this->event->id,
        'user_id' => Auth::id(),
        'status' => EventRegistration::STATUS_CONFIRMED, // 即座に確定
    ]);

    session()->flash('success', 'イベントへの申し込みが完了しました。');

    return redirect()->route('events.show', $this->event->id);
};

?>

<div class="py-6 min-h-screen bg-gradient-to-br from-orange-50 via-amber-50 to-yellow-50">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
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

        <!-- 申し込みフォームカード -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-orange-100">
            <!-- ヘッダー -->
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-8 py-6">
                <h1 class="text-2xl font-bold text-white">イベント申し込み</h1>
                <p class="text-orange-100 mt-2">{{ $event->name }}</p>
            </div>

            <div class="p-8">
                <!-- イベント基本情報 -->
                <div class="bg-orange-50 p-6 rounded-xl border border-orange-100 mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">申し込み内容</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">開催日時：</span>
                            <span class="text-gray-900">{{ $event->start_time->format('Y年m月d日 H:i') }} 〜
                                {{ $event->end_time->format('H:i') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">参加費：</span>
                            <span class="text-gray-900">{{ $event->getFormattedFee() }}</span>
                        </div>
                        <div class="md:col-span-2">
                            <span class="font-medium text-gray-700">開催場所：</span>
                            <span class="text-gray-900">{{ $event->location }}</span>
                        </div>
                    </div>
                </div>

                <!-- エラーメッセージ -->
                @if (session('error'))
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                </path>
                            </svg>
                            <p class="ml-3 text-red-700">{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <!-- 申し込み済みの場合 -->
                @if ($this->hasApplied)
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">申し込み済み</h3>
                        <p class="text-gray-600 mb-6">既にこのイベントに申し込み済みです。</p>
                        <a href="{{ route('events.show', $event->id) }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            イベント詳細を見る
                        </a>
                    </div>
                @elseif ($event->isFinished())
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">イベント終了</h3>
                        <p class="text-gray-600">このイベントは既に終了しています。</p>
                    </div>
                @elseif (!$event->hasAvailableSlots())
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">満員</h3>
                        <p class="text-gray-600">定員に達しているため申し込みできません。</p>
                    </div>
                @else
                    <!-- 申し込みフォーム -->
                    <form wire:submit="applyEvent">
                        <div class="space-y-6">
                            <!-- お名前 -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    お名前 <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" wire:model="name"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors text-gray-900">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- メールアドレス -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    メールアドレス <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" wire:model="email"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors text-gray-900">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 電話番号 -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    電話番号 <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="phone" wire:model="phone" placeholder="090-1234-5678"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors text-gray-900">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- メッセージ -->
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                    メッセージ（任意）
                                </label>
                                <textarea id="message" wire:model="message" rows="4" placeholder="質問やご要望があればお書きください"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors resize-none text-gray-900"></textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- 注意事項 -->
                            <div class="bg-yellow-50 p-4 rounded-xl border border-yellow-200">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                        </path>
                                    </svg>
                                    <div>
                                        <h4 class="text-sm font-medium text-yellow-800 mb-1">申し込み前にご確認ください</h4>
                                        <ul class="text-sm text-yellow-700 space-y-1">
                                            <li>• キャンセルはイベント開始の24時間前まで可能です</li>
                                            <li>• 参加費はイベント主催者の指示に従いお支払いください</li>
                                            <li>• 連絡先は緊急時の連絡用です</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- 送信ボタン -->
                            <div class="pt-4">
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-lg font-bold rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    申し込みを確定する
                                </button>
                            </div>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
