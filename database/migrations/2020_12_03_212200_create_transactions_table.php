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
            $table->longText('manager_comment')->nullable()->comment('توضیح مسئول درباره تراکنش');
            $table->longText('user_comment')->nullable()->comment('توضیح کاربر درباره تراکنش');

            $table->bigInteger('transaction_status_id')->nullable()->unsigned()->comment('وضعیت تراکنش');

//            $table->bigInteger('user_id')->nullable()->unsigned()->comment('کاربر');
//            $table->bigInteger('fund_id')->nullable()->unsigned()->comment('صندوق');
//            $table->bigInteger('company_id')->nullable()->unsigned()->comment('شرکت');
//            $table->bigInteger('allocated_loan_id')->nullable()->unsigned()->comment('وام تخصیص داده شده');

            $table->bigInteger('parent_transaction_id')->nullable()->unsigned()->comment('تراکنش کلی که شامل ریز تراکنش ها هست');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('transaction_status_id')
                ->references('id')
                ->on('transaction_statuses')
                ->onDelete('cascade')
                ->onupdate('cascade');

//            $table->foreign('user_id')
//                ->references('id')
//                ->on('users')
//                ->onDelete('cascade')
//                ->onupdate('cascade');
//
//            $table->foreign('fund_id')
//                ->references('id')
//                ->on('funds')
//                ->onDelete('cascade')
//                ->onupdate('cascade');
//
//            $table->foreign('company_id')
//                ->references('id')
//                ->on('companies')
//                ->onDelete('cascade')
//                ->onupdate('cascade');
//
//            $table->foreign('allocated_loan_id')
//                ->references('id')
//                ->on('allocated_loans')
//                ->onDelete('cascade')
//                ->onupdate('cascade');

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
