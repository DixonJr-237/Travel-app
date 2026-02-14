<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id('bus_id');
            $table->string('registration_number')->unique();
            $table->integer('seats_count');
            $table->string('model')->nullable();
            $table->year('year')->nullable();
            $table->enum('status', ['active', 'maintenance', 'inactive'])->default('active');
            $table->foreignId('agency_id')->constrained('agencies', 'id_agence');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buses');
    }
};
