<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers;

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

Route::post('/login', [Controllers\Api\ApiController::class,'authenticate']);
Route::post('/login/v1', [Controllers\Api\ApiController::class,'login']);
Route::post('/register', [Controllers\Api\ApiController::class,'register']);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('get-user', [Controllers\Api\ApiController::class,'get_user']);
    Route::get('cek', function() {
        dd("hallo");
    });
});
