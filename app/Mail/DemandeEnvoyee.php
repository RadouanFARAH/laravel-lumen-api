<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DemandeEnLigne extends Mailable
{
    use Queueable, SerializesModels;

 
    public $name;
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
                    ->subject(trans('notifications.demande_envoyee.email.subject'))
                    ->view('emails.DemandeEnvoyee');
    }
}
