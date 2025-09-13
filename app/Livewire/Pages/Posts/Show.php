<?php

namespace App\Livewire\Pages\Posts;

use App\Models\Post;
use Livewire\Component;

class Show extends Component
{
    public Post $post;

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function render()
    {
        return view('livewire.pages.posts.show');
    }
}
