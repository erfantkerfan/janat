<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name')->comment('نام وام');
            $table->integer('loan_amount')->default(0)->comment('مبلغ وام');
            $table->integer('interest_rate')->default(0)->comment('نرخ بهره به درصد');
            $table->integer('interest_amount')->default(0)->comment('مقدار بهره');
            $table->integer('installment_rate')->default(0)->comment('مبلغ هر قسط');
            $table->integer('number_of_installments')->default(0)->comment('تعداد اقساط');
            $table->bigInteger('fund_id')->unsigned();
            $table->tinyInteger('loan_type_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fund_id')
                ->references('id')
                ->on('funds')
                ->onDelete('cascade')
                ->onupdate('cascade');
            $table->foreign('loan_type_id')
                ->references('id')
                ->on('loan_types')
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
        Schema::dropIfExists('loans');
    }
}
