<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('name')->comment('نام شرکت');
            $table->text('undertaker')->comment('نام مسئول');
            $table->bigInteger('fund_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('fund_id')
                ->references('id')
                ->on('funds')
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
        Schema::dropIfExists('companies');
    }
}
