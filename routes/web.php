<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomePageController;
use App\Http\Controllers\VocabularyController;

Route::get('/', [HomePageController::class, 'show']);
Route::get('/vocabs', [VocabularyController::class, 'show']);
Route::get('/test', [HomePageController::class, 'generate_question']);
Route::post('/test/vocab', [HomePageController::class, 'check_answer']);
Route::post('/', [HomePageController::class, 'generate_meanings']);
Route::post('/vocabs/add', [HomePageController::class, 'store_vocab']);
Route::post('/vocabs/delete', [HomePageController::class, 'delete_vocab']);
