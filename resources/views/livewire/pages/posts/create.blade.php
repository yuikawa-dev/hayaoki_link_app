<?php

use function Livewire\Volt\{state, rules, layout};
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

layout('layouts.app');

state([
    'content' => '',
]);

rules([
    'content' => 'required|max:1000',
]);

$save = function () {
    $this->validate();

    $post = Post::create([
        'user_id' => Auth::id(),
        'content' => $this->content,
    ]);

    session()->flash('message', '投稿しました。');

    return redirect()->route('mypage.posts');
};

?>

<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Good Morning!</h2>
                <form wire:submit="save" class="space-y-4">
                    <div class="mb-6">
                        <label for="content" class="block text-sm font-medium text-gray-700">
                            さぁ、朝を始めましょう。
                        </label>
                        <textarea wire:model="content" id="content" required rows="4"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black"
                            placeholder="おはようございます。"></textarea>
                        @error('content')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            投稿
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
