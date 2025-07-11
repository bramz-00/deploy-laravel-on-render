<?php

use App\Http\Controllers\GeminiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function () {
    return '33';
});
Route::post('/gemini/generate', [GeminiController::class, 'generateText']);
