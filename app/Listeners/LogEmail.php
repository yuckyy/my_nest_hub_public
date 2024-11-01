<?php
namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class LogEmail
{
    public function handle(MessageSent $event)
    {
        $message = $event->message;

        $subject = $message->getSubject();
        $toObj = $event->message->getTo();
        foreach($toObj as $key => $val) {
            $to = empty($key) ? $val : $key;
        }
        // The Swift_Message has a __toString method so you should be able to log it ;)
        Log::channel('mail_log')->info($to . " << " . $subject);
    }


    private function parseAddresses(array $array): array
    {
        $parsed = [];
        foreach($array as $address) {
            $parsed[] = $address->getAddress();
        }
        return $parsed;
    }

}
