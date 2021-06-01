<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('fund_id')->unsigned();
            $table->bigInteger('company_id')->unsigned();
            $table->integer('monthly_payment')->default(0)->comment('شهریه صندوق (ماهانه)');
            $table->boolean('payroll_deduction')->default(false)->comment('پرداخت شهریه به صورت کسر از حقوق');
            $table->timestamp('joined_at')->nullable()->comment('زمان عضویت در صندوق');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onupdate('cascade');

            $table->foreign('fund_id')
                ->references('id')
                ->on('funds')
                ->onDelete('cascade')
                ->onupdate('cascade');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
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
        Schema::dropIfExists('accounts');
    }
}
