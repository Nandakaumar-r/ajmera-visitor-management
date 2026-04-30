<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ImportEmployeeSeeder extends Seeder
{
    private function parseDate($dateString)
    {
        try {
            // Try different date formats
            $formats = [
                'd M y',           // 13 Nov 13
                'd M Y',           // 13 Nov 2013
                'j M y',           // 1 Apr 16
                'j M Y',           // 1 Apr 2016
                'Y-m-d',           // 2016-04-01
                'd/m/Y',           // 01/04/2016
                'd-m-Y',           // 01-04-2016
            ];

            foreach ($formats as $format) {
                try {
                    $date = Carbon::createFromFormat($format, trim($dateString));
                    if ($date) {
                        return $date->format('Y-m-d');
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // If no format matches, use current date
            echo "Warning: Could not parse date '{$dateString}', using current date.\n";
            return now()->format('Y-m-d');
        } catch (\Exception $e) {
            echo "Error parsing date '{$dateString}': {$e->getMessage()}\n";
            return now()->format('Y-m-d');
        }
    }

    public function run()
    {
        // Read CSV file
        $csvFile = fopen(base_path("CSV/Active list of Employee Details.csv"), "r");
        
        // Skip header row
        fgetcsv($csvFile);
        
        // First pass: Create all employees without manager relationships
        $employeeData = [];
        while (($data = fgetcsv($csvFile)) !== FALSE) {
            $employeeId = $data[0];
            $employeeName = $data[1];
            $employeeEmail = $data[2];
            $employeeDesignation = $data[3];
            $employeeDepartment = $data[4];
            $employeeDateOfJoining = $this->parseDate($data[5]);
            $managerEmail = isset($data[7]) ? $data[7] : null;

            // Skip invalid emails or generate a unique one for employees without email
            if (empty($employeeEmail) || $employeeEmail == 'N/A' || $employeeEmail == 'Nil') {
                $employeeEmail = strtolower(str_replace(' ', '.', $employeeName)) . '.' . Str::random(4) . '@fidelisgroup.in';
            }

            // Create user account
            try {
                $user = User::create([
                    'name' => $employeeName,
                    'email' => $employeeEmail,
                    'password' => Hash::make('password123'), // Default password
                ]);

                // Assign role based on designation
                if (stripos($employeeDesignation, 'manager') !== false) {
                    $user->assignRole('Manager');
                } else {
                    $user->assignRole('Employee');
                }

                // Create employee record
                $employee = Employee::create([
                    'employee_id' => $employeeId,
                    'employee_name' => $employeeName,
                    'employee_email' => $employeeEmail,
                    'employee_designation' => $employeeDesignation,
                    'employee_department' => $employeeDepartment,
                    'employee_date_of_joining' => $employeeDateOfJoining,
                    'manager_id' => null, // Will be updated in second pass
                ]);

                // Store for second pass
                $employeeData[$employeeEmail] = [
                    'employee' => $employee,
                    'manager_email' => $managerEmail
                ];

                echo "Successfully imported {$employeeName}\n";
            } catch (\Exception $e) {
                // Log any errors but continue processing
                echo "Error processing employee {$employeeName}: " . $e->getMessage() . "\n";
            }
        }
        fclose($csvFile);

        // Second pass: Update manager relationships
        foreach ($employeeData as $employeeEmail => $data) {
            if ($data['manager_email']) {
                $manager = Employee::where('employee_email', $data['manager_email'])->first();
                if ($manager) {
                    $data['employee']->manager_id = $manager->employee_id;
                    $data['employee']->save();
                    echo "Updated manager for {$data['employee']->employee_name} to {$manager->employee_name}\n";
                }
            }
        }
    }
}
