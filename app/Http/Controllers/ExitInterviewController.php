<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ExitInterviewQuestion;
use App\Models\ExitInterviewResponse;
use App\Models\Resignation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExitInterviewController extends Controller
{
    public function showForm()
    {
        $questions = ExitInterviewQuestion::paginate(20); // 5 questions per page
        return view('exit_interview.form', compact('questions'));
    }

    public function submitForm(Request $request)
    {
        $userId = Auth::id();
        $questions = ExitInterviewQuestion::all();
        $responses = [];

        if ($request->has('answers')) {
            foreach ($request->input('answers') as $questionId => $answer) {
                $response = ExitInterviewResponse::create([
                    'question_id' => (int) $questionId,
                    'user_id' => $userId,
                    'answer' => is_array($answer) ? implode(', ', $answer) : $answer,
                ]);
                $responses[$questionId] = $response->answer;
            }
        }
        // Handle file upload for signature
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures');
            $responses['signature'] = $signaturePath;
        }

        // Generate PDF
        // $pdf = Pdf::loadView('exit_interview.pdf', [
        //     'questions' => $questions,
        //     'responses' => $responses,
        // ]);

        // $pdfFilePath = 'NOC_' . $userId . '.pdf';
        // Storage::put($pdfFilePath, $pdf->output());

        return redirect()->back()->with('success', 'Feedback submitted successfully.');
    }

    // Show employee feedback for HR
    public function showEmployeeFeedback($resignationId)
    {
        try {
            $resignation = Resignation::with('employee')->findOrFail($resignationId);
            $user = User::where('email', $resignation->employee->employee_email)->first();
            $questions = ExitInterviewQuestion::with(['responses' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])->get();

            return view('hr.employee_feedback', compact('resignation', 'questions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error fetching employee feedback: ' . $e->getMessage());
        }
    }
}
