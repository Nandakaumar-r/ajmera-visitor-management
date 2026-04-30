<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class CommentSection extends Component
{
    public $post;
    public $comments;
    public $commentText;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->comments = $post->comments()->with('user')->get();
    }

    public function addComment()
    {
        $comment = $this->post->comments()->create([
            'user_id' => auth()->id(),
            'comment' => $this->commentText,
        ]);

        $this->comments->prepend($comment);
        $this->commentText = '';
    }

    public function render()
    {
        return view('livewire.comment-section');
    }
}
