<?php 

namespace Ramatimati\Waliby\App\Traits;

use Ramatimati\Waliby\App\Models\MessageTemplate;
use Ramatimati\Waliby\App\Models\Event;
use Ramatimati\Waliby\App\Models\Meta;
use Ramatimati\Waliby\App\Models\Job;
use Illuminate\Support\Facades\DB;
use Ramatimati\Waliby\Waliby;
use GuzzleHttp\Client;
use Carbon\Carbon;

trait sentWATrait{
    private function addToQueue($event_id){
        // process body from database
        $connection = config('waliby.phoneBookConnecction');
        $table = config('waliby.phoneBookTable');
        $column = DB::connection($connection)->getSchemaBuilder()->getColumnListing($table);
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
        $fetchReceiver = DB::connection($connection)->table($table)->when($countParams == 1, function($sub) use ($params){
            return $sub->where($params[0]['param'], $params[0]['value']);
        })
        ->when($countParams == 2, function($sub) use ($params){
            return $sub->where($params[0]['param'], $params[0]['value'])->where($params[1]['param'], $params[1]['value']);
        })
        ->when($countParams == 3, function($sub) use ($params){
            return $sub->where($params[0]['param'], $params[0]['value'])->where($params[1]['param'], $params[1]['value'])->where($params[2]['param'], $params[2]['value']);
        })
        ->get();

        $target = [];
        foreach ($fetchReceiver as $key => $value) {
            $message = $event->template->message;
            preg_match_all('/~(.*?)~/', $message, $matches);
            foreach ($matches[1] as $kpreg => $vpreg) {
                if (in_array($vpreg, $column)) {
                    $message = str_replace('~'.$vpreg.'~', $value->$vpreg, $message);
                }
            }

            $target[$key]['event_id'] = $event_id;
            $target[$key]['phone_number'] = $value->$phoneColumn;
            $target[$key]['text'] = $message;
            $target[$key]['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
            $target[$key]['updated_at'] = Carbon::now()->format('Y-m-d H:i:s');
        }

        try {
            DB::beginTransaction();

            Job::insert($target);
            Event::where('id', $event_id)->update([
                'last_processed' => Carbon::now()->format('Y-m-d H:i:s')
            ]);

            DB::commit();

            $code = 200;
            $response = [
                'message' => 'Added to queue'
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::info('WALIBY TRAIT EVENT : '.$th->getMessage());

            $code = 500;
            $response = [
                'message' => 'failed to process event'
            ];
        }

        return response()->json($response, $code);
    }

    private function addToQueueSingle($phoneNumber, $messageTemplateName){
        $connection = config('waliby.phoneBookConnection');
        $table = config('waliby.phoneBookTable');
        $phoneNumberColumn = config('waliby.phoneNumberColumn');

        $receiver = DB::connection($connection)->table($table)->where($phoneNumberColumn, $phoneNumber)->first();
        if ($receiver == null) {
            return reponse()->json([
                'code' => 500,
                'message' => 'phone number not found'
            ], 500);
        }

        $template = MessageTemplate::where('name', $messageTemplateName)->first();

        // process message
        $message = $template->message;
        preg_match_all('/~(.*?)~/', $message, $matches);
        foreach ($matches[1] as $kpreg => $vpreg) {
            if (in_array($vpreg, $column)) {
                $message = str_replace('~'.$vpreg.'~', $receiver->$vpreg, $message);
            }
        }

        // store
        try {
            DB::beginTransaction();

            Job::create([
                'event_id' => null,
                'phone_number' => $phoneNumber,
                'text' => $message,
            ]);

            DB::commit();

            $code = 200;
            $response = [
                'message' => 'Single message added to queue'
            ];
        } catch (\Throwable $th) {
            DB::rollback();
            \Log::info('WALIBY TRAIT SINGLE MESSAGE : '.$th->getMessage());

            $code = 500;
            $response = [
                'message' => 'failed to process single message'
            ];
        }

        return response()->json($response, $code);
    }
}