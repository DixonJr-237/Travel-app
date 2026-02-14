<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sub_regions', function (Blueprint $table) {
            $table->id('id_sub_region');
            $table->string('name');
            $table->foreignId('id_region')->constrained('regions', 'id_region');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sub_regions');
    }
};
