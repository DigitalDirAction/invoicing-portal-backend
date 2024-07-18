<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}/{hash}', [UserController::class, 'verify'])
    ->middleware('signed') //note that I don't use the auth or auth:sanctum middlewares
    ->name('verification.verify');