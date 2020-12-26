<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocatedLoanInstallmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocated_loan_installments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('allocated_loan_id')->unsigned();
            $table->integer('rate')->default(0)->comment('مبلغ قسط');
//            $table->integer('number_of_total_installments')->default(0)->comment('تعداد اقساط');
//            $table->integer('number_of_paid_installments')->default(0)->comment('تعداد اقساط باقیمانده');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('allocated_loan_id')
                ->references('id')
                ->on('allocated_loans')
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
        Schema::dropIfExists('allocated_loan_installments');
    }
}
