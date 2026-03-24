<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::apiResource('register',RegisterController::class)->only(['store']);
Route::apiResource('login',LoginController::class)->only(['store']);

Route::middleware('auth:api')->group(function (){
    Route::post('/logout',[LoginController::class,'logout'])->name('logout');
    Route::patch('/changepassword',[ProfileController::class,'changePassword']);
    Route::put('/updateprofile',[ProfileController::class,'updateProfile']);
    Route::get('/profile',[ProfileController::class,'me']);
    Route::delete('/deletecomte',[ProfileController::class,'deleteCompte']);



    // acouuuuuuuuunt
    Route::get('/accounts', [AccountController::class, 'index']);
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts/{id}', [AccountController::class, 'show']);

    Route::post('/accounts/{id}/co-owners', [AccountController::class, 'addCoOwner']);
    Route::delete('/accounts/{id}/co-owners', [AccountController::class, 'removeCoOwner']);

    Route::post('/accounts/{id}/guardian', [AccountController::class, 'assignGuardian']);

    Route::patch('/accounts/{id}/convert', [AccountController::class, 'convertToCourant']);

    Route::delete('/accounts/{id}', [AccountController::class, 'requestClosure']);
});
