<?php

use App\Http\Controllers\ApiController\ApiAuthController;
use App\Http\Controllers\ApiController\ApiApplicationController;
use App\Http\Controllers\ApiController\TransactionController;
use App\Models\Rating;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//for api application
Route::resource('/application', ApiApplicationController::class)->middleware('auth:sanctum');

//for transaction making
Route::post('/transaction', [TransactionController::class, 'store'])->middleware('auth:sanctum');

//for ratings
Route::post('/feedback', [TransactionController::class, 'storeFeedback'])->middleware('auth:sanctum');

//for vendor list
Route::get('/vendor-list', function () {
    $vendor = Vendor::get();

    return response()->json([
        'message' => 'vendor list',
        'vendor' => $vendor,
    ]);
})->middleware('auth:sanctum');

Route::post('/register', [ApiAuthController::class, 'register']);
Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/logout', [ApiAuthController::class, 'logout'])->middleware('auth:sanctum');
