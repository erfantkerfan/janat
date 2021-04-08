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
//        Route::get('debug', [HomeController::class, 'debug'])->name('api.debug');
        Route::group(['prefix' => 'users'], function () {
            Route::get('{user}/get_user_pic', [UserController::class, 'getUserPic'])->name('api.panel.getUserPic');
            Route::put('{user}/set_user_pic', [UserController::class, 'setUserPic'])->name('api.panel.user.setPic');
            Route::put('{user}/reset_pass', [UserController::class, 'resetPass'])->name('api.panel.user.resetPass');
            Route::get('{user}/get_total_balance', [UserController::class, 'getTotalBalance'])->name('api.panel.getUserTotalBalance');
        });
        Route::resource('users', '\\'.UserController::class);
        Route::resource('user_statuses', '\\'. UserStatusController::class);
        Route::resource('user_types', '\\'. UserTypeController::class);
        Route::resource('companies', '\\'. CompanyController::class);
        Route::group(['prefix' => 'funds'], function () {
            Route::get('{fund}/get_incomes_and_expenses', [FundController::class, 'getIncomesAndExpenses'])->name('api.panel.fund.getIncomesAndExpenses');
            Route::get('{fund}/get_expense_transactions', [FundController::class, 'getExpenseTransactions'])->name('api.panel.getExpensesTransactions');
        });
        Route::resource('funds', '\\'. FundController::class);
        Route::resource('loans', '\\'. LoanController::class);
        Route::resource('loan_types', '\\'. LoanTypeController::class);
        Route::group(['prefix' => 'allocated_loans'], function () {
            Route::get('show_periodic_payroll_deduction', [AllocatedLoanController::class, 'showPeriodicPayrollDeduction'])
                ->name('api.panel.allocated_loans.show_periodic_payroll_deduction');
            Route::get('pay_periodic_payroll_deduction', [AllocatedLoanController::class, 'payPeriodicPayrollDeduction'])
                ->name('api.panel.allocated_loans.pay_periodic_payroll_deduction');
            Route::get('rollback_pay_periodic_payroll_deduction', [AllocatedLoanController::class, 'rollbackPayPeriodicPayrollDeduction'])
                ->name('api.panel.allocated_loans.rollback_pay_periodic_payroll_deduction');
        });
        Route::resource('allocated_loans', '\\'. AllocatedLoanController::class);
        Route::resource('allocated_loan_Installments', '\\'. AllocatedLoanInstallmentController::class);
        Route::group(['prefix' => 'accounts'], function () {
            Route::get('{account}/balance', [AccountController::class, 'getBalance']);
            Route::get('show_periodic_payroll_deduction', [AccountController::class, 'showPeriodicPayrollDeductionForChargeFund'])
                ->name('api.panel.accounts.pay_periodic_payroll_deduction');
            Route::get('pay_periodic_payroll_deduction', [AccountController::class, 'payPeriodicPayrollDeductionForChargeFund'])
                ->name('api.panel.accounts.pay_periodic_payroll_deduction');
            Route::get('rollback_pay_periodic_payroll_deduction', [AccountController::class, 'rollbackPayPeriodicPayrollDeduction'])
                ->name('api.panel.accounts.rollback_pay_periodic_payroll_deduction');
        });
        Route::resource('accounts', '\\'. AccountController::class);
        Route::resource('transactions', '\\'. TransactionController::class);
        Route::resource('transaction_statuses', '\\'. TransactionStatusController::class);
        Route::resource('settings', '\\'. SettingController::class);
    });
});


//Route::get('/home', 'HomeController@index')->name('home');
