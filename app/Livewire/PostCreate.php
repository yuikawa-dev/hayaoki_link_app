<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostCreate extends Component
{
    public $content = '';
    public $message = null;

    protected $rules = [
        'content' => 'required|min:1|max:1000',
    ];

    protected $messages = [
        'content.required' => '投稿内容を入力してください。',
        'content.min' => '投稿内容を入力してください。',
        'content.max' => '投稿内容は1000文字以内で入力してください。',
    ];

    public function getCharacterCountProperty()
    {
        return mb_strlen($this->content);
    }

    public function getRemainingCharactersProperty()
    {
        return 1000 - $this->characterCount;
    }

    public function test()
    {
        $this->message = 'テストボタンが動作しました！ユーザーID: ' . Auth::id();
    }

    public function save()
    {
        $this->validate();

        try {
            $post = Post::create([
                'user_id' => Auth::id(),
                'content' => $this->content,
            ]);

            // 成功メッセージをセッションに保存
            session()->flash('success', '投稿が完了しました！');

            // マイページにリダイレクト
            return redirect()->route('mypage.posts');
        } catch (\Exception $e) {
            $this->message = 'エラー: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.post-create')->layout('layouts.app');
    }
}
