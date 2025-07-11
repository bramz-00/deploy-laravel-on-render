<?php

use App\Http\Controllers\GeminiController;
use App\Http\Controllers\OpenAIController;
use Illuminate\Support\Facades\Route;
use OpenAI\Laravel\Facades\OpenAI;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

Route::get('/chat', [OpenAIController::class, 'index'])->name('chat');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
