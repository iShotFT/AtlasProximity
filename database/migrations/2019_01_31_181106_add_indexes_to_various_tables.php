<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexesToVariousTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('guilds', function (Blueprint $table) {
            $table->index('region');
            $table->index('gamemode');
        });

        Schema::table('boats', function (Blueprint $table) {
            $table->index('region');
            $table->index('gamemode');
        });

        Schema::table('proximity_tracks', function (Blueprint $table) {
            $table->index('region');
            $table->index('gamemode');
        });

        Schema::table('player_tracks', function (Blueprint $table) {
            $table->index('region');
            $table->index('gamemode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guilds', function (Blueprint $table) {
            $table->dropIndex('region');
            $table->dropIndex('gamemode');
        });

        Schema::table('boats', function (Blueprint $table) {
            $table->dropIndex('region');
            $table->dropIndex('gamemode');
        });

        Schema::table('proximity_tracks', function (Blueprint $table) {
            $table->dropIndex('region');
            $table->dropIndex('gamemode');
        });

        Schema::table('player_tracks', function (Blueprint $table) {
            $table->dropIndex('region');
            $table->dropIndex('gamemode');
        });
    }
}
