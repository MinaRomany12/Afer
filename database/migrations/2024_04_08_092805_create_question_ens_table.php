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
        Schema::create('question_ens', function (Blueprint $table) {
            $table->id();
            $table->string('Option_1');
            $table->string('Option_2');
            $table->string('Option_3');
            $table->string('Option_4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_ens');
    }
};
