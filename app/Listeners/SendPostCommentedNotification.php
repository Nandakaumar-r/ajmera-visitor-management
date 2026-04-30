<?php

namespace App\Listeners;

use App\Events\PostCommented;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPostCommentedNotification
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
    public function handle(PostCommented $event)
    {
        Notification::create([
            'user_id' => $event->post->user->id,
            'message' => "{$event->user->name} commented on your post.",
        ]);
    }
}
