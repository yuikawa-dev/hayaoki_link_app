<?php

use function Livewire\Volt\{state, rules, computed};
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

use Livewire\Volt\Component;

new class extends Component {
    use WithFileUploads;

    public $name;
    public $bio;
    public $profile_image;
    public $current_profile_image;

    public function mount()
    {
        $this->name = auth()->user()->name;
        $this->bio = auth()->user()->bio;
        $this->current_profile_image = auth()->user()->profile_image;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function updateProfile()
    {
        $validated = $this->validate();

        $user = auth()->user();

        if ($this->profile_image) {
            if ($user->profile_image) {
                Storage::delete($user->profile_image);
            }
            $validated['profile_image'] = $this->profile_image->store('profile-images', 'public');
        }

        $user->update($validated);

        session()->flash('success', '更新しました。');

        return $this->redirect(route('mypage'), navigate: true);
    }

    public function getProfileImageUrl()
    {
        if ($this->profile_image) {
            return $this->profile_image->temporaryUrl();
        }

        return $this->current_profile_image ? Storage::url($this->current_profile_image) : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
};

?>

<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <form wire:submit.prevent="updateProfile" class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-6">
                    プロフィール編集
                </h2>

                <!-- プロフィール画像 -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        プロフィール画像
                    </label>
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <img src="{{ $this->getProfileImageUrl() }}" alt="{{ $name }}"
                                class="h-16 w-16 object-cover rounded-full">
                        </div>
                        <label class="block">
                            <span class="sr-only">プロフィール画像を選択</span>
                            <input type="file" wire:model="profile_image"
                                class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100
                            " />
                        </label>
                    </div>
                    @error('profile_image')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 名前 -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        名前
                    </label>
                    <input type="text" wire:model="name" id="name"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 自己紹介 -->
                <div class="mb-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700">
                        自己紹介
                    </label>
                    <textarea wire:model="bio" id="bio" rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-black"></textarea>
                    @error('bio')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <a href="{{ route('mypage') }}" class="text-sm text-gray-600 hover:text-gray-900 mr-4">
                        キャンセル
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        更新する
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
