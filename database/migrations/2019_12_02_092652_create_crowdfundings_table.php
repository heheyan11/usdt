<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrowdfundingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crowdfundings', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code',5);
            $table->unsignedTinyInteger('base_rate');
            $table->unsignedTinyInteger('one_rate');
            $table->unsignedTinyInteger('two_rate');
            $table->unsignedTinyInteger('lead_rate');
            $table->decimal('target_amount', 10, 4);
            $table->decimal('total_amount', 10, 4);
            $table->unsignedSmallInteger('user_count')->default(0);
            $table->string('url')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crowdfundings');
    }
}
