<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrajetEnLigne extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $date;
    public $source;
    public $destination;
   
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->name=$data["name"];
        $this->date=$data["date"];
        $this->source=$data["source"];
        $this->destination=$data["destination"];
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
                    ->subject(trans('notifications.trajet_enligne.email.subject'))
                    ->view('emails.TrajetEnLigne');
    }
}
