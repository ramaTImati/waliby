<?php

namespace Ramatimati\Waliby;

use Ramatimati\Waliby\App\Models\MessageTemplate;
use Ramatimati\Waliby\App\Models\History;
use Ramatimati\Waliby\App\Models\Event;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Carbon\Carbon;

class Waliby {

    public static function test(){
        return 'test';
    }

    public static function GetMessage(array $params){
        $id = $params['templateId'];
        $phoneNumber = $params['phoneNumber'];

        $table = config('waliby.phoneBookTable');
        $column = DB::connection()->getSchemaBuilder()->getColumnListing($table);
        $phoneNumberColumn = config('waliby.phoneNumberColumn');
        $nameColumn =config('waliby.nameColumn');

        $data = DB::table($table)
            ->where($phoneNumberColumn, $phoneNumber)
            ->first();
        
        try {
            $template = MessageTemplate::where('id', $id)->first();

            $message = $template->message;
            preg_match_all('/~(.*?)~/', $template->message, $matches);
            foreach ($matches[1] as $kpreg => $preg) {
                if (in_array($preg, $column)) {
                    $message = str_replace('~'.$preg.'~', $data->$preg, $message);
                }
                if ($preg == 'date') {
                    $message = str_replace('~'.$preg.'~', Carbon::now()->format('d F Y'), $message);
                }
            }

            $result = [
                'templateId' => $id,
                'phoneNumber' => $phoneNumber,
                'message' => $message
            ];

            return $result;
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => $th->getMessage().' on line '.$th->getLine()
            ], 500);
        }
    }

    public static function GetEvent(array $params){
        $templateId = $params['templateId'];
        $eventId = $params['eventId'];

        $table = config('waliby.phoneBookTable');
        $column = DB::connection()->getSchemaBuilder()->getColumnListing($table);
        $phoneNumberColumn = config('waliby.phoneNumberColumn');

        $eventReceiver = Event::with('template')->where('id', $eventId)->first();
        $receiver = json_decode($eventReceiver->to, true);
        
        $data = DB::table($table)->whereIn($phoneNumberColumn, $receiver)->get();

        $result = [];
        $replacer = [];
        preg_match_all('/~(.*?)~/', $eventReceiver->template->message, $matches);
        foreach ($matches[1] as $kpreg => $preg) {
            if (in_array($preg, $column)) {
                $replacer[$kpreg]['variable'] = $preg;
                $replacer[$kpreg]['key'] = $kpreg;
            }
            if ($preg == 'date') {
                $replacer[$kpreg]['variable'] = $preg;
                $replacer[$kpreg]['key'] = $kpreg;
            }
        }

        foreach ($data as $key => $value) {   
            $replacement = '';
            foreach ($replacer as $krep => $vrep) {
                $dyn = $vrep['variable'];
                if ($dyn != 'date') {
                    $replacement = str_replace('~'.$vrep['variable'].'~', $value->$dyn, $eventReceiver->template->message);
                }else{
                    $replacement = str_replace('~date~', Carbon::now()->format('d F Y'), $replacement);
                }
            }       
            $result[$key]['phone'] = $value->$phoneNumberColumn;
            $result[$key]['message'] = $replacement;
        }

        return $result;
    }

    public static function SendMessage(string $type = 'POST', string $endpoint, array $header, array $payload){
        // $client = new Client();
        // $request = $client->request($type, $endpoint, [
        //     'headers' => $header,
        //     'json' => $payload,
        // ]);

        // $response = $request->getBody()->getContents();
        $response = '{
            "status": true,
            "message": "Text Message is pending and waiting to be processed. with error: 0",
            "data": {
                "device_id": "7RRG1P",
                "quota": "unlimited",
                "messages": [
                    {
                        "id": "164e7a41-cf93-4c7e-8644-3e970f6ccb9d",
                        "phone": "6282315535393",
                        "message": "selamat pagi saudara Rifqi Ramadhan, sekarang tanggal 24 October 2024",
                        "status": "pending",
                        "ref_id": null
                    },
                    {
                        "id": "33761f08-4ae2-41c7-b16b-e15fab8ee215",
                        "phone": "628986957465",
                        "message": "selamat pagi saudara rama, sekarang tanggal 24 October 2024",
                        "status": "pending",
                        "ref_id": null
                    }
                ]
            }
        }';
        $response = json_decode($response);
        // dd($response->data);
        $data = $response->data->messages;
        foreach ($data as $key => $value) {
            History::create([
                'message_id' => $value->id,
                'phone_number' => $value->phone,
                'message_text' => $value->message,
                'status' => $value->status,
            ]);
        }
        // return response()->json($response->data);


        return response()->json($response);
    }
}