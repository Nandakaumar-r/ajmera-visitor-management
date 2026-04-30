<?php

namespace App\Http\Controllers;

use App\Models\Resignation;
use App\Models\FnFSettlement;
use App\Services\FnFCalculatorService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\FnFSettlementNotification; // Import the email class
use App\Models\ExitProcess;

class FnFSettlementController extends Controller
{
    private $calculatorService;

    public function __construct(FnFCalculatorService $calculatorService)
    {
        $this->calculatorService = $calculatorService;
    }

    public function index()
    {
        // Get Resignation based on LWD of the Current Date  >= LWD
        $resignations = Resignation::where('manager_last_working_day', '>=', now())->with('employee')->get();
        return view('fnf.index', compact('resignations'));
    }

    public function show($resignation_id)
    {
        $resignation = Resignation::with('employee')->findOrFail($resignation_id);
        return view('fnf.show', compact('resignation'));
    }

    public function calculate(Request $request, $resignation_id)
    {
        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'days_worked' => 'required|numeric|min:0|max:31',
            'unused_leaves' => 'required|numeric|min:0',
            'years_of_service' => 'required|numeric|min:0',
            'notice_period_served' => 'required|boolean',
            'bonus' => 'nullable|numeric|min:0',
            'incentives' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'loan_balance' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
        ]);

        $calculation = $this->calculatorService->calculateSettlement($request->all());

        return response()->json($calculation);
    }

    public function generate(Request $request, $resignation_id)
    {
        $request->validate([
            'calculation_data' => 'required|json'
        ]);

        try {
            DB::beginTransaction();

            $resignation = Resignation::findOrFail($resignation_id);
            $calculationData = json_decode($request->calculation_data, true);

            // Create F&F Settlement record
            $fnfSettlement = FnFSettlement::create([
                'resignation_id' => $resignation_id,
                'basic_salary' => $calculationData['basic_salary'] ?? 0,
                'days_worked' => $calculationData['days_worked'] ?? 0,
                'proportionate_salary' => $calculationData['proportionate_salary'] ?? 0,
                'unused_leaves' => $calculationData['unused_leaves'] ?? 0,
                'leave_encashment' => $calculationData['leave_encashment'] ?? 0,
                'gratuity' => $calculationData['gratuity'] ?? 0,
                'bonus' => $calculationData['bonus'] ?? 0,
                'incentives' => $calculationData['incentives'] ?? 0,
                'tax_deduction' => $calculationData['tax_deduction'] ?? 0,
                'loan_balance' => $calculationData['loan_balance'] ?? 0,
                'notice_recovery' => $calculationData['notice_recovery'] ?? 0,
                'other_deductions' => $calculationData['other_deductions'] ?? 0,
                'total_earnings' => $calculationData['total_earnings'] ?? 0,
                'total_deductions' => $calculationData['total_deductions'] ?? 0,
                'net_payable' => $calculationData['net_payable'] ?? 0,
                'calculation_details' => $calculationData,
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'status' => 'pending',
                'remarks' => $request->remarks
            ]);

            // Update resignation status
            $resignation->update([
                'fnf_processed' => true,
                'fnf_processed_date' => now(),
                'fnf_status' => 'pending_approval'
            ]);

            // Generate PDF
            $pdf = Pdf::loadView('fnf.settlement_pdf', compact('fnfSettlement', 'resignation')); // Create a view for the PDF
            $pdfPath = storage_path("app/public/fnf_settlement_{$fnfSettlement->id}.pdf");
            $pdf->save($pdfPath);

            ExitProcess::where('employee_id', $resignation->employee_id)->update(['payroll_clearance' => 1]);

            // Send email with PDF attachment
            Mail::to(['hr@example.com', 'finance@example.com'])->send(new FnFSettlementNotification($fnfSettlement, $pdfPath));

            DB::commit();


            return redirect()->route('fnf.show', $resignation_id)
                ->with('success', 'F&F Settlement generated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to generate F&F Settlement. ' . $e->getMessage());
        }
    }
}
