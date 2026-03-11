<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::apiResource('register',RegisterController::class)->only(['store']);
Route::apiResource('login',LoginController::class)->only(['store']);

Route::middleware('auth:api')->group(function (){
    Route::post('/logout',[LoginController::class,'logout'])->name('logout');
    
    Route::get('/profile',[ProfileController::class,'me']);
});
