<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegionAndGamemodeColumnToBoatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boats', function (Blueprint $table) {
            $table->string('gamemode', 3)->default('pvp')->after('coordinate');
            $table->string('region', 2)->default('eu')->after('coordinate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boats', function (Blueprint $table) {
            //
            $table->dropColumn([
                'gamemode',
                'region',
            ]);
        });
    }
}
