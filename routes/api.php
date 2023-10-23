<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FarmaciaController as FarmaciaV1;

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


Route::prefix('/v1')->group(function(){

    Route::apiResource('/farmacias',FarmaciaV1::class);

    Route::prefix('/farmacias')->group(function(){
        Route::get('/nearest/{latitud}/{longitud}',[FarmaciaV1::class,'getNearest'])->name('getNearest');
        Route::get('/distances/{latitud}/{longitud}',[FarmaciaV1::class,'getDistances'])->name('getDistances');
    });
});

