<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\Manager;
use League\Csv\Reader;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UpdateEmployeeManagers extends Command
{
    protected $signature = 'update:employee-managers';
    protected $description = 'Update employee records with correct manager_id from CSV';

    public function handle()
    {
        $this->info('Starting employee-manager relationship update...');
        
        try {
            DB::beginTransaction();
            
            $csv = Reader::createFromPath(storage_path('app/CSV/Active list of Employee Details.csv'), 'r');
            $csv->setHeaderOffset(0);
            
            $records = $csv->getRecords();
            $updatedCount = 0;
            $errorCount = 0;
            
            foreach ($records as $record) {
                if (empty($record['Employee Manager Email'])) {
                    continue;
                }
                
                $manager = Manager::where('manager_email', $record['Employee Manager Email'])->first();
                
                if (!$manager) {
                    $this->warn("Manager not found for email: {$record['Employee Manager Email']}");
                    $errorCount++;
                    continue;
                }
                
                $employee = Employee::where('employee_id', $record['Employee ID'])->first();
                
                if (!$employee) {
                    $this->warn("Employee not found with ID: {$record['Employee ID']}");
                    $errorCount++;
                    continue;
                }
                
                $employee->manager_id = $manager->id;
                $employee->save();
                $updatedCount++;
            }
            
            DB::commit();
            
            $this->info("Successfully updated $updatedCount employee-manager relationships.");
            if ($errorCount > 0) {
                $this->warn("Encountered $errorCount errors during update.");
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating employee-manager relationships: ' . $e->getMessage());
            $this->error('Error updating employee-manager relationships: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
