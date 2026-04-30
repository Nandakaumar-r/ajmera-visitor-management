<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\QuickLink;
use Carbon\Carbon;
use App\Services\LinkedInService;
use Illuminate\Http\Request;

class SocialFeedController extends Controller
{
    protected $linkedInService;

    public function __construct(LinkedInService $linkedInService)
    {
        $this->linkedInService = $linkedInService;
    }

    public function index()
    {
        $posts = $this->linkedInService->getAllPosts();
        $birthdays = $this->getTodaysBirthdays();
        $anniversaries = $this->getWorkAnniversaries();
        $events = $this->getUpcomingEvents();
        $announcements = $this->getRecentAnnouncements();
        $quickLinks = $this->getActiveQuickLinks();

        return view('social-feed.index', compact(
            'posts',
            'birthdays',
            'anniversaries',
            'events',
            'announcements',
            'quickLinks'
        ));
    }

    protected function getTodaysBirthdays()
    {
        $today = Carbon::now();
        
        return User::whereMonth('date_of_birth', $today->month)
            ->whereDay('date_of_birth', $today->day)
            ->get();
    }

    protected function getWorkAnniversaries()
    {
        $today = Carbon::now();
        $users = User::whereMonth('joining_date', $today->month)
            ->whereDay('joining_date', $today->day)
            ->get();

        $anniversaries = [];
        foreach ($users as $user) {
            $years = $today->diffInYears($user->joining_date);
            if ($years > 0) {
                if (!isset($anniversaries[$years])) {
                    $anniversaries[$years] = [];
                }
                $anniversaries[$years][] = $user;
            }
        }
        
        // Sort by years in ascending order
        ksort($anniversaries);
        
        return $anniversaries;
    }

    protected function getUpcomingEvents()
    {
        return Event::where('date', '>=', now())
            ->orderBy('date')
            ->limit(5)
            ->get();
    }

    protected function getRecentAnnouncements()
    {
        return Announcement::where('published_at', '<=', now())
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();
    }

    protected function getActiveQuickLinks()
    {
        return QuickLink::where('is_active', true)
            ->orderBy('order')
            ->get();
    }
}
