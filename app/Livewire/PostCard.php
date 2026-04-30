<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class PostCard extends Component
{
    public Post $post;
    public $likesCount;
    public $isLiked;
    public $isCommentSectionVisible = false; // Initialize the variable

    // Remove 'postLiked' listener and only use 'likeToggled' to refresh the post
    protected $listeners = ['likeToggled' => 'refreshPostData'];

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->likesCount = $post->likes->count();
        $this->isLiked = $post->likedBy(auth()->user());
    }

    public function toggleLike()
    {
        // Toggling like logic
        if ($this->isLiked) {
            $this->post->likes()->detach(auth()->id());
            $this->isLiked = false;
        } else {
            $this->post->likes()->attach(auth()->id());
            $this->isLiked = true;
        }

        // Update likes count
        $this->likesCount = $this->post->likes()->count();

        // Emit 'likeToggled' event to refresh the like count in the component
        $this->emit('likeToggled', $this->post->id);
    }

    public function refreshPostData($postId)
    {
        // Fetch the post again to get updated data
        $this->post = Post::with('likes')->find($postId);
        $this->likesCount = $this->post->likes->count();
    }

    public function toggleCommentSection()
    {
        $this->isCommentSectionVisible = !$this->isCommentSectionVisible;
    }

    public function render()
    {
        return view('livewire.post-card');
    }
}
