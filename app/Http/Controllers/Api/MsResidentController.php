<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\MsResidentRequest;
use App\Models\MsResident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image; // Import Intervention Image
// use Intervention\Image\ImageManagerStatic as Image;

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
        $ress = DB::select($request->prmStatus == 'ACT' ? 'CALL sp_msresident_active_get_filter(?, ?, ?, ?)' : 'CALL sp_msresident_nonactive_get_filter(?, ?, ?, ?)' , [
            $request->prmStartData, 
            $request->prmLengthData, 
            $request->prmFIlter || '', 
            $request->prmUserID
        ]);
        if(!$ress){
            return response()->json(['xStatus' => '0', 'xMessage' => 'Not Found Data']);
        }
        return response()->json(['xStatus' => '1', 'xMessage' => '', 'data' => $ress]);
    }

    public function store(MsResidentRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('FileURL')) {
            // $file = $request->file('FileURL');
            // $image = Image::make($file)->encode('jpg', 75); // Kompres gambar
            // $path = $data['IDCardNumber'] . '-' . uniqid() . '.jpg';
            // \Storage::disk('public-uploads')->put($path, (string) $image);
            // $data['FileURL'] = $path;

            // Validasi file yang diunggah
            $request->validate([
                'FileURL' => 'required|image|mimes:jpeg,png,jpg,gif', // Maks 2MB untuk contoh
            ]);
            
            // Ambil file gambar dari request
            $file = $request->file('FileURL');
            
            // Buat instance dari gambar menggunakan intervention/image
            $image = Image::make($file);
            
            $extension = $file->extension();
            // dd($extension);
            try{
                // Kompres gambar
                $image->encode("$extension", 75); // Kompres ke 75% kualitas

                // Simpan gambar sementara untuk cek ukuran
                $tempPath = tempnam(sys_get_temp_dir(), 'image_') . ".$extension";
                $image->orientate()->save($tempPath);

                // Kurangi kualitas hingga ukuran <= 200KB
                while (filesize($tempPath) > 200 * 1024) {
                    $quality = intval($image->quality() * 0.9);
                    $image->encode("$extension", $quality);
                    $image->orientate()->save($tempPath);
                }
            } catch (\Exception $e) {
                $image->encode("$extension", 20); // Kompres ke 75% kualitas

                // Simpan gambar sementara untuk cek ukuran
                try{
                    $image->encode("$extension", 15); // Kompres ke 15% kualitas
                    // Simpan gambar sementara untuk cek ukuran
                    $tempPath = tempnam(sys_get_temp_dir(), 'image_') . ".$extension";
                    $image->orientate()->save($tempPath);
                } catch (\Exception $e) {

                    return response()->json(['xStatus' => '0', 'xMessage' => "error at downgrade quality image: '$e'"]);
                }

            }

            // Simpan gambar ke storage
            $path = $data['IDCardNumber'] . '-' . uniqid() . ".$extension";
            // $path = Storage::put('images/' . uniqid() . '.jpg', $image->stream());
            
            \Storage::disk('public-uploads')->put($path, (string) $image);
            $data['FileURL'] = env('APP_URL') . '/storage/uploads/' . $path;

            // Hapus file sementara
            unlink($tempPath);
        }else{
            $data['FileURL'] = "";
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
        // $msResident = MsResident::findOrFail($id);
        // $msResident = MsResident::where('ID', $id)->first();
        $msResident = DB::select('SELECT * FROM vw_msresident where ID = ?', [$id]);
        if (!$msResident) {
            return response()->json(['xStatus' => '0', 'xMessage' => 'Not Found Data']);
        }
        return response()->json(['xStatus' => '1', 'xMessage' => '','data' => $msResident]);

    }

    public function update_bu(MsResidentRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('FileURL')) {
            // Validasi file yang diunggah
            $request->validate([
                'FileURL' => 'required|image|mimes:jpeg,png,jpg,gif', // Maks 2MB untuk contoh
            ]);

            // Ambil file gambar dari request
            $file = $request->file('FileURL');
            
            // Buat instance dari gambar menggunakan intervention/image
            $image = Image::make($file);

            $extension = $file->extension();
            try {
            // Kompres gambar
            $image->encode("$extension", 75); // Kompres ke 75% kualitas

            // Simpan gambar sementara untuk cek ukuran
            $tempPath = tempnam(sys_get_temp_dir(), 'image_') . ".$extension";
            $image->orientate()->save($tempPath);

            // Kurangi kualitas hingga ukuran <= 200KB
                // Potensial kode yang menyebabkan exception
                while (filesize($tempPath) > 200 * 1024) {
                    $quality = intval($image->quality() * 0.9);
                    $image->encode("$extension", $quality);
                    $image->orientate()->save($tempPath);
                }
            } catch (\Exception $e) {
                return response()->json(['xStatus' => '0', 'xMessage' => "error at downgrade quality image: '$e'"]);
            }

            // Simpan gambar ke storage
            $path = $data['IDCardNumber'] . '-' . uniqid() . ".$extension";
            // $path = Storage::put('images/' . uniqid() . '.jpg', $image->stream());
            
            \Storage::disk('public-uploads')->put($path, (string) $image);
            // $data['FileURL'] = $path;
            $data['FileURL'] = env('APP_URL') . '/storage/uploads/' . $path;

            // Hapus file sementara
            unlink($tempPath);
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
    public function update(MsResidentRequest $request, $id)
    {
        $data = $request->validated();

        if ($request->hasFile('FileURL')) {
            // Validasi file yang diunggah
            $request->validate([
                'FileURL' => 'required|image|mimes:jpeg,png,jpg,gif', // Maks 2MB untuk contoh
            ]);

            // Ambil file gambar dari request
            $file = $request->file('FileURL');
            
            // Buat instance dari gambar menggunakan intervention/image
            $image = Image::make($file);

            $extension = $file->extension();
            try {
            // Kompres gambar
            // $image->encode("$extension", 75); // Kompres ke 75% kualitas

            // Simpan gambar sementara untuk cek ukuran
            $tempPath = tempnam(sys_get_temp_dir(), 'image_') . ".$extension";
            $image->orientate()->save($tempPath);

            // Kurangi kualitas hingga ukuran <= 200KB
                // Potensial kode yang menyebabkan exception
                // while (filesize($tempPath) > 200 * 1024) {
                //     $quality = intval($image->quality() * 0.9);
                //     $image->encode("$extension", $quality);
                //     $image->orientate()->save($tempPath);
                // }
            } catch (\Exception $e) {
                return response()->json(['xStatus' => '0', 'xMessage' => "error at downgrade quality image: '$e'"]);
            }

            // Simpan gambar ke storage
            $path = $data['IDCardNumber'] . '-' . uniqid() . ".$extension";
            // $path = Storage::put('images/' . uniqid() . '.jpg', $image->stream());
            
            \Storage::disk('public-uploads')->put($path, (string) $image);
            // $data['FileURL'] = $path;
            $data['FileURL'] = env('APP_URL') . '/storage/uploads/' . $path;

            // Hapus file sementara
            unlink($tempPath);
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

    public function updateListStatus(Request $request)
    {
        $ress = DB::select('call sp_msresident_multy_update(?, ?, ?)', [
            $request->prmIDs,
            $request->prmStatus, //Status Y / N for Soft delete
            $request->prmUserID, //User Login for Soft Delete
        ]);
        return response()->json($ress[0]);
    }
}