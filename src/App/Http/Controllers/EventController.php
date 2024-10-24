<?php 

namespace Ramatimati\Waliby\App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Ramatimati\Waliby\App\Models\MessageTemplate;
use Yajra\DataTables\Facades\DataTables;
use Ramatimati\Waliby\App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends BaseController {

    public function index(Request $request){
        if ($request->ajax()) {
            $data = Event::all();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($d){
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" class="btn btn-secondary" onclick="detailEvent('.$d->id.')">Show</button>';
                    $btn .= '<button type="button" class="btn btn-primary" onclick="sent('.$d->id.')">Sent</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('waliby::event.index');
    }

    public function store(Request $request){
        try {
            DB::beginTransaction();

            Event::create([
                'event_name' => $request->eventname,
                'message_template_id' => $request->messageTemplate,
                'to' => json_encode($request->receiver),
                'event_status' => 'active'
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

    public function getReceiver(Request $request){
        $table = config('waliby.phoneBookTable');
        $phoneColumn = config('waliby.phoneNumberColumn');
        $nameColumn =config('waliby.nameColumn');

        $data = DB::table($table)
            ->when($request->get('q'), function($sub) use ($request, $nameColumn){
                return $sub->where($nameColumn, 'like', '%'.$request->get('q').'%');
            })
            ->get();

        $result = [];
        foreach ($data as $key => $value) {
            if (isset($value->$phoneColumn)) {
                $result[$key]['id'] = $value->$phoneColumn;
                $result[$key]['text'] = $value->$nameColumn;
            }
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
            $result[$key]['text'] = $value->message;
        }
        $result = array_values($result);
        return response()->json($result);
    }
}