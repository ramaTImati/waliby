<?php

namespace Ramatimati\Waliby;

use Ramatimati\Waliby\App\Traits\sentWATrait;
use Ramatimati\Waliby\App\Models\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class Waliby {

    use sentWATrait;

    public static function SendMessage(string $phoneNumber, string $messageTemplateName){
        $sent = $this->addToQueueSingle($phoneNumber, $messageTemplateName);

        return response($sent);
    }

    public static function RemoveFromQueue(string $phoneNumber, string $eventId = null){
        try {
            $del = Job::where('phone_number', $phoneNumber)
                ->when($eventId, function($sub) use ($eventId){
                    return $sub->where('event_id', $eventId);
                })
                ->delete();

            Log::info('Success deleted data from queue '.$phoneNumber);
            return response()->json([
                'message' => 'Success deleted data from queue'
            ], 200);
        } catch (\Throwable $th) {
            Log::info('Error delete data from queue : '.$th->getMessage().' on line '.$th->getLine());
            return response()->json([
                'message' => $th->getMessage().' on line '.$th->getLine()
            ], 500);
        }
    }
}