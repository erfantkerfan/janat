<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttachableCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachable_cases', function (Blueprint $table) {
            $table->bigInteger('picture_id')->unsigned();
            $table->string('attachable_case_type');
            $table->bigInteger('attachable_case_id')->unsigned();
            $table->timestamps();

            $table->foreign('picture_id')
                ->references('id')
                ->on('pictures')
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
        Schema::dropIfExists('attachable_cases');
    }
}
