<?php

namespace App\Http\Controllers;

use App\Models\Form16;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Form16Controller extends Controller
{
    public function index()
    {
        $form16s = Form16::with('employee')
            ->where('employee_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('form16s.index', compact('form16s'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'financial_year' => 'required|string',
            'gross_salary' => 'required|numeric',
            'taxable_income' => 'required|numeric',
            'tax_deducted' => 'required|numeric',
            'pan_number' => 'required|string'
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('form16s.template', [
            'employee' => User::find($validated['employee_id']),
            'financial_year' => $validated['financial_year'],
            'gross_salary' => $validated['gross_salary'],
            'taxable_income' => $validated['taxable_income'],
            'tax_deducted' => $validated['tax_deducted'],
            'pan_number' => $validated['pan_number']
        ]);

        // Save PDF
        $fileName = "form16_{$validated['employee_id']}_{$validated['financial_year']}.pdf";
        $filePath = 'form16s/' . $fileName;
        Storage::put($filePath, $pdf->output());

        Form16::create([
            ...$validated,
            'file_path' => $filePath,
            'status' => 'generated'
        ]);

        return redirect()->route('form16s.index')
            ->with('success', 'Form 16 generated successfully');
    }
}
