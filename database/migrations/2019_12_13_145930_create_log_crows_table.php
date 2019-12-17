<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogCrowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_crows', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crowdfunding_id');
            $table->integer('crowdfunding_code');
            $table->decimal('amount',15, 4);
            $table->decimal('sub',15, 4);
            $table->decimal('send',15, 4);
            $table->integer('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_crows');
    }
}
