<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserCrow extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_crows',function (Blueprint $table){
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('crowdfunding_id');
            $table->unique(['user_id','crowdfunding_id']);
            $table->unsignedDecimal('amount',15,4);
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
        Schema::dropIfExists('user_crows');
    }
}
