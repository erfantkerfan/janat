<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('cost')->nullable()->comment('مبلغ تراکنش');
            $table->timestamp('deadline_at')->nullable()->comment('مهلت پرداخت');
            $table->longText('managerComment')->nullable()->comment('توضیح مسئول درباره تراکنش');
            $table->bigInteger('status_id')->unsigned()->comment('وضعیت تراکنش');
            $table->bigInteger('allocated_loan_id')->unsigned()->comment('وام تخصیص داده شده');
            $table->bigInteger('parent_transaction_id')->nullable()->unsigned()->comment('تراکنش کلی که شامل ریز تراکنش ها هست');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('status_id')
                ->references('id')
                ->on('transaction_status')
                ->onDelete('cascade')
                ->onupdate('cascade');
            $table->foreign('allocated_loan_id')
                ->references('id')
                ->on('allocated_loans')
                ->onDelete('cascade')
                ->onupdate('cascade');
            $table->foreign('parent_transaction_id')
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
        Schema::dropIfExists('transactions');
    }
}
