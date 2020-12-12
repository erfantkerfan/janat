<?php

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

use App\Http\Controllers\FundController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\UserStatusController;

Route::get('/', [HomeController::class, 'welcome'])->name('web.welcome');

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('web.panel.dashboard');
    Route::group(['prefix' => 'api'], function () {
        Route::resource('users', '\\'.UserController::class);
        Route::group(['prefix' => 'users'], function () {
            Route::get('{user}/get_user_pic', [UserController::class, 'getUserPic'])->name('web.panel.getUserPic');
            Route::put('{user}/set_user_pic', [UserController::class, 'setUserPic'])->name('web.panel.getUserPic');
            Route::put('{user}/reset_pass', [UserController::class, 'resetPass'])->name('web.panel.getUserPic');
        });
        Route::resource('user_statuses', '\\'. UserStatusController::class);
        Route::resource('companies', '\\'. CompanyController::class);
        Route::resource('funds', '\\'. FundController::class);
        Route::resource('accounts', '\\'. AccountController::class);
    });
});


//Route::get('/home', 'HomeController@index')->name('home');
