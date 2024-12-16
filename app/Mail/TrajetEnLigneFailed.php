<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrajetEnLigneFailed extends Mailable
{
    use Queueable, SerializesModels;

 
    public $name;
   
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->name=$data["name"];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $locale = app()->getLocale();
        return $this->from('Luggin@luggin.com', 'Luggin')
                    ->subject(trans('notifications.trajet_enligne_failed.email.subject'))
                    ->view('emails.TrajetEnLigneFailed');
    }
}
