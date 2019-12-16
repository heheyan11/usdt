<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogIncomesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_incomes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('crowdfunding_id');
            $table->integer('crowdfunding_code');
            $table->unsignedDecimal('amount', 15, 4);
            $table->unsignedDecimal('income', 15, 4);
            $table->string('title');
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
        Schema::dropIfExists('log_incomes');
    }
}
