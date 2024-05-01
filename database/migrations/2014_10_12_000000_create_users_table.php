<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('verify_email')->default(false);
            $table->integer('verify_code');
            $table->string('password');
            $table->string('phone');
            $table->string('job');
            $table->string('gender');
            $table->integer('age');
            $table->string('profile_image')->nullable();
            $table->string('front_id')->nullable();
            $table->string('back_id')->nullable();
            $table->boolean('verify_id')->default(false);
            $table->boolean('notification')->default(false);
            $table->string('token',1000)->nullable();
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
};
