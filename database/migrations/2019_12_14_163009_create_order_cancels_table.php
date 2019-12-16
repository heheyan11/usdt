<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCancelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_cancels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('crowdfunding_id');
            $table->decimal('amount',15,4);
            $table->decimal('rate',10,4);
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
        Schema::dropIfExists('order_cancels');
    }
}
