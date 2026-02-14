<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('coordinates', function (Blueprint $table) {
            $table->id('id_coord');
            $table->string('geo_coord')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address')->nullable();
            $table->foreignId('id_city')->constrained('cities', 'id_city');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('coordinates');
    }
};
