<?php

namespace App\Mail;

use App\Models\Resignation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\Manager;
use App\Models\Employee;
use Illuminate\Queue\SerializesModels;

class ResignationCancelledEmployee extends Mailable
{
    use Queueable, SerializesModels;

    public $resignation;
    public $manager;
    public $employee;

    public function __construct(Resignation $resignation, Manager $manager, Employee $employee)
    {
        $this->resignation = $resignation;
        $this->manager = $manager;
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->subject('Your Resignation Has Been Cancelled')
            ->markdown('emails.cancelled_employee')
            ->with(['resignation' => $this->resignation]);
    }
}
