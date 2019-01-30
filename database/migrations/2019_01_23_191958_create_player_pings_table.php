<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayerPingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_pings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip', 15);
            $table->string('player');
            $table->string('port', 5);
            $table->string('region', 2)->default('eu');
            $table->string('gamemode', 3)->default('pvp');
            $table->string('coordinates', 3);
            $table->timestamps();

            $table->index('player');
            $table->index('region');
            $table->index('gamemode');
            $table->index('coordinates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_pings');
    }
}
