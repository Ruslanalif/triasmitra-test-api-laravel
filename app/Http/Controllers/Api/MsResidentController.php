<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MsResidentRequest;
use App\Models\MsResident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image; // Import Intervention Image

class MsResidentController extends Controller
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
    public function getFiltered(Request $request)
    {
        $ress = DB::select('CALL sp_msresident_get_filter(?, ?, ?, ?)', [
            $request->prmStartData, 
            $request->prmLengthData, 
            $request->prmFIlter || '', 
            $request->prmUserID
        ]);
        return response()->json($ress);
    }

    public function store(MsResidentRequest $request)
    {
        // $data = $request;
        $data = $request->validated();

        if ($request->hasFile('FileURL')) {
            $file = $request->file('FileURL');
            $image = Image::make($file)->encode('jpg', 75); // Kompres gambar
            $path = $data['IDCardNumber'] . '-' . uniqid() . '.jpg';
            \Storage::disk('public-uploads')->put($path, (string) $image);
            $data['FileURL'] = $path;
        }

        $createdDate = now();
        $modifiedDate = now();

        $ress = DB::select('CALL sp_msresident_submit(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            'ADD',
            null, // ID for insert should be null
            $data['IDCardNumber'],
            $data['Name'],
            $data['BirthPlace'],
            $data['BirthDay'],
            $data['Gender'],
            $data['Province'],
            $data['Regency'],
            $data['District'],
            $data['Village'],
            $data['Address'],
            $data['Religion'],
            $data['MaritalStatus'],
            $data['Employment'],
            $data['Citizenship'],
            $data['FileURL'],
            $data['FgActive'],
            $data['UserID']
        ]);

        return response()->json($ress[0], 201);
    }

    public function show($id)
    {
        $msResident = MsResident::findOrFail($id);
        return response()->json($msResident);
    }

    public function update(MsResidentRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('FileURL')) {
            $file = $request->file('FileURL');
            $image = Image::make($file)->encode('jpg', 75); // Kompres gambar
            $path = $data['IDCardNumber'] . '-' . uniqid() . '.jpg';
            \Storage::disk('public-uploads')->put($path, (string) $image);
            $data['FileURL'] = $path;
        }else{
            $data['FileURL'] = "";
        }

        $msResident = MsResident::findOrFail($id);

        $modifiedDate = now();

        $ress = DB::select('CALL sp_msresident_submit(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            "EDIT",
            $msResident->ID,
            $data['IDCardNumber'],
            $data['Name'],
            $data['BirthPlace'],
            $data['BirthDay'],
            $data['Gender'],
            $data['Province'],
            $data['Regency'],
            $data['District'],
            $data['Village'],
            $data['Address'],
            $data['Religion'],
            $data['MaritalStatus'],
            $data['Employment'],
            $data['Citizenship'],
            $data['FileURL'],
            $data['FgActive'],
            $data['UserID']
        ]);

        return response()->json($ress[0]);
    }

    public function destroy(Request $request, $id)
    {
        $ress = DB::select('CALL sp_msresident_delete(?, ?, ?, ?)', [
            $request->prmMode, // DEL or SOFT
            $id,
            $request->prmStatus, //Status Y / N for Soft delete
            $request->prmUserID, //User Login for Soft Delete
        ]);
        return response()->json($ress[0]);
    }
}