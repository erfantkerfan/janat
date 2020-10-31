<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('f_name');
            $table->string('l_name');
            $table->string('fa_name')->nullable();
            $table->string('SSN')->unique();
            $table->string('staff_code')->nullable();
            $table->string('company_id')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->integer('status')->default(1);
            $table->string('joined_at')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
