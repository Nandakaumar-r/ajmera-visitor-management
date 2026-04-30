<?php

namespace App\Http\Controllers;

use App\Models\HelpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'question' => 'How do I submit a resignation request?',
                'answer' => 'You can submit a resignation request by clicking on the "New Request" button in the Resignations section of your dashboard. Fill out the required information including your reason and preferred resignation date.'
            ],
            [
                'question' => 'How can I view my leave balance?',
                'answer' => 'Your leave balance is displayed on the dashboard. For detailed history, click on "View leave history" in the Leave Balance section.'
            ],
            [
                'question' => 'How do I mark my attendance?',
                'answer' => 'You can mark your attendance by clicking on the "Mark Attendance" button in the Today\'s Attendance section of your dashboard.'
            ],
            [
                'question' => 'How can I update my profile information?',
                'answer' => 'Click on your profile name in the top navigation bar and select "Profile" to update your information.'
            ],
            [
                'question' => 'What happens after I submit a resignation request?',
                'answer' => 'After submission, your manager will review your request and either accept it with a confirmed last working day or decline it. You\'ll receive an email notification about their decision.'
            ]
        ];

        $helpRequests = HelpRequest::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('help.index', compact('faqs', 'helpRequests'));
    }

    public function submitHelp(Request $request)
    {
        $request->validate([
            'issue' => 'required|string|max:1000',
        ]);

        try {
            HelpRequest::create([
                'user_id' => Auth::id(),
                'issue_description' => $request->issue,
                'status' => 'open'
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error submitting help request'], 500);
        }
    }
}
