<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use League\Csv\Reader;
use Illuminate\Support\Facades\DB;

class ImportEmployeesFromCsvSeeder extends Seeder
{
    public function run(): void
    {
        $csv = Reader::createFromPath(base_path('CSV/Active list of Employee Details.csv'), 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $managers = [];
        $employees = [];

        // First pass: Collect all unique managers
        foreach ($records as $record) {
            if (!empty($record['Employee Manager Email']) && $record['Employee Manager Email'] !== 'N/A') {
                $managers[$record['Employee Manager Email']] = [
                    'name' => $record['Employee Manager Name'],
                    'email' => $record['Employee Manager Email'],
                ];
            }
        }

        // Create manager accounts first
        foreach ($managers as $managerEmail => $managerData) {
            $user = User::firstOrCreate(
                ['email' => $managerEmail],
                [
                    'name' => $managerData['name'],
                    'password' => Hash::make('password123'), // Default password
                ]
            );
            $user->assignRole('Manager');

            // Create manager employee record
            Employee::firstOrCreate(
                ['employee_email' => $managerEmail],
                [
                    'employee_name' => $managerData['name'],
                    'employee_designation' => 'Manager',
                    'employee_department' => 'Management',
                    'employee_date_of_joining' => now(),
                    'manager_id' => null,
                ]
            );
        }

        // Reset the CSV reader for the second pass
        $csv = Reader::createFromPath(base_path('CSV/Active list of Employee Details.csv'), 'r');
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        // Second pass: Create employee accounts
        foreach ($records as $record) {
            if (empty($record['Employee Email']) || $record['Employee Email'] === 'N/A') {
                continue;
            }

            // Skip if this person is already created as a manager
            if (isset($managers[$record['Employee Email']])) {
                continue;
            }

            // Create user account
            $user = User::firstOrCreate(
                ['email' => $record['Employee Email']],
                [
                    'name' => $record['Employee Name'],
                    'password' => Hash::make('password123'), // Default password
                ]
            );
            $user->assignRole('Employee');

            // Find manager's employee record
            $managerId = null;
            if (!empty($record['Employee Manager Email']) && $record['Employee Manager Email'] !== 'N/A') {
                $managerEmployee = Employee::where('employee_email', $record['Employee Manager Email'])->first();
                if ($managerEmployee) {
                    $managerId = $managerEmployee->id;
                }
            }

            // Create employee record
            $joiningDate = \DateTime::createFromFormat('d M y', $record['Employee Date of Joining']);
            if (!$joiningDate) {
                $joiningDate = now();
            }

            Employee::firstOrCreate(
                ['employee_email' => $record['Employee Email']],
                [
                    'employee_name' => $record['Employee Name'],
                    'employee_designation' => $record['Employee Designation'],
                    'employee_department' => $record['Employee Department'],
                    'employee_date_of_joining' => $joiningDate,
                    'manager_id' => $managerId,
                ]
            );
        }
    }
}
