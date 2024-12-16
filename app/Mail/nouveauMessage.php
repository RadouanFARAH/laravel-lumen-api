<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class nouveauMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $sender;
    public $message;
    public $datetime;
    public $departCity;
    public $arriveCity;
    public $tripdate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->sender=$data["sender"];
        $this->message=$data["message"];
        $this->datetime=$data["datetime"];
        $this->departCity=$data["departCity"];
        $this->arriveCity=$data["arriveCity"];
        $this->tripdate=$data["tripdate"];
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
                    ->subject(trans('notifications.nouveau_message.email.subject'))
                    ->view('emails.NouveauMessage');
    }
}
