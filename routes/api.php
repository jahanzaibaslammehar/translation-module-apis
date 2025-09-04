<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\TranslationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/login', [AuthController::class, 'login']);


// api routes protected by auth sanctum
Route::prefix('translation')->middleware('auth:sanctum')->group(function () {
    Route::get('/get/{context}/{locale}', [TranslationController::class, 'get']);
    Route::post('/create', [TranslationController::class, 'create']);
    Route::patch('/update', [TranslationController::class, 'update']);
    Route::get('/search/{keyword}', [TranslationController::class, 'search']);
});
