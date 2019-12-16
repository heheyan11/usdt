<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleParisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_parises', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id');
            $table->integer('user_id');
            $table->unique(['article_id','user_id']);
            $table->unsignedTinyInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_parises');
    }
}
