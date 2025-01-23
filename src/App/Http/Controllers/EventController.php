<?php 

namespace Ramatimati\Waliby\App\Http\Controllers;

use Ramatimati\Waliby\App\Http\Requests\EventPostRequest;
use Illuminate\Routing\Controller as BaseController;
use Ramatimati\Waliby\App\Models\MessageTemplate;
use Ramatimati\Waliby\App\Traits\sentWATrait;
use Yajra\DataTables\Facades\DataTables;
use Ramatimati\Waliby\App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends BaseController {

    use sentWATrait;
    public function index(Request $request){
        if ($request->ajax()) {
            $data = Event::with('template')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($d){
                    $btn = '<div class="btn-group">';
                    if ($d->event_type == 'manual') {
                        $btn .= '<button type="button" class="btn btn-sm btn-warning" onclick="sentEvent('.$d->id.')">Sent</button>';
                    }
                    $btn .= '<button type="button" class="btn btn-sm btn-secondary" onclick="detailEvent('.$d->id.')">Show</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteEvent('.$d->id.')">Delete</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('waliby::event.index');
    }

    public function store(EventPostRequest $request){
        $req = $request->validated();
        try {
            DB::beginTransaction();

            Event::create([
                'event_name' => $req['eventname'],
                'event_type' => $req['eventType'],
                'message_template_id' => $req['messageTemplate'],
                'receiver_params' => $req['receiverParams'],
                'last_processed' => null,
                'scheduled_every' => $req['scheduledEvery'] ?? null,
                'scheduled_at' => $req['scheduledAt'] ?? null
            ]);

            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'success'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'code' => 500,
                'message' => $th->getMessage().' on line '.$th->getLine()
            ], 500);
        }
    }

    public function show($id){
        $data = Event::with('template')->where('id', $id)->first();

        $rawParams = explode(',', $data->receiver_params);
        $params = [];
        foreach ($rawParams as $key => $value) {
            $params[$key] = explode('=', $value);
        }

        // get receiver data
        $table = config('waliby.phoneBookTable');
        $nameColumn = config('waliby.nameColumn');
        $countParams = count($params);
        $receiver = DB::table($table)->when($countParams == 1, function($sub) use ($params){
            return $sub->where($params[0][0], $params[0][1]);
        })->when($countParams == 2, function($sub) use ($params){
            return $sub->where($params[0][0], $params[0][1])->where($params[1][0], $params[1][1]);
        })->when($countParams == 3, function($sub) use ($params){
            return $sub->where($params[0][0], $params[0][1])->where($params[1][0], $params[1][1])->where($params[2][0], $params[2][1]);
        })
        ->select($nameColumn)
        ->get();

        return response()->json([
            'event_name' => $data->event_name,
            'message' => $data->template->message,
            'parameters' => $data->receiver_params,
            'receiver' => $receiver
        ]);
    }

    public function getReceiver(Request $request){
        $table = config('waliby.phoneBookTable');
        $colCondition1 = config('waliby.columnCondition1');
        $colCondition2 = config('waliby.columnCondition2');
        $colCondition3 = config('waliby.columnCondition3');

        
        if ($colCondition1) {
            $condition1 = DB::table($table)->select($colCondition1)->groupBy($colCondition1)->get();
        }else {
            $condition1 = null;
        }
        if ($colCondition2) {
            $condition2 = DB::table($table)->select($colCondition2)->groupBy($colCondition2)->get();
        }else{
            $condition2 = null;
        }
        if ($colCondition3) {
            $condition3 = DB::table($table)->select($colCondition3)->groupBy($colCondition3)->get();
        }else{
            $condition3 = null;
        }

        $params = [];

        if (isset($condition1)) {
            foreach ($condition1 as $kc1 => $vc1) {
                $temp = $colCondition1.' = '.$vc1->$colCondition1;
                array_push($params, $temp);
                if (isset($condition2)) {
                    foreach ($condition2 as $kc2 => $vc2) {
                        if ($kc1 == count($condition1)-1) {
                            $temp = $colCondition2.' = '.$vc2->$colCondition2;
                            array_push($params, $temp);
                        }
                        $temp = $colCondition1.' = '.$vc1->$colCondition1.', '.$colCondition2.' = '.$vc2->$colCondition2;
                        array_push($params, $temp);
                        if (isset($condition3)) {
                            foreach ($condition3 as $kc3 => $vc3) {
                                if ($kc2 == count($condition2)-1) {
                                    $temp = $colCondition1.' = '.$vc1->$colCondition1.', '.$colCondition3.' = '.$vc3->$colCondition3;
                                    array_push($params, $temp);
                                }
                                if ($kc1 == count($condition1)-1 && $kc2 == count($condition2)-1) {
                                    $temp = $colCondition3.' = '.$vc3->$colCondition3;
                                    array_push($params, $temp);
                                }
                                $temp = $colCondition1.' = '.$vc1->$colCondition1.', '.$colCondition2.' = '.$vc2->$colCondition2.', '.$colCondition3.' = '.$vc3->$colCondition3;
                                array_push($params, $temp);
                            }
                        }
                    }
                }
            }
        }

        sort($params);

        $result = [];
        foreach ($params as $key => $value) {
            $result[$key]['id'] = str_replace(' ', '', $value);
            $result[$key]['text'] = $value;
        }
        $result = array_values($result);
        return response()->json($result);
    }

    public function getMessageTemplate(Request $request){
        $data = MessageTemplate::when($request->get('q'), function($sub) use ($request){
            return $sub->where('message', 'like', '%'.$request->get('q').'%');
        })
        ->get();

        $result = [];
        foreach ($data as $key => $value) {
            $result[$key]['id'] = $value->id;
            $result[$key]['text'] = $value->name.' ('.$value->message.')';
        }
        $result = array_values($result);
        return response()->json($result);
    }

    public function sentManually($id){
        return $this->send($id);
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://api.fonnte.com/send',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => array('data' => '[{"target": "082315535393", "message": "test"},{"target": "081232575808", "message": "test fokus kamera"}]'),
        //     CURLOPT_HTTPHEADER => array(
        //         'Authorization: u1PxBCwJXf9b2-UJ4m1M' //change TOKEN to your actual token
        //     ),
        // ));

        // $response = curl_exec($curl);

        // curl_close($curl);
        // echo $response;
    }
}