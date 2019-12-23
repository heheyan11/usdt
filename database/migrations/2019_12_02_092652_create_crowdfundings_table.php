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
            $table->string('title')->nullbale();
            $table->unsignedTinyInteger('base_rate');
            $table->unsignedTinyInteger('one_rate');
            $table->unsignedTinyInteger('two_rate');
            $table->unsignedTinyInteger('lead_rate');
            $table->unsignedDecimal('out_rate',10,4)->default(0);
            $table->unsignedDecimal('out_amount',10,4)->default(0);
            $table->decimal('manage_rate',5,2);
            $table->unsignedMediumInteger('run');
            $table->unsignedDecimal('target_amount', 15, 4);
            $table->unsignedDecimal('total_amount', 15, 4)->default(0);
            $table->unsignedDecimal('income',28,4)->default(0);

            $table->unsignedSmallInteger('user_count')->default(0);
            $table->string('url')->nullable();
            $table->text('content')->nullable();
            $table->integer('start_at')->nullable();
            $table->integer('end_at')->nullable();
            $table->string('allow')->nullable();
            $table->string('status')->default(\App\Models\Crowdfunding::STATUS_FUNDING);
            $table->string('run_status')->default(\App\Models\Crowdfunding::RUN_STOP);
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
        Schema::dropIfExists('crowdfundings');
    }
}
