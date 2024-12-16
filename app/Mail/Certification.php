<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Certification extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $isSuccess;

    /**
     * Create a new message instance.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $this->name = $data["user_pseudo"];
        $this->isSuccess = $data["isSuccess"];
    }
 
    /**
     * Build the message.
     *
     * @return $this
     */
    
    public function build()
    {

        $subject = $this->isSuccess ? trans('notifications.certification_success.email.subject') : trans('notifications.certification_failed.email.subject');
        $locale = app()->getLocale();
        return $this
        ->subject($subject)
        ->view($isSuccess ? 'emails.CertificationSuccess' : 'emails.CertificationFailed');
    }
}
