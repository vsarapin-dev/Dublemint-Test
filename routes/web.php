<?php

use App\Http\Controllers\FeelingLucky\FeelingLuckyController;
use App\Http\Controllers\History\ShowHistoryController;
use App\Http\Controllers\Logout\LogoutController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\SignIn\SignInController;
use App\Http\Controllers\SignUp\SignUpController;
use App\Http\Controllers\Token\GetTokenController;
use App\Http\Controllers\Token\TokenDeactivateController;
use App\Http\Controllers\Token\TokenRegenerateController;
use App\Http\Controllers\User\UserCreateController;
use App\Http\Controllers\User\UserDeleteController;
use App\Http\Controllers\User\UserEditController;
use App\Http\Controllers\User\UserGetController;
use App\Http\Controllers\User\UserLoginAsController;
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


Route::middleware('guest')->get('/', function () {
    return Inertia::render('Welcome');
})->name('index');


Route::prefix('auth')->post('/sign-up', SignUpController::class)->name('sign.up');

Route::middleware('phone-verified')->group(function() {
    Route::get('/access-code/{token}', SignInController::class)->name('sign.in');
});

Route::middleware(['auth', 'active'])->group(function() {
    Route::get('/home', function () { return Inertia::render('Home'); })->name('home');
    Route::get('/logout', LogoutController::class)->name('logout');
    Route::post('/feeling-lucky', FeelingLuckyController::class)->name('play');
    Route::post('/show-history', ShowHistoryController::class)->name('history');

    Route::middleware('admin')->prefix('admin')->group(function () {

        Route::get('/users', function () { return Inertia::render('UserManagement'); })->name('user.management');
        Route::post('/get-users', UserGetController::class)->name('users.get');
        Route::post('/create-user', UserCreateController::class)->name('users.create');
        Route::post('/delete-user', UserDeleteController::class)->name('users.delete');
        Route::post('/edit-user', UserEditController::class)->name('users.edit');
        Route::post('/login-as', UserLoginAsController::class)->name('users.login');


        Route::post('/get-roles', RoleController::class)->name('roles.get');

    });

    Route::prefix('token')->group(function () {

        Route::post('/get-token', GetTokenController::class)->name('token.get');
        Route::post('/regenerate', TokenRegenerateController::class)->name('token.regenerate');
        Route::post('/deactivate', TokenDeactivateController::class)->name('token.deactivate');

    });
});

