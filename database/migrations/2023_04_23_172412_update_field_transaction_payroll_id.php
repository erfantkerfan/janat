<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->bigInteger('payroll_deduction_id')
                ->after('paid_as_payroll_deduction')
                ->nullable()
                ->unsigned()
                ->comment('کد پرداخت دوره ای');

            $table->foreign('payroll_deduction_id')
                ->references('id')
                ->on('payroll_deductions')
                ->onDelete('cascade')
                ->onupdate('cascade');
        });

        $this->transactionsCursor(true);
        $this->transactionsCursor(false);
    }

    private function transactionsCursor ($isLoan) {
        $transactionTypeId = ($isLoan ? 5 : 2);
        DB::table('transactions')
            ->select('paid_at')
            ->distinct()
            ->where('paid_as_payroll_deduction', 1)
            ->where('transaction_type_id', $transactionTypeId)
            ->cursor()
            ->each(function ($transactionPainAt) use ($isLoan, $transactionTypeId) {
                $payrollDeductionId = $this->createPayrollDeductions($transactionPainAt->paid_at, $isLoan);
                $this->updateTransactions($transactionPainAt->paid_at, $transactionTypeId, $payrollDeductionId);
            });
    }

    private function createPayrollDeductions ($paid_at, $isLoan) {
        return DB::table('payroll_deductions')
            ->insertGetId(
                array(
                    'paid_for_loan' => ($isLoan ? 1 : 0),
                    'paid_for_monthly_payment' => ($isLoan ? 0 : 1),
                    'from' => $paid_at,
                    'to' => $paid_at,
                    'paid_at' => $paid_at
                )
            );
    }

    private function updateTransactions ($paid_at, $transactionTypeId, $payrollDeductionId) {
        DB::table('transactions')
            ->where('paid_at', $paid_at)
            ->where('paid_as_payroll_deduction', 1)
            ->where('transaction_type_id', $transactionTypeId)
            ->update(['payroll_deduction_id' => $payrollDeductionId]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('payroll_deduction_id');
        });
    }
};
