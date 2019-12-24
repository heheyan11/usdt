<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_tis', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index('user_id');
            $table->decimal('amount',15,4);
            $table->decimal('rate',15,4);
            $table->tinyInteger('status')->default(\App\Models\OrderTi::STATUS_WAIT);
            $table->tinyInteger('verify')->default(\App\Models\OrderTi::VER_WAIT);
            $table->string('address');
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
        Schema::dropIfExists('order_tis');
    }
}
