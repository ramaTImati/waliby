<?php

namespace Ramatimati\Waliby;

use Ramatimati\Waliby\App\Models\MessageTemplate;
use Ramatimati\Waliby\App\Traits\sentWATrait;
use Ramatimati\Waliby\App\Models\History;
use Ramatimati\Waliby\App\Models\Event;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Waliby {

    use sentWATrait;

    public static function SendMessage(string $phoneNumber, string $messageTemplateName){
        $sent = $this->addToQueueSingle($phoneNumber, $messageTemplateName);

        return response($sent);
    }
}