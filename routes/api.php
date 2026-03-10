<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('register',RegisterController::class)->only(['store']);
Route::apiResource('login',LoginController::class)->only(['store']);

Route::middleware('auth:sanctum')->group(function (){
    Route::post('/logout',[LoginController::class,'logout'])->name('logout');
});