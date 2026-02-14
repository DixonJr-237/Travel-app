<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('journeys', function (Blueprint $table) {
            $table->id('journey_id');
            $table->string('name');
            $table->foreignId('departure_location_coord_id')->constrained('coordinates', 'id_coord');
            $table->foreignId('arrival_location_coord_id')->constrained('coordinates', 'id_coord');
            $table->decimal('distance', 8, 2); // en km
            $table->integer('estimated_duration'); // en minutes
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('journeys');
    }
};
