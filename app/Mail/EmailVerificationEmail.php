<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $otp;
    public function __construct($user, $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.emailVerificationEmail')->subject('Welcome to Dalorum')->with(['user' => $this->user, 'otp' => $this->otp]);
    }
}
