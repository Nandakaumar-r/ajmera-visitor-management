<?php

namespace App\Http\Controllers;

use App\Imports\EmployeesImport;
use App\Models\Departments;
use App\Models\Designations;
use App\Models\Employee;
use App\Models\ExitProcess;
use App\Models\Manager;
use App\Models\Resignation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('manager', 'designation', 'department')
            ->orderBy('employee_date_of_joining', 'desc')
            ->paginate(10); // paginate directly here
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        $managers = Manager::all();
        $departments = Departments::all();
        $designations = Designations::all();
        return view('employees.create', compact('managers', 'departments', 'designations'));
    }

    public function store(Request $request)
    {
        // Validate the employee input data
        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_email' => 'required|email|unique:employees,employee_email|unique:users,email', // Ensure email is unique in both tables
            'employee_designation' => 'required|string',
            'employee_department' => 'required|string',
            'employee_date_of_joining' => 'required|date',
            'manager_id' => 'required|exists:managers,id',
        ]);

        // Generate Unique 5 digit Employee ID
        do {
            $employeeID = random_int(10000, 99999);
        } while (Employee::where('employee_id', $employeeID)->exists());

        // Attach employee id to array
        $validated['employee_id'] = $employeeID;
        // Create the employee record
        Employee::create($validated);

        // Generate a random password
        $randomPassword = Str::random(8); // Generates an 8-character random password

        // Create the user record with the employee email and hashed password
        $user = User::create([
            'name' => $validated['employee_name'],  // Set employee name as user name
            'email' => $validated['employee_email'], // Employee email for user
            'password' => Hash::make($randomPassword), // Hash the random password
        ]);

        // Assign the "Employee" role (using Spatie's Role package)
        $user->assignRole('Employee'); // Ensure the "Employee" role exists in the roles table

        // Optionally, you can send the password to the employee via email
        // Mail::to($user->email)->send(new EmployeeWelcomeMail($user, $randomPassword));

        return redirect()->route('employees.index')->with('success', 'Employee added successfully with user account created.');
    }

    public function edit($id)
    {
        $employee = Employee::findOrFail($id);
        $managers = Manager::all();
        $departments = Departments::all();
        $designations = Designations::all();
        return view('employees.edit', compact('employee', 'managers', 'departments', 'designations'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validated = $request->validate([
            'employee_name' => 'required|string|max:255',
            'employee_email' => 'required|email|unique:employees,employee_email,' . $employee->id,
            'employee_designation' => 'required|string',
            'employee_department' => 'required|string',
            'employee_date_of_joining' => 'required|date',
            'manager_id' => 'required|exists:managers,id',
        ]);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully');
    }

    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully');
    }

    // Show the import form
    public function showImportForm()
    {
        return view('employees.import');
    }

    // Handle the CSV import
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        Excel::import(new EmployeesImport, $request->file('csv_file'));

        return redirect()->route('employees.index')->with('success', 'Employees imported successfully!');
    }

    public function RelievingLetter()
    {
        $resignation = Resignation::all();
        return view('farewell', compact('resignation'));
    }

    public function sendRelievingLetter($employeeId)
    {
        // Get Employee Data
        $employee = Employee::findOrFail($employeeId);

        // Get Exit Process Data
        $exitProcess = ExitProcess::where('employee_id', $employee->employee_id)->first();

        // Company Name
        $companyName = 'Fidelis Technologies Services Pvt. Ltd.';

        // Generate PDF
        $pdf = Pdf::loadView('pdf.relieving_letter', compact('employee', 'exitProcess', 'companyName'));

        // Save PDF to storage (optional)
        $filePath = storage_path('app/public/relieving_letter_' . $employee->employee_id . '.pdf');
        $pdf->save($filePath);

        // Send Markdown email with PDF attached
        Mail::send('emails.relieving_letter', compact('employee', 'companyName'), function ($message) use ($employee, $filePath) {
            $message->to($employee->employee_email)
                ->cc('hr@example.com')  // Replace with HR email
                ->subject('Relieving Letter')
                ->attach($filePath, [
                    'as' => 'relieving_letter.pdf',
                    'mime' => 'application/pdf',
                ]);
        });

        ExitProcess::where('employee_id', $employee->employee_id)->update(['relieving_letter_issued' => true]);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function organizationChart()
    {
        $employees = Employee::with('manager', 'subordinates')->get();
        return view('organization_chart', compact('employees'));
    }

    public function sendWelcomeEmail(Request $request)
    {
        try {
            $email = $request->input('email');

            // Find the employee by email
            $employee = Employee::where('employee_email', $email)->first();

            if (!$employee) {
                return response()->json(['message' => 'Employee not found'], 404);
            }

            $password = Str::random(12); // Generate a longer, more secure password

            // Update the user record with the new password
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json(['message' => 'User account not found'], 404);
            }

            $user->password = Hash::make($password);
            $user->save();

            // Send welcome email
            Mail::to($email)->send(new WelcomeEmail(
                $password,
                $email,
                url('/login'),
                $employee->employee_name
            ));

            return response()->json([
                'message' => 'Welcome email sent successfully!',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send welcome email',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
