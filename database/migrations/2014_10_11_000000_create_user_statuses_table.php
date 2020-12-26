<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_statuses', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name')->nullable()->comment('نام وضعیت');
            $table->string('display_name')->nullable()->comment('نام قابل نمایش این وضعیت');
            $table->longText('description')->nullable()->comment('توضیح درباره وضعیت');
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
        Schema::dropIfExists('user_statuses');
    }
}
