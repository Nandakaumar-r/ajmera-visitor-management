<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class EmployeeApiController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index(): JsonResponse
    {
        $employees = Employee::select('id', 'first_name', 'last_name', 'email', 'department_id', 'designation_id')
            ->with(['department', 'designation'])
            ->orderBy('first_name')
            ->get();
           
        return response()->json($employees);
    }
 
    /**
     * Display the specified employee.
     */
    public function show(Employee $employee): JsonResponse
    {
        $employee->load(['department', 'designation']);
        return response()->json($employee);
    }
 
    /**
     * Return employee details and manager information by employee_id.
     */
    public function details(string $employeeId): JsonResponse
    {
        // Fetch employee by primary key (employee_id is the PK on the model)
        $employee = Employee::query()
            ->where('employee_id', $employeeId)
            ->first();
 
        if (!$employee) {
            return response()->json([
                'message' => 'Employee not found',
            ], 404);
        }
 
        $manager = null;
 
        if (!empty($employee->manager_id)) {
            // First, try to resolve manager as another employee record
            $managerEmployee = Employee::query()
                ->where('employee_id', $employee->manager_id)
                ->first();
 
            if ($managerEmployee) {
                $manager = [
                    'id' => $managerEmployee->employee_id,
                    'name' => $managerEmployee->employee_name,
                    'email' => $managerEmployee->employee_email,
                    'source' => 'employees',
                ];
            } else {
                // Fall back to managers table; support possible schemas
                $managerRow = DB::table('managers')
                    ->where('manager_id', $employee->manager_id)
                    ->orWhere('id', $employee->manager_id)
                    ->first();
 
                if ($managerRow) {
                    $manager = [
                        'id' => $managerRow->manager_id ?? (string)($managerRow->id ?? ''),
                        'name' => $managerRow->manager_name ?? null,
                        'email' => $managerRow->manager_email ?? null,
                        'source' => 'managers',
                    ];
                }
            }
        }
 
        return response()->json([
            'employee' => [
                'id' => $employee->employee_id,
                'name' => $employee->employee_name,
                'email' => $employee->employee_email,
                'designation' => $employee->employee_designation,
                'department' => $employee->employee_department,
                'date_of_joining' => optional($employee->employee_date_of_joining)->toDateString() ?? $employee->employee_date_of_joining,
                'manager_id' => $employee->manager_id,
            ],
            'manager' => $manager,
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken('auth-token')->plainTextToken;
            return response()->json([
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
}
