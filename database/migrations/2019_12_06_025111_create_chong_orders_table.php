<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChongOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chong_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->char('order_no',18);
            $table->integer('user_id')->index('user_id');
            $table->char('symbol',5)->nullable();
            $table->unsignedDecimal('amount',15,4);
            $table->string('hash')->nullable();
            $table->timestamp('created_at', 0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chong_orders');
    }
}
