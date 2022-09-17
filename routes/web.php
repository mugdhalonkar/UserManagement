<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


Route::get('list-users', [UserController::class, 'index']);
Route::post('add-update-user', [UserController::class, 'store']);
Route::post('edit-user', [UserController::class, 'edit']);
Route::post('delete-user', [UserController::class, 'destroy']);
