<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeInsuranceRequest;
use App\Http\Requests\UpdateEmployeeInsuranceRequest;
use App\Http\Resources\EmployeeInsuranceResource;
use App\Models\EmployeeInsurance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EmployeeInsuranceController extends Controller
{
    /**
     * Display a listing of insurance records
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = EmployeeInsurance::query();

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by employee ID or name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('employee_name', 'like', "%{$search}%");
            });
        }

        $insurances = $query->orderBy('created_at', 'desc')->paginate(15);

        return EmployeeInsuranceResource::collection($insurances);
    }

    /**
     * Store a newly created insurance record
     */
    public function store(StoreEmployeeInsuranceRequest $request): JsonResponse
    {
        $insurance = EmployeeInsurance::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Insurance details submitted successfully',
            'data' => new EmployeeInsuranceResource($insurance),
        ], 201);
    }

    /**
     * Display the specified insurance record
     */
    public function show(EmployeeInsurance $insurance): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new EmployeeInsuranceResource($insurance),
        ]);
    }

    /**
     * Update the specified insurance record
     */
    public function update(UpdateEmployeeInsuranceRequest $request, EmployeeInsurance $insurance): JsonResponse
    {
        $insurance->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Insurance details updated successfully',
            'data' => new EmployeeInsuranceResource($insurance),
        ]);
    }

    /**
     * Remove the specified insurance record
     */
    public function destroy(EmployeeInsurance $insurance): JsonResponse
    {
        $insurance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Insurance record deleted successfully',
        ]);
    }

    /**
     * Get statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_submissions' => EmployeeInsurance::count(),
            'pending' => EmployeeInsurance::pending()->count(),
            'approved' => EmployeeInsurance::approved()->count(),
            'total_premium' => EmployeeInsurance::sum('premium'),
            'with_spouse' => EmployeeInsurance::whereNotNull('spouse_name')->count(),
            'with_children' => EmployeeInsurance::where(function ($q) {
                $q->whereNotNull('child1_name')
                  ->orWhereNotNull('child2_name');
            })->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Export to CSV
     */
    public function export(): JsonResponse
    {
        $insurances = EmployeeInsurance::all();

        $csvData = [];
        $csvData[] = [
            'Employee ID',
            'Employee Name',
            'Spouse Name',
            'Spouse DOB',
            'Spouse Aadhar',
            'Spouse Gender',
            'Child 1 Name',
            'Child 1 DOB',
            'Child 1 Aadhar',
            'Child 1 Gender',
            'Child 2 Name',
            'Child 2 DOB',
            'Child 2 Aadhar',
            'Child 2 Gender',
            'Monthly Premium',
            'Status',
            'Submitted At',
        ];

        foreach ($insurances as $insurance) {
            $csvData[] = [
                $insurance->employee_id,
                $insurance->employee_name,
                $insurance->spouse_name ?? '-',
                $insurance->spouse_dob?->format('Y-m-d') ?? '-',
                $insurance->spouse_aadhar ?? '-',
                $insurance->spouse_gender ?? '-',
                $insurance->child1_name ?? '-',
                $insurance->child1_dob?->format('Y-m-d') ?? '-',
                $insurance->child1_aadhar ?? '-',
                $insurance->child1_gender ?? '-',
                $insurance->child2_name ?? '-',
                $insurance->child2_dob?->format('Y-m-d') ?? '-',
                $insurance->child2_aadhar ?? '-',
                $insurance->child2_gender ?? '-',
                '₹' . $insurance->premium,
                ucfirst($insurance->status),
                $insurance->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $csvData,
        ]);
    }
}

