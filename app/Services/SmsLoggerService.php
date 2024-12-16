<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsLoggerService
{
    /**
     * Log the SMS details locally instead of sending it.
     *
     * @param string $phoneNumber
     * @param string $message
     * @return void
     */
    public function logSms($phoneNumber, $message)
    {
        Log::info("Mock SMS Sent");
        Log::info("Recipient: {$phoneNumber}");
        Log::info("Message: {$message}");
    }
}
