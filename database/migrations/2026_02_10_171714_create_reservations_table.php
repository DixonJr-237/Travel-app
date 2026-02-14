<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id('reservation_id');
            $table->date('date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->foreignId('ticket_id')->constrained('tickets', 'ticket_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
