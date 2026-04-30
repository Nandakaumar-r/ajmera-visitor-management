<?php

namespace App\Imports;

use App\Models\Departments;
use App\Models\Designations;
use App\Models\Employee;
use App\Models\Manager;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
/**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Find or create designation
        $designation = Designations::firstOrCreate(['designation_name' => $row['employee_designation']]);

        // Find or create department
        $department = Departments::firstOrCreate(['department_name' => $row['employee_department']]);

        return new Employee([
            'employee_id'            => $row['employee_id'],
            'employee_name'          => $row['employee_name'],
            'employee_email'         => $row['employee_email'],
            'employee_designation'   => $designation->id,
            'employee_department'    => $department->id,
            'employee_date_of_joining' => $this->parseDate($row['employee_date_of_joining']),
            'manager_id'             => $this->getManagerIdByEmail($row['manager_email']),
        ]);
    }

    /**
    * Convert date format to Y-m-d.
    */
    private function parseDate($date)
    {
        try {
            return Carbon::createFromFormat('d M y', $date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null; // or handle error accordingly
        }
    }

    /**
    * Find manager ID based on manager's email.
    */
    private function getManagerIdByEmail($email)
    {
        $manager = Manager::where('manager_email', $email)->first();
        return $manager ? $manager->id : dd('No manager found with email: ' . $email);
    }
}
