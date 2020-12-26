<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocatedLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocated_loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('account_id')->unsigned();
            $table->bigInteger('loan_id')->unsigned();
            $table->integer('loan_amount')->default(0)->comment('مبلغ وام');
            $table->integer('installment_rate')->default(0)->comment('مبلغ هر قسط');
            $table->integer('number_of_installments')->default(0)->comment('تعداد اقساط');
            $table->boolean('payroll_deduction')->default(false)->comment('پرداخت اقساط به صورت کسر از حقوق');
//            $table->integer('number_of_paid_installments')->default(0)->comment('تعداد اقساط باقیمانده');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('loan_id')
                ->references('id')
                ->on('loans')
                ->onDelete('cascade')
                ->onupdate('cascade');
            $table->foreign('account_id')
                ->references('id')
                ->on('accounts')
                ->onDelete('cascade')
                ->onupdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allocated_loans');
    }
}
