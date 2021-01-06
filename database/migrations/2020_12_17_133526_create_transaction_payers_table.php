<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionPayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_payers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cost')->nullable()->comment('مبلغ پرداخت');
            $table->bigInteger('transaction_id')->unsigned();
            $table->bigInteger('transaction_payers_id')->unsigned();
            $table->string('transaction_payers_type');
            $table->timestamps();

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
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
        Schema::dropIfExists('transaction_payers');
    }
}
