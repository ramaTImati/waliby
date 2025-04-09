<?php 

namespace Ramatimati\Waliby\App\Traits;

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
            \Log::info('WALIBY TRAIT : '.$th->getMessage());

            $code = 500;
            $response = [
                'message' => 'failed to process event'
            ];
        }

        return response()->json($response, $code);
    }
}