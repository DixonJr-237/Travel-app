<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id('ticket_id');
            $table->dateTime('purchase_date');
            $table->decimal('price', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'used'])->default('pending');
            $table->string('seat_number');
            $table->string('booking_reference')->unique();
            $table->foreignId('journey_id')->constrained('journeys', 'journey_id');
            $table->foreignId('customer_id')->constrained('customers', 'customer_id');
            $table->foreignId('trip_id')->constrained('tips', 'trip_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
