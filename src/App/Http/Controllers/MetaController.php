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

        return view('waliby::meta.index', compact('key'));
    }
}
