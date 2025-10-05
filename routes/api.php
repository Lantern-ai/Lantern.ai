<?php

use App\Http\Controllers\AiManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


    Route::post("/ai/ask", [AiManager::class, 'ask']);


