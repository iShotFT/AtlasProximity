<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip', 15);
            $table->string('port', 5);
            $table->string('region', 2)->default('eu');
            $table->string('gamemode', 3)->default('pvp');
            $table->string('coordinates', 3);
            $table->boolean('online')->default(1);
            $table->integer('players')->default(0);
            $table->text('info')->nullable();
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
        Schema::dropIfExists('pings');
    }
}
