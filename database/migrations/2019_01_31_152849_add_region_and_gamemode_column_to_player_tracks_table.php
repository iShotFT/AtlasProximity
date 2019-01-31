<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionAndGamemodeColumnToPlayerTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_tracks', function (Blueprint $table) {
            $table->string('gamemode', 3)->default('pvp')->after('player');
            $table->string('region', 2)->default('eu')->after('player');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player_tracks', function (Blueprint $table) {
            //
            $table->dropColumn([
                'gamemode',
                'region',
            ]);
        });
    }
}
