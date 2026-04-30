<?php

namespace App\Http\Controllers;

use App\Mail\IdCardSubmissionNotification;
use App\Models\Employee;
use App\Models\ExitProcess;
use App\Models\IdCardSubmission;
use App\Models\Resignation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class IdCardController extends Controller
{
    public function create()
    {
        $resignation = Resignation::with('employee')->get();
        return view('hr.id_card_submission', compact('resignation'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'employee_id' => 'required',
                'id_card_file' => 'nullable|file|mimes:jpeg,png,pdf|max:2048', // Allow file to be optional
                'remarks' => 'nullable|string',
                'captured_image' => 'nullable|string', // For base64 or any other format
            ]);
        
            $path = null; // Initialize the path variable
        
            // Handle file upload if provided
            if ($request->hasFile('id_card_file')) {
                // If the file is uploaded
                $file = $request->file('id_card_file');
                $fileExtension = $file->getClientOriginalExtension();
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return redirect()->route('idcard.create')->with('error', 'Invalid file type. Only JPG, JPEG, PNG, and PDF files are allowed.');
                }
        
                // Save the uploaded ID card file
                $path = $request->file('id_card_file')->store('id_cards', 'public');
            }
        
            // Handle webcam captured image if provided
            if ($request->has('captured_image')) {
                // Extract the base64 string from the captured image
                $imageData = $request->captured_image;
                $imageData = str_replace('data:image/png;base64,', '', $imageData); // Remove data URL prefix
                $imageData = base64_decode($imageData); // Decode the base64 string
        
                // Save the image to storage (or convert to other formats if needed)
                $imageName = 'captured_image_' . time() . '.png'; // You can change the extension if needed
                $imagePath = storage_path('app/public/id_cards/' . $imageName); // Save as id_card in storage
                file_put_contents($imagePath, $imageData);
        
                $path = 'id_cards/' . $imageName; // Save the path to the captured image
            }
        
            // Create the ID card submission record
            $submission = IdCardSubmission::create([
                'employee_id' => $request->employee_id,
                'file_path' => $path, // Save the path of uploaded file or captured image
                'remarks' => $request->remarks,
                'submitted_by' => Auth::id(),
            ]);
        
            // Update ExitProcess if ID card is submitted
            ExitProcess::where('employee_id', $request->employee_id)->update(['id_card_submitted' => 1]);
        
            // Send email notification (adjust recipient as needed)
            Mail::to('hr@example.com')->send(new IdCardSubmissionNotification($submission));
        
            // Redirect with success message
            return redirect()->route('idcard.create')->with('success', 'ID Card submitted successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->validator);
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }
     
}
