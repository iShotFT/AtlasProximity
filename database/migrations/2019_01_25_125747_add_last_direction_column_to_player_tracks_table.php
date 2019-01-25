<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastDirectionColumnToPlayerTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_tracks', function (Blueprint $table) {
            $table->string('last_direction')->nullable()->after('last_coordinate');
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
            $table->dropColumn('last_direction');
        });
    }
}
