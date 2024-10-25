<?php

namespace Ramatimati\Waliby\App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Ramatimati\Waliby\App\Models\MessageTemplate;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TemplateController extends BaseController {
    public function index(Request $request){
        $table = config('waliby.phoneBookTable');
        $column = DB::connection()->getSchemaBuilder()->getColumnListing($table);

        if ($request->ajax()) {
            $data = MessageTemplate::orderBy('created_at', 'DESC')->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('uuid', function($d){
                    return '<span class="badge text-bg-secondary">'.$d->id.'</span>';
                })
                ->addColumn('action', function(){
                    return '-';
                })
                ->rawColumns(['uuid', 'action'])
                ->make(true);
        }
        return view('waliby::message_template.index', compact('column'));
    }

    public function store(Request $request){
        try {
            MessageTemplate::create([
                'id' => Str::uuid(),
                'message' => $request->template,
                'created_by' => 1
            ]);

            return response()->json([
                'code' => 200,
                'message' => 'success'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => $th->getMessage().' on line '.$th->getLine()
            ], 500);
        }

        // return response()->json($request->all());
    }
}