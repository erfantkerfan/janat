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
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LoanTypeController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\UserStatusController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AllocatedLoanController;
use App\Http\Controllers\TransactionStatusController;
use App\Http\Controllers\AllocatedLoanInstallmentController;

Route::get('/', [HomeController::class, 'welcome'])->name('web.welcome');

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('web.panel.dashboard');
    Route::group(['prefix' => 'api'], function () {
        Route::get('dashboard', [HomeController::class, 'dashboardData'])->name('api.panel.dashboardData');
        Route::resource('users', '\\'.UserController::class);
        Route::group(['prefix' => 'users'], function () {
            Route::get('{user}/get_user_pic', [UserController::class, 'getUserPic'])->name('api.panel.getUserPic');
            Route::put('{user}/set_user_pic', [UserController::class, 'setUserPic'])->name('api.panel.getUserPic');
            Route::put('{user}/reset_pass', [UserController::class, 'resetPass'])->name('api.panel.getUserPic');
        });
        Route::resource('user_statuses', '\\'. UserStatusController::class);
        Route::resource('user_types', '\\'. UserTypeController::class);
        Route::resource('companies', '\\'. CompanyController::class);
        Route::resource('funds', '\\'. FundController::class);
        Route::resource('loans', '\\'. LoanController::class);
        Route::resource('loan_types', '\\'. LoanTypeController::class);
        Route::get('allocated_loans/pay_periodic_payroll_deduction', [AllocatedLoanController::class, 'payPeriodicPayrollDeduction']);
        Route::get('account/pay_periodic_payroll_deduction_for_charge_fund', [AccountController::class, 'payPeriodicPayrollDeductionForChargeFund']);
        Route::resource('allocated_loans', '\\'. AllocatedLoanController::class);
        Route::resource('allocated_loan_Installments', '\\'. AllocatedLoanInstallmentController::class);
        Route::get('accounts/{account}/balance', [AccountController::class, 'getBalance']);
        Route::resource('accounts', '\\'. AccountController::class);
        Route::resource('transactions', '\\'. TransactionController::class);
        Route::resource('transaction_statuses', '\\'. TransactionStatusController::class);
        Route::resource('settings', '\\'. SettingController::class);
    });
});


//Route::get('/home', 'HomeController@index')->name('home');
