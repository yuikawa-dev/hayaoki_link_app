<?php

namespace App\Livewire\Pages\Posts;

use App\Models\Post;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Create extends Component
{
    public $title = '';
    public $content = '';
    public $url = '';

    protected $rules = [
        'title' => 'required|max:255',
        'content' => 'required',
        'url' => 'nullable|url|max:255',
    ];

    public function save()
    {
        $this->validate();

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'content' => $this->content,
            'url' => $this->url,
        ]);

        session()->flash('message', '投稿を作成しました。');

        return redirect()->route('posts.show', $post);
    }

    public function render()
    {
        return view('livewire.pages.posts.create');
    }
}
