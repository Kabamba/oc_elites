<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Players extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('postnom');
            $table->string('date_naiss');
            $table->string('lieu_naiss');
            $table->date('date_deb_car');
            $table->date('date_deb_equipe');
            $table->string('nationality');
            $table->float('poids');
            $table->float('taille');
            $table->string('dorsale_number');
            $table->longText('descriptions');
            $table->foreignId('position_id')->constrained();
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
        //
    }
}
