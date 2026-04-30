<?php

namespace App\Http\Controllers;

use App\Models\Resignation;
use App\Models\RelievingLetter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RelievingLetterController extends Controller
{
    public function create(Resignation $resignation)
    {
        $employee = $resignation->employee;
        $existingLetter = RelievingLetter::where('resignation_id', $resignation->id)
            ->latest()
            ->first();
        
        return view('hr.relieving_letter.create', [
            'resignation' => $resignation,
            'employee' => $employee,
            'existingLetter' => $existingLetter
        ]);
    }

    public function store(Request $request, Resignation $resignation)
    {
        $request->validate([
            'letter_date' => 'required|date',
            'content' => 'required|string'
        ]);

        $letter = RelievingLetter::create([
            'resignation_id' => $resignation->id,
            'letter_date' => $request->letter_date,
            'content' => $request->content,
            'status' => 'draft',
            'generated_by' => Auth::id(),
            'generated_at' => now()
        ]);

        // Generate PDF
        $pdf = PDF::loadView('hr.relieving_letter.pdf', ['letter' => $letter]);
        
        // Save PDF to storage
        $filename = 'relieving_letter_' . $resignation->employee->employee_id . '_' . now()->format('Y_m_d_His') . '.pdf';
        $path = 'relieving_letters/' . $filename;
        Storage::put('public/' . $path, $pdf->output());
        
        // Update letter with file path
        $letter->update([
            'file_path' => $path,
            'status' => 'generated'
        ]);

        return redirect()->route('resignations.show', $resignation)
            ->with('success', 'Relieving letter has been generated successfully.');
    }

    public function download(RelievingLetter $letter)
    {
        if (!Storage::exists('public/' . $letter->file_path)) {
            return back()->with('error', 'PDF file not found.');
        }

        return Storage::download('public/' . $letter->file_path, 'relieving_letter.pdf');
    }

    public function preview(RelievingLetter $letter)
    {
        return view('hr.relieving_letter.preview', compact('letter'));
    }

    public function index()
    {
        $letters = RelievingLetter::with(['resignation.employee', 'generatedBy'])
            ->latest()
            ->paginate(10);
            
        return view('hr.relieving_letter.index', compact('letters'));
    }
}
