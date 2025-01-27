<?php

namespace Ramatimati\Waliby\App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use Yajra\DataTables\Facades\DataTables;
use Ramatimati\Waliby\App\Models\Meta;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MetaController extends BaseController
{
    public function index(Request $request){
        $key = [
            'REQUEST_HEADERS','REQUEST_BODY','RESPONSE'
        ];

        $data = Meta::whereIn('name', $key)->get();
        $res = [];
        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return view('waliby::meta.index', compact('res'));
    }

    public function update(Request $request){
        try {
            if (isset($request->header)) {
                Meta::where('name', 'REQUEST_HEADERS')->update([
                    'value' => $request->header
                ]);
            }
            if (isset($request->body)) {
                Meta::where('name', 'REQUEST_BODY')->update([
                    'value' => $request->body
                ]);
            }
            if (isset($request->response)) {
                Meta::where('name', 'RESPONSE')->update([
                    'value' => $request->response
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
        return redirect()->route('waliby.metas.index');
    }
}
