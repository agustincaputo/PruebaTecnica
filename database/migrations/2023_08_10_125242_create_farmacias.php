<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFarmacias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('farmacias', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('nombre');
            $table->string('direccion');
            $table->double('latitud',4,2);
            $table->double('longitud',5,2);
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
        Schema::dropIfExists('farmacias');
    }
}
