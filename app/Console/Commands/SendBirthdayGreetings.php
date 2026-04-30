<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\BirthdayGreetingMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendBirthdayGreetings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:greetings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send birthday greetings to employees';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('m-d'); // Get today's month and day

        $employees = User::whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') = ?", [$today])->get();

        foreach ($employees as $employee) {
            Mail::to($employee->email)->send(new BirthdayGreetingMail($employee));
            $this->info("Birthday greeting sent to: {$employee->name}");
        }

        return 0;
    }
}
