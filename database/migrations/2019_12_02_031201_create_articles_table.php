<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('article_cate_id')->nullable();
            $table->foreign('article_cate_id')->references('id')->on('article_cates')->onDelete('set null');
            $table->string('title');
            $table->text('thumb')->nullable();
            $table->json('imgs')->nullable();
            $table->string('short_content')->nullable();
            $table->text('content')->nullable();
            $table->integer('clicks')->default(0);
            $table->integer('zan')->default(0);
            $table->integer('share')->default(0);
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
        Schema::dropIfExists('articles');
    }
}
