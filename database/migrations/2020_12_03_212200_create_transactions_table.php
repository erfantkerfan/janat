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
            $table->boolean('paid_as_payroll_deduction')->default(false)->comment('پرداخت به صورت کسر از حقوق');
            $table->timestamp('deadline_at')->nullable()->comment('مهلت پرداخت');
            $table->timestamp('paid_at')->nullable()->comment('تاریخ پرداخت');
            $table->longText('manager_comment')->nullable()->comment('توضیح مسئول درباره تراکنش');
            $table->longText('user_comment')->nullable()->comment('توضیح کاربر درباره تراکنش');

            $table->tinyInteger('transaction_status_id')->nullable()->unsigned()->comment('وضعیت تراکنش');
            $table->tinyInteger('transaction_type_id')->nullable()->unsigned()->comment('وضعیت تراکنش');

            $table->bigInteger('parent_transaction_id')->nullable()->unsigned()->comment('تراکنش کلی که شامل ریز تراکنش ها هست');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('transaction_status_id')
                ->references('id')
                ->on('transaction_statuses')
                ->onDelete('cascade')
                ->onupdate('cascade');
            $table->foreign('transaction_type_id')
                ->references('id')
                ->on('transaction_types')
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
