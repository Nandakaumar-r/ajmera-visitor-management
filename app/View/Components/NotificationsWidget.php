<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\Component;

class NotificationsWidget extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function getRecentNotifications($employeeId)
    {
        return Notification::where('user_id', $employeeId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }    

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.notifications-widget');
    }
}
