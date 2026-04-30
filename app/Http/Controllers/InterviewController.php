<?php

namespace App\Http\Controllers;

use App\Mail\ResignationCancelledEmployee;
use App\Mail\ResignationCancelledManager;
use App\Models\ExitProcess;
use App\Models\InterviewResponse;
use App\Models\Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class InterviewController extends Controller
{
    /**
     * Save the exit interview responses and audio recordings
     */
    public function store(Request $request)
    {
        $request->validate([
            'responses' => 'nullable|array',
            'responses.*' => 'nullable|string',
            'last_working_day' => 'required|date'
        ]);

        $resignationId = $request->resignation_id;
        $responses = $request->input('responses', []);

        try {
            DB::beginTransaction();

            foreach ($responses as $questionId => $response) {
                $audioPath = null;
                $audioData = $request->input("audio_data_{$questionId}");

                if ($audioData) {
                    $audioData = substr($audioData, strpos($audioData, ',') + 1);
                    $decodedAudio = base64_decode($audioData);

                    $fileName = Str::uuid() . '.webm';
                    $path = "interview_audios/{$resignationId}/{$fileName}";

                    Storage::disk('public')->put($path, $decodedAudio);
                    $audioPath = $path;
                }

                InterviewResponse::create([
                    'employee_id' => $request->employee_id,
                    'resignation_id' => $resignationId,
                    'question_id' => $questionId,
                    'response' => $response,
                    'audio_path' => $audioPath,
                ]);
            }

            // ✅ Update resignation status if needed
            $resignation = \App\Models\Resignation::findOrFail($resignationId);
            $resignation->status = 'completed';
            $resignation->save();

            ExitProcess::where('employee_id', $request->employee_id)->update([
                'hr_exit_interview' => 1,
                'last_working_day' => $request->last_working_day,
                'notice_period' => 1
            ]);

            DB::commit();

            // ✅ Get fresh data
            $employee = $resignation->employee;
            $manager = $employee->manager;

            // ✅ Send mails if data exists
            if ($manager && $employee) {
                Mail::to($manager->manager_email)
                    ->send(new \App\Mail\HRApprovedLWDNotification($resignation, $manager, $employee));

                Mail::to($employee->employee_email)
                    ->send(new \App\Mail\HRApprovedLWDNotificationEmployee($resignation, $manager, $employee));
            }

            return response()->json([
                'success' => true,
                'message' => 'Interview responses saved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to save interview responses',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle employee transfer request
     */
    public function transfer(Request $request, $resignationId)
    {
        $request->validate([
            'department_id' => 'required|exists:employee_departments,id',
            'manager_email' => 'required|email'
        ]);

        try {
            DB::beginTransaction();

            $resignation = \App\Models\Resignation::findOrFail($resignationId);
             $employee   = $resignation->employee;

            // Update employee department
            $employee->update([
                'employee_department' => $request->department_id
            ]);

            $manager = Manager::where('manager_email', $request->manager_email)
                ->firstOrFail();

            // 3) Update employee's manager_id
            $employee->update([
                'manager_id' => $manager->manager_id,
            ]);

            // Cancel resignation
            $resignation->update([
                'status' => 'transferred',
                'resignation_reason' => 'Employee transferred to different department'
            ]);

            ExitProcess::where('employee_id', $resignation->employee_id)
                ->update([
                    'internal_movement_rejection' => 1
                ]);

            DB::commit();

            // Send email to manager
            Mail::to($request->manager_email)->send(new \App\Mail\EmployeeTransferredManagerMail($resignation));
            // Send to Employee
            Mail::to($resignation->employee->employee_email)->send(
                new \App\Mail\EmployeeTransferredEmployeeMail($resignation)
            );
            return response()->json([
                'success' => true,
                'message' => 'Employee transferred successfully and email sent to manager.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Employee Transfer Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to transfer employee'
            ], 500);
        }
    }


    public function getManager($id)
    {
        $managers = Manager::where('department_id', $id)->get();

        if ($managers->isNotEmpty()) {
            $emails = $managers->pluck('manager_email')->filter()->values(); // get all non-null emails

            return response()->json([
                'success' => true,
                'manager_emails' => $emails
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No managers found for this department'
        ]);
    }


    /**
     * Handle employee revival (cancellation of resignation)
     */

    public function revive($resignationId)
    {
        try {
            DB::beginTransaction();

            $resignation = \App\Models\Resignation::with('employee.manager')->findOrFail($resignationId);

            $resignation->status = 'cancelled';
            $resignation->resignation_reason = 'Employee decided to stay';
            $resignation->save();

            DB::commit();

            $employee = $resignation->employee;
            $manager = $employee->manager;


            if ($manager && $employee) {
                Mail::to($employee->employee_email)
                    ->send(new ResignationCancelledEmployee($resignation, $manager, $employee));

                Mail::to($manager->manager_email)
                    ->send(new ResignationCancelledManager($resignation, $manager, $employee));
            }

            return response()->json([
                'success' => true,
                'message' => 'Employee resignation cancelled successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel resignation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
