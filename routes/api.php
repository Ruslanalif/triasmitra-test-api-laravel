<?php

use App\Http\Controllers\Api\MsAreaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\MsResidentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::get('test', function () {
    return response()->json(['staus' => '1', 'message' => 'Connection Server Successfully!']);
})->name('notauthorize');
Route::get('notauthorize', function () {
    return response()->json(['xStatus' => '0','xMessage' => 'Your Request can not be procesed, please login first']);
})->name('notauthorize');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);


    Route::post('msresidents/store', [MsResidentController::class, 'store']);
    Route::post('msresidents/get-all', [MsResidentController::class, 'index']);
    Route::post('msresidents/get-filter', [MsResidentController::class, 'getFiltered']);
    Route::post('msresidents/get-data/{id}', [MsResidentController::class, 'show']);
    Route::post('msresidents/store/{id}', [MsResidentController::class, 'update']);
    Route::post('msresidents/delete/{id}', [MsResidentController::class, 'destroy']);
    Route::post('msresidents/list-status', [MsResidentController::class, 'updateListStatus']);

    Route::post('list-area', [MsAreaController::class, 'getListArea']);

});
