<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->string('f_name')->nullable()->comment('نام');
            $table->string('l_name')->nullable()->comment('نام خانوادگی');
            $table->string('SSN')->unique()->comment('کد ملی');
            $table->string('password')->comment('کلمه عبور');
            $table->string('staff_code')->nullable();
            $table->integer('salary')->default(0)->comment('حقوق ماهانه');
            $table->string('address')->nullable()->comment('آدرس');
            $table->string('phone')->nullable()->comment('تلفن ثابت');
            $table->string('mobile')->nullable()->comment('تلفن همراه');
            $table->string('email')->nullable()->comment('ایمیل');
            $table->longText('description')->nullable()->comment('توضیحات');
            $table->timestamp('email_verified_at')->nullable();
            $table->bigInteger('company_id')->nullable()->unsigned()->comment('کد شرکت کاربر');
            $table->tinyInteger('status_id')->unsigned()->comment('وضعیت');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies');
            $table->foreign('status_id')
                ->references('id')
                ->on('user_statuses');
        });
        DB::statement("ALTER TABLE users ADD user_pic  MEDIUMBLOB");
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