<?php

namespace App\Http\Controllers;

use App\Models\HelpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewHelpRequest;
use App\Models\User;

class HelpRequestController extends Controller
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
        
        $requests = HelpRequest::query()
            ->when(auth()->user()->hasRole('employee'), function ($query) {
                return $query->where('user_id', auth()->id());
            })
            ->with(['user', 'closedBy'])
            ->latest()
            ->get();

        return view('help.index', compact('requests', 'faqs'));
    }

    public function create()
    {
        $categories = [
            'Employee Information',
            'Income Tax',
            'Payslips',
            'Others'
        ];

        return view('help.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'attachment' => 'nullable|file|max:5120' // 5MB max
        ]);

        $helpRequest = new HelpRequest();
        $helpRequest->category = $validated['category'];
        $helpRequest->subject = $validated['subject'];
        $helpRequest->description = $validated['description'];
        $helpRequest->priority = $validated['priority'];
        $helpRequest->user_id = auth()->id();
        $helpRequest->status = 'active';

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('help-requests');
            $helpRequest->attachment_path = $path;
        }

        $helpRequest->save();

        // Send email to HR
        $hrUsers = User::role('hr')->get();
        foreach ($hrUsers as $hrUser) {
            Mail::to($hrUser->email)->queue(new NewHelpRequest($helpRequest));
        }

        return redirect()->route('help-requests.index')
            ->with('success', 'Request submitted successfully. HR team will be notified.');
    }

    public function show(HelpRequest $helpRequest)
    {
        //$this->authorize('view', $helpRequest);
        return view('help.show', compact('helpRequest'));
    }

    public function close(HelpRequest $helpRequest)
    {
        $this->authorize('close', $helpRequest);

        $helpRequest->update([
            'status' => 'closed',
            'closed_by' => auth()->id(),
            'closed_at' => now()
        ]);

        return redirect()->route('help-requests.show', $helpRequest)
            ->with('success', 'Request closed successfully.');
    }
}
