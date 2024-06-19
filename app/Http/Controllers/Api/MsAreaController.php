<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MsResident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MsAreaController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('auth:api');
    }
    public function index()
    {
        return MsResident::all();
    }
    public function getListArea(Request $request)
    {
        $ress = DB::select('CALL sp_get_area(?, ?, ?)' , [
            $request->prmType, 
            $request->prmKey, 
            $request->prmUserID
        ]);
        if(!$ress){
            return response()->json(['xStatus' => '0', 'xMessage' => 'Not Found Data']);
        }
        return response()->json(['xStatus' => '1', 'xMessage' => '', 'data' => $ress]);
    }

}