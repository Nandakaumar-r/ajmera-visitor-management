<?php

namespace App\Listeners;

use App\Events\PostLiked;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostLikedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostLiked $event)
    {
        Notification::create([
            'user_id' => $event->post->user->id,
            'message' => "{$event->user->name} liked your post.",
        ]);
    }
}
