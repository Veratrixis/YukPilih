<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return Inertia::render('LoginPage');
})->name('login');

Route::get('/reset_password', function () {
    return Inertia::render('ResetPassPage');
})->name('resetpass');

Route::get('/polls', function () {
    return Inertia::render('PollsPage');
})->name('polls');

Route::get('/create_poll', function () {
    return Inertia::render('CreatePollPage');
})->name('login');

Route::get('/profile', function () {
    return Inertia::render('ProfilePage');
})->name('me');

// require __DIR__.'/auth.php';