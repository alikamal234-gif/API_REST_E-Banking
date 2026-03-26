<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;

Route::apiResource('register', RegisterController::class)->only(['store']);
Route::apiResource('login', LoginController::class)->only(['store']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::patch('/changepassword', [ProfileController::class, 'changePassword']);
    Route::put('/updateprofile', [ProfileController::class, 'updateProfile']);
    Route::get('/profile', [ProfileController::class, 'me']);
    Route::delete('/deletecomte', [ProfileController::class, 'deleteCompte']);

    // acouuuuuuuuunt
    Route::get('/accounts', [AccountController::class, 'index']);
    Route::post('/accounts', [AccountController::class, 'store']);
    Route::get('/accounts/{id}', [AccountController::class, 'show']);

    Route::post('/accounts/{id}/co-owners', [AccountController::class, 'addCoOwner']);
    Route::delete('/accounts/{id}/co-owners', [AccountController::class, 'removeCoOwner']);

    Route::post('/accounts/{id}/guardian', [AccountController::class, 'assignGuardian']);

    Route::patch('/accounts/{id}/convert', [AccountController::class, 'convertToCourant']);

    Route::delete('/accounts/{id}', [AccountController::class, 'requestClosure']);

    // transfeeeeeeeeeeeer

    Route::post('/transfer', [TransferController::class, 'transfer']);
    Route::get('/transfer/history', [TransferController::class, 'history']);
    Route::get('/transfer/{id}/show', [TransferController::class, 'show']);

    // transactiiiiiiiiiiiiiiiiiion

    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::get('/transactions/{id}', [TransactionController::class, 'show']);
    Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

    // contaaaaaaaaaaaaaaaaact

    Route::get('/contacts', [ContactController::class, 'index']);
    Route::post('/contacts', [ContactController::class, 'store']);
    Route::get('/contacts/{id}', [ContactController::class, 'show']);
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);
});


Route::prefix('admin')->middleware(['auth:api', 'is_admin'])->group(function () {

        Route::patch('/accounts/{id}/block', [AdminController::class, 'block']);
        Route::patch('/accounts/{id}/unblock', [AdminController::class, 'unblock']);
        Route::patch('/accounts/{id}/close', [AdminController::class, 'close']);
});
