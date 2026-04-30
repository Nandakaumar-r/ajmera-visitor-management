<?php

namespace App\Console\Commands;

use App\Mail\BirthdayPhotoRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendBirthdayPhotoRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:photo-request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a photo request to employees with birthdays tomorrow';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->format('m-d');
        $users = User::whereRaw("DATE_FORMAT(date_of_birth, '%m-%d') = ?", [$tomorrow])->get();

        foreach ($users as $user) {
            Mail::to($user->email)->send(new BirthdayPhotoRequest($user));
        }

        $this->info('Birthday photo request emails sent.');
    }
}
