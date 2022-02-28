<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRencontresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rencontres', function (Blueprint $table) {
            $table->id();
            $table->string('team_home_name');
            $table->string('team_away_name');
            $table->string('team_home_logo');
            $table->string('team_away_logo');
            $table->integer('team_home_score');
            $table->integer('team_away_score');
            $table->dateTime('date_match');
            $table->foreignId('user_id')->constrained();
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
        Schema::dropIfExists('rencontres');
    }
}
