<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConditionsGeneralesUpdated extends Mailable
{
    use Queueable, SerializesModels;


    public $date;
    public $terms_link;
    public $privacy_link;
    public $previous_terms_link;
    public $conditions;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->name = $data["name"];
        $this->date = date('d-m-Y');
        $this->terms_link = $data["terms_link"];
        $this->privacy_link = $data["privacy_link"];
        $this->previous_terms_link = $data["previous_terms_link"];
        $this->conditions = $data["conditions"];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
 
        return $this
            ->subject(trans('notifications.conditions_generales_updated.email.subject'))
            ->view('emails.ConditionsGeneralesUpdated');
    }
}
