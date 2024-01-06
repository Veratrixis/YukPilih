<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PollsController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('/poll', PollsController::class);

Route::post('/poll/{poll_id}/vote/{choice_id}', [PollsController::class, 'vote']);
Route::post('/auth/login', [LoginController::class, 'login']);

Route::post('/auth/logout', [UserController::class, 'logout']);
Route::post('/auth/register', [UserController::class, 'register_user']);
Route::get('/auth/me', [UserController::class, 'get_profile']);
Route::post('/auth/reset_password', [UserController::class, 'reset_password']);