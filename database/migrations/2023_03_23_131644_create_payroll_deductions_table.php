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
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->boolean('paid_for_loan')->default(false)->comment('پرداخت برای قسط');
            $table->boolean('paid_for_monthly_payment')->default(false)->comment('پرداخت برای شهریه ماهانه');
            $table->timestamp('from')->nullable()->comment('از تاریخ');
            $table->timestamp('to')->nullable()->comment('تا تاریخ');
            $table->timestamp('paid_at')->nullable()->comment('تاریخ پرداخت');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_deductions');
    }
};
