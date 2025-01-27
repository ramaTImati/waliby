<?php

namespace Ramatimati\Waliby\App\Http\Controllers;

use Ramatimati\Waliby\App\Http\Requests\TemplateUpdateRequest;
use Ramatimati\Waliby\App\Http\Requests\TemplatePostRequest;
use Illuminate\Routing\Controller as BaseController;
use Ramatimati\Waliby\App\Models\MessageTemplate;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
                ->addColumn('action', function($d){
                    $btn = '<div class="btn-group">';
                    $btn .= '<button type="button" class="btn btn-sm btn-warning" onclick="edit(`'.$d->id.'`)">Edit</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-danger" onclick="deleteTemplate(`'.$d->id.'`)">Delete</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['uuid', 'action'])
                ->make(true);
        }
        return view('waliby::message_template.index', compact('column'));
    }

    public function store(TemplatePostRequest $request){
        $req = $request->validated();

        try {
            MessageTemplate::create([
                'id' => Str::uuid(),
                'name' => $req['name'],
                'message' => $req['template']
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
    }

    public function show($id){
        try {
            $data = MessageTemplate::find($id);
    
            return response($data);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => $th->getMessage().' on line '.$th->getLine()
            ], 500);
        }
    }

    public function update($id, TemplateUpdateRequest $request){
        $req = $request->validated();

        try {
            DB::beginTransaction();

            $data = MessageTemplate::where('id', $req['templateId'])->update([
                'message' => $req['text']
            ]);

            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'Data updated successfully'
            ], 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'code' => 500,
                'message' => $th->getMessage().' on line '.$th->getLine()
            ], 500);
        }
    }

    public function destroy($id){
        try {
            $data = MessageTemplate::find($id)->delete();

            return response()->json([
                'code' => 200,
                'message' => 'data deleted'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 500,
                'message' => $th->getMessage()
            ]);
        }
    }
}