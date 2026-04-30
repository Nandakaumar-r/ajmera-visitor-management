<?php

namespace App\Http\Controllers;

use App\Models\TestEmployee;
use App\Services\ResignationPredictionService;
use Illuminate\Http\Request;

class ResignationPredictionController extends Controller
{
    protected $predictionService;

    public function __construct(ResignationPredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    public function index()
    {
        $employees = TestEmployee::with(['resignation'])->get();
        return view('resignations.predictions.index', compact('employees'));
    }

    public function show($employeeId)
    {
        $employee = TestEmployee::where('employee_id', $employeeId)
            ->with(['resignation', 'attendances', 'leaves'])
            ->firstOrFail();
            
        return view('resignations.predictions.show', compact('employee'));
    }

    public function analyze(Request $request, $employeeId)
    {
        try {
            $prediction = $this->predictionService->analyzeEmployee($employeeId);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $prediction
                ]);
            }

            return redirect()->route('resignations.predictions.show', $employeeId)
                           ->with('success', 'Analysis completed successfully');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return redirect()->route('resignations.predictions.show', $employeeId)
                           ->with('error', 'Failed to analyze: ' . $e->getMessage());
        }
    }

    public function analyzeAll()
    {
        try {
            $results = $this->predictionService->analyzeAllEmployees();
            return response()->json([
                'success' => true,
                'data' => $results
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
