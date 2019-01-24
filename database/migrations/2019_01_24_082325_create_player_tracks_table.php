<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_tracks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('guild_id');
            $table->integer('channel_id');
            $table->string('player');
            $table->string('last_coordinate')->nullable();
            $table->timestamp('until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_tracks');
    }
}
