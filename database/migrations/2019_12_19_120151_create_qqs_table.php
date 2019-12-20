<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qqs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nickname');
            $table->char('openid',35);
            $table->string('headimgurl');
            $table->char('gender',5);
            $table->string('province');
            $table->string('city');
            $table->char('year',6);
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
        Schema::dropIfExists('qqs');
    }
}
