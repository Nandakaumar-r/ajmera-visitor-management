<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $password;
    public $email;
    public $url;
    public $employeeName;

    public function __construct($password, $email, $url, $employeeName)
    {
        $this->password = $password;
        $this->email = $email;
        $this->url = $url;
        $this->employeeName = $employeeName;
    }

    public function build()
    {
        return $this->markdown('emails.welcome')
                    ->subject('Welcome to Nexo HR - Your Account Details')
                    ->with([
                        'password' => $this->password,
                        'email' => $this->email,
                        'url' => $this->url,
                        'employeeName' => $this->employeeName
                    ]);
    }
}
