<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_cards', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');

            $table->string('name');
            $table->string('code');
            $table->string('province',100)->nullable();
            $table->string('city',100)->nullable();
            $table->string('county',100)->nullable();
            $table->char('birthday',8)->nullable();
            $table->char('age',3)->nullable();


            $table->string('address')->nullable();
            $table->string('nationality',50)->nullable();
            $table->char('sex',3)->nullable();

            $table->string('issue')->nullable();
            $table->char('start_date',8)->nullable();
            $table->char('end_date',8)->nullable();

            $table->string('face')->nullable();
            $table->string('back')->nullable();

            $table->timestamp('created_at');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_cards');
    }
}
