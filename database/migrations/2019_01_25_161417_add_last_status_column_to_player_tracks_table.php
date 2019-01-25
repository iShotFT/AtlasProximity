<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastStatusColumnToPlayerTracksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_tracks', function (Blueprint $table) {
            $table->boolean('last_status')->default(1)->after('last_direction');
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
            $table->dropColumn('last_status');
        });
    }
}
