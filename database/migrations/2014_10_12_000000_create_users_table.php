<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->char('phone', 11)->unique();
            $table->string('name')->nullable();
            $table->string('headimgurl');
            $table->tinyInteger('sex')->default(0);
            $table->string('password')->nullable()->unique();
            $table->string('paypass')->nullable();
            $table->tinyInteger('is_verify')->default(\App\Models\User::CARD_NO);
            $table->string('wechat_id')->nullable();
            $table->string('qq_id')->nullable();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->index('parent_id');
            $table->boolean('is_directory')->default(0);
            $table->unsignedInteger('level')->default(0);
            $table->string('path')->default('-');
            $table->tinyInteger('check_level')->default(0);
            $table->tinyInteger('share_level')->default(0);
            $table->integer('share_code');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
