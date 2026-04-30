<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PayslipController extends Controller
{
    public function index()
    {
        $payslips = Payslip::with('employee')
            ->where('employee_id', Auth::user()->id)
            ->latest()
            ->paginate(12);

        return view('payslips.index', compact('payslips'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:users,id',
            'month' => 'required|string',
            'year' => 'required|integer',
            'basic_salary' => 'required|numeric',
            'allowances' => 'required|numeric',
            'deductions' => 'required|numeric'
        ]);

        $netSalary = $validated['basic_salary'] + $validated['allowances'] - $validated['deductions'];

        // Generate PDF
        $pdf = Pdf::loadView('payslips.template', [
            'employee' => User::find($validated['employee_id']),
            'month' => $validated['month'],
            'year' => $validated['year'],
            'basic_salary' => $validated['basic_salary'],
            'allowances' => $validated['allowances'],
            'deductions' => $validated['deductions'],
            'net_salary' => $netSalary
        ]);

        // Save PDF
        $fileName = "payslip_{$validated['employee_id']}_{$validated['month']}_{$validated['year']}.pdf";
        $filePath = 'payslips/' . $fileName;
        Storage::put($filePath, $pdf->output());

        // Create payslip record
        Payslip::create([
            ...$validated,
            'net_salary' => $netSalary,
            'file_path' => $filePath,
            'status' => 'generated'
        ]);

        return redirect()->route('payslips.index')
            ->with('success', 'Payslip generated successfully');
    }
}
