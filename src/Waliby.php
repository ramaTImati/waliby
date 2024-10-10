<?php

namespace Ramatimati\Waliby;

use Ramatimati\Waliby\App\Models\MessageTemplate;
use GuzzleHttp\Client;

class Waliby {

    public static function test(){
        return 'test';
    }

    public static function getMessage(array $params){
        $id = $params['templateId'];
        
        try {
            $template = MessageTemplate::where('id', $id)->first();

            return response()->json([
                'code' => 200,
                'message' => $template->message
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => $th->getMessage().' on line '.$th->getLine()
            ], 500);
        }
    }

    public static function SendMessage(string $type = 'POST', string $endpoint, array $header, array $payload){
        $client = new Client();
        $request = $client->request($type, $endpoint, [
            'headers' => $header,
            'json' => $payload,
        ]);

        $response = $request->getBody()->getContents();
        $response = json_decode($response);

        return response()->json($response);
    }
}