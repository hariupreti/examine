<?php

use App\Http\Controllers\UserController;

Route::get('/', [UserController::class, 'showForm'])->name('username.form');
Route::post('/username', [UserController::class, 'processForm'])->name('username.process');
Route::post('/submitanswers', [UserController::class, 'submitAnswer'])->name('answer.process');