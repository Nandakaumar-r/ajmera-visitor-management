<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Manager;
use League\Csv\Reader;
use Illuminate\Support\Facades\Log;

class ImportManagersFromCSV extends Command
{
    protected $signature = 'import:managers';
    protected $description = 'Import managers from the active employee list CSV';

    public function handle()
    {
        $this->info('Starting manager import...');
        
        try {
            $csv = Reader::createFromPath(storage_path('app/CSV/Active list of Employee Details.csv'), 'r');
            $csv->setHeaderOffset(0);
            
            $managers = [];
            $records = $csv->getRecords();
            
            foreach ($records as $record) {
                if (!empty($record['Employee Manager Name']) && !empty($record['Employee Manager Email'])) {
                    $managers[$record['Employee Manager Email']] = [
                        'manager_name' => $record['Employee Manager Name'],
                        'manager_email' => $record['Employee Manager Email']
                    ];
                }
            }
            
            foreach ($managers as $manager) {
                Manager::updateOrCreate(
                    ['manager_email' => $manager['manager_email']],
                    ['manager_name' => $manager['manager_name']]
                );
            }
            
            $this->info('Successfully imported ' . count($managers) . ' managers.');
        } catch (\Exception $e) {
            Log::error('Error importing managers: ' . $e->getMessage());
            $this->error('Error importing managers: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
