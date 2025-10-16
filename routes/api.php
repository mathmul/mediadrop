<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json(['ok' => true]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/media', fn() => response()->json(['message' => 'ok']));
});
