<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tips', function (Blueprint $table) {
            $table->id('trip_id');
            $table->date('departure_date');
            $table->time('departure_time');
            $table->decimal('initial_price', 10, 2);
            $table->integer('available_seats');
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->foreignId('bus_id')->constrained('buses', 'bus_id');
            $table->foreignId('journey_id')->constrained('journeys', 'journey_id');
            $table->foreignId('departure_location_coord_id')->constrained('coordinates', 'id_coord');
            $table->foreignId('arrival_location_coord_id')->constrained('coordinates', 'id_coord');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tips');
    }
};
