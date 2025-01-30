<?php 

namespace Ramatimati\Waliby\App\Traits;

use Ramatimati\Waliby\App\Models\Event;
use Ramatimati\Waliby\App\Models\Meta;
use Illuminate\Support\Facades\DB;
use Ramatimati\Waliby\Waliby;
use GuzzleHttp\Client;
use Carbon\Carbon;

trait sentWATrait{
    public function send($event_id){
        $endpoint = config('waliby.endpoint');

        $requestFormat = $this->processData($event_id);
        if ($requestFormat['code'] == 200) {
            $client = new Client();
            $request = $client->post($endpoint, [
                'headers' => $requestFormat['data']['headers'],
                'json' => $requestFormat['data']['body'],
            ]);
    
            $response = $request->getBody()->getContents();
            $response = $this->storeHistory($response);
            $code = 200;
            $message = $response;
        }else{
            $code = 500;
            $response = 'request format not supported';
        }

        return response()->json([
            'code' => $code,
            'message' => $message
        ], $code);
    }

    private function processData($event_id){
        $token = config('waliby.token');

        // process header from database
        $fetchHeader = Meta::where('name', 'REQUEST_HEADERS')->first();
        $fetchHeader = explode(',', $fetchHeader->value);
        $headers = [];
        foreach ($fetchHeader as $key => $value) {
            $explode = explode('=', $value);
            $headers[$explode[0]] = $explode[1] == '~token~' ? $token : $explode[1];
        }

        // process body from database
        $table = config('waliby.phoneBookTable');
        $column = DB::connection()->getSchemaBuilder()->getColumnListing($table);
        $phoneColumn = config('waliby.phoneNumberColumn');
        $nameColumn = config('waliby.nameColumn');

        $event = Event::with('template')->where('id', $event_id)->first();
        $explodeReceiverParams = explode(',', $event->receiver_params);
        $params = [];
        foreach ($explodeReceiverParams as $key => $value) {
            $explode = explode('=', $value);
            $params[$key]['param'] = $explode[0];
            $params[$key]['value'] = $explode[1];
        }
        $countParams = count($params);
        $fetchReceiver = DB::table($table)->when($countParams == 1, function($sub) use ($params){
            return $sub->where($params[0]['param'], $params[0]['value']);
        })
        ->when($countParams == 2, function($sub) use ($params){
            return $sub->where($params[0]['param'], $params[0]['value'])->where($params[1]['param'], $params[1]['value']);
        })
        ->when($countParams == 3, function($sub) use ($params){
            return $sub->where($params[0]['param'], $params[0]['value'])->where($params[1]['param'], $params[1]['value'])->where($params[2]['param'], $params[2]['value']);
        })
        ->get();

        $fetchBody = Meta::where('name', 'REQUEST_BODY')->first();
        $fetchBody = $fetchBody->value;
        $checkNested = explode('+', $fetchBody);
        $countCheckNested = count($checkNested);

        if ($countCheckNested == 1) {
            $code = 200;
            $rawBody = explode(',', $fetchBody);
            $body = [];
            foreach ($fetchReceiver as $key => $value) {
                foreach ($rawBody as $kb => $vb) {
                    $explode = explode('=', $vb);
                    if ($explode[1] == '~phoneNumber~') {
                        $dynamic = $value->$phoneColumn;
                    }elseif ($explode[1] == '~message~') {
                        $message = $event->template->message;
                        preg_match_all('/~(.*?)~/', $message, $matches);
                        foreach ($matches[1] as $kpreg => $vpreg) {
                            if (in_array($vpreg, $column)) {
                                $message = str_replace('~'.$vpreg.'~', $value->$vpreg, $message);
                            }
                        }
                        $dynamic = $message;
                    }else{
                        $dynamic = null;
                    }
                    $keyValue = str_replace('#array#', '', $explode[0]);
                    $keyValue = str_replace('#string#', '', $keyValue);
                    $body[$key][$keyValue] = $dynamic;
                }
            }
            $bodyFormat = $body;
        }elseif ($countCheckNested == 2) {
            $rawBody = explode(',', $checkNested[1]);
            $body = [];
            foreach ($fetchReceiver as $key => $value) {
                foreach ($rawBody as $kb => $vb) {
                    $explode = explode('=', $vb);
                    if ($explode[1] == '~phoneNumber~') {
                        $dynamic = $value->$phoneColumn;
                    }elseif ($explode[1] == '~message~') {
                        $message = $event->template->message;
                        preg_match_all('/~(.*?)~/', $message, $matches);
                        foreach ($matches[1] as $kpreg => $vpreg) {
                            if (in_array($vpreg, $column)) {
                                $message = str_replace('~'.$vpreg.'~', $value->$vpreg, $message);
                            }
                        }
                        $dynamic = $message;
                    }else{
                        $dynamic = null;
                    }
                    $keyValue = str_replace('#array#', '', $explode[0]);
                    $keyValue = str_replace('#string#', '', $keyValue);
                    $body[$key][$keyValue] = $dynamic;
                }
            }
            if (str_contains($checkNested[1], '#array#')) {
                $code = 200;
                $bodyFormat = [str_replace('=', '', $checkNested[0]) => $body];
            }elseif (str_contains($checkNested[1], '#string#')) {
                $code = 200;
                $res = json_encode($body,JSON_UNESCAPED_SLASHES);
                $bodyFormat = [str_replace('=', '', $checkNested[0]) => $res];
            }else{
                $code = 500;
                $bodyFormat = null;
            }
        }else{
            $code = 500;
        }

        try {
            Event::where('id', $event_id)->update([
                'last_processed' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $th) {
            \Log::info('WALIBY TRAIT : '.$th->getMessage());
        }

        $requestFormat = [
            'code' => $code,
            'data' => [
                'headers' => $headers,
                'body' => $bodyFormat
            ]
        ];

        return $requestFormat;
    }

    private function storeHistory($response){
        $fetchFormat = Meta::where('name', 'RESPONSE')->first();
        $format = json_decode($fetchFormat->value, true);

        $responseType = '';
        $resPhoneNumber = '';
        $resId = '';
        $resStatus = '';
        $resMessage = '';
        $path = '';

        foreach ($format as $key => $value) {
            if (is_array($value)) { 
                foreach ($value as $karray => $varray) {
                    if (is_array($varray)) { 
                        $path = $karray;
                        foreach ($varray as $kchild => $vchild) {
                            $responseType = 'hasChild';
                            if(is_array($vchild)){
                                $path = $key.'/'.$karray;
                                foreach ($vchild as $kgc => $vgc) {
                                    if ($vgc == '~id~') {
                                        $resId = $kgc;
                                    }elseif ($vgc == '~phoneNumber~') {
                                        $resPhoneNumber = $kgc;
                                    }elseif ($vgc == '~status~') {
                                        $resStatus = $kgc;
                                    }elseif ($vgc == '~message~') {
                                        $resMessage = $kgc;
                                    }
                                }
                            }elseif ($vchild == '~phoneNumber~') {
                                $resPhoneNumber = $kchild;
                            }elseif ($vchild == '~status~') {
                                $resStatus = $kchild;
                            }elseif ($vchild == '~id~') {
                                $resId = $kchild;
                            }elseif ($vchild == '~message~') {
                                $resMessage = $kchild;
                            }
                        }
                    }
                }
            }elseif ($value == '~id~') {
                $responseType = 'single';
                $resId = $key;
            }elseif ($value == '~phoneNumber~') {
                $responseType = 'single';
                $resPhoneNumber = $key;
            }elseif ($value == '~status~') {
                $responseType = 'single';
                $resStatus = $key;
            }elseif ($value == '~message~') {
                $responseType = 'single';
                $resMessage = $key;
            }
        }

        $response = json_decode($response, true);
        $responseValue = [];
        if ($responseType == 'single') {
            foreach ($response as $key => $value) {
                if ($key == $resId) {
                    foreach ($value as $kchild => $vchild) {
                        $responseValue[$kchild]['message_id'] = $vchild;
                    }
                }
                if ($key == $resPhoneNumber) {
                    foreach ($value as $kchild => $vchild) {
                        $responseValue[$kchild]['phone_number'] = $vchild;
                        $responseValue[$kchild]['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                        $responseValue[$kchild]['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
                    }
                }
                if ($key == $resStatus) {
                    foreach ($responseValue as $kchild => $vchild) {
                        $responseValue[$kchild]['status'] = $value;
                    }
                }
            }
        }elseif ($responseType == 'hasChild') {
            $pathId = explode('/', $path);
            $countPath = count($pathId);
            if ($countPath == 2) {
                foreach ($response[$pathId[0]][$pathId[1]] as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $kc => $vc) {
                            if ($kc == $resId) {
                                $responseValue[$key]['message_id'] = $vc;
                            }
                            if ($kc == $resPhoneNumber) {
                                $responseValue[$key]['phone_number'] = $vc;
                            }
                            if ($kc == $resStatus) {
                                $responseValue[$key]['status'] = $vc;
                            }
                            if ($kc == $resMessage) {
                                $responseValue[$key]['message_text'] = $vc;
                            }
                            $responseValue[$key]['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                            $responseValue[$key]['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
                        }
                    }else{
                        // return $value;
                    }
                }
            }
        }else{
            return 'api response not recognized by waliby';
        }

        try {
            DB::beginTransaction();

            Waliby::StoreHistory($responseValue);

            DB::commit();
            return 'sent and stored';
        } catch (\Throwable $th) {
            DB::rollback();
            return $th->getMessage();
        }   
    }
}