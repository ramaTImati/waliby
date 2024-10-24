<?php 

namespace Ramatimati\Waliby\App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Ramatimati\Waliby\App\Models\History;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HistoryController extends BaseController {
    public function index(Request $request){
        if ($request->ajax()) {
            $data = History::orderBy('created_at', 'DESC')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return view('waliby::history.index');
    }

    public function statsUpdate(Request $request){
        $content = json_decode(file_get_contents('php://input'), true);

        $messageId = $content['id'];
        $status = $content['status'];
        $phone = $content['phone'];
        $note = $content['note'];
        $sender = $content['sender'];
        $deviceId = $content['deviceId'];

        try {
            History::where('message_id', $messageId)->where('phone_number', $phone)->update([
                'status' => $status
            ]);
        } catch (\Throwable $th) {
            
        }
    }
}