<?php

namespace App\Jobs;
use Illuminate\Support\Facades\Mail;

class MailJobs extends Job
{

    public  $data;
    public $callback;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($callback,$data)
    {
        $this->data = $data;
        $this->callback = $callback;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      
        Mail::to(["winorg68@gmail.com","innov237@gmail.com",$this->data['email']])->send(new $this->callback($this->data));
    }
}
