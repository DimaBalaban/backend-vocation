<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacationLikeController;
use App\Http\Controllers\VacationNewController;



// Users
Route::get('users-with-vacations', [UserController::class, 'getUsersWithVacations']);
Route::post('/register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::delete('users/{id}', [UserController::class, 'destroy']);


// Vacation

Route::post('vacation_new', [VacationNewController::class, 'store']);


// likes

Route::get('vacations_like/{vacationId}', [VacationLikeController::class, 'getLikesForVacation']);
Route::post('vacations_like', [VacationLikeController::class, 'store']);



