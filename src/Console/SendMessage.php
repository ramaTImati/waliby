<?php

namespace Ramatimati\Waliby\Console;

use Ramatimati\Waliby\App\Models\JobLog;
use Ramatimati\Waliby\App\Models\Meta;
use Ramatimati\Waliby\App\Models\Job;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Ramatimati\Waliby\Waliby;
use GuzzleHttp\Client;
use Carbon\Carbon;

class SendMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waliby:send-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'function to send single whatsapp message from WALIBY every 30s';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $getSingleMessage = Job::orderBy('created_at', 'ASC')->first();

        if (isset($getSingleMessage)) {
            $insertToLog = JobLog::create([
                'event_id' => $getSingleMessage->event_id ?? null,
                'phone_number' => $getSingleMessage->phone_number,
                'text' => $getSingleMessage->text,
                'reserved_at' => Carbon::now()->format('Y-m-d H:i:s')
            ])->id;
    
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
            $fetchBody = Meta::where('name', 'REQUEST_BODY')->first();
            $fetchBody = $fetchBody->value;
            $checkNested = explode('+', $fetchBody);
            $countCheckNested = count($checkNested);
            if ($countCheckNested == 1) {
                $rawBody = explode(',', $fetchBody);
                $body = [];
                foreach ($rawBody as $kb => $vb) {
                    $explode = explode('=', $vb);
                    if ($explode[1] == '~phoneNumber~') {
                        $dynamic = $getSingleMessage->phone_number;
                    }elseif ($explode[1] == '~message~') {
                        $dynamic = $getSingleMessage->text;
                    }else{
                        $dynamic = null;
                    }
                    $keyValue = str_replace('#array#', '', $explode[0]);
                    $keyValue = str_replace('#string#', '', $keyValue);
                    $body[$keyValue] = $dynamic;
                }
            }elseif ($countCheckNested == 2) {
    
            }else{
    
            }
        
            try {
                DB::beginTransaction();
    
                $endpoint = config('waliby.endpoint');
                $client = new Client();
                $request = $client->post($endpoint, [
                    'headers' => $headers,
                    'json' => $body,
                ]);
        
                $response = $request->getBody()->getContents();
                Log::info('WALIBY CONSOLE POST : '. $response);
                $response = $this->storeHistory($response);
                
                JobLog::where('id', $insertToLog)->update([
                    'finished_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'status' => 'success'
                ]);

                Job::where('id', $getSingleMessage->id)->delete();

                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                Log::error('WALIBY CONSOLE (SendMessage.php) : '.$th->getMessage().' on line '.$th->getLine());
                JobLog::where('id', $insertToLog)->update([
                    'finished_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'status' => 'error',
                    'exception' => $th->getMessage()
                ]);
            }
        }
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
            Log::error('api response not recognized by waliby');
        }

        try {
            DB::beginTransaction();

            Waliby::StoreHistory($responseValue);

            DB::commit();
            Log::info('WALIBY CONSOLE (message history sent and stored)');
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('WALIBY CONSOLE (message history)'.$th->getMessage().' on line '.$th->getLine());
        }   
    }
}
