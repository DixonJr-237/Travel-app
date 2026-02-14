<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agency_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_company')->constrained('companies', 'id_company');
            $table->foreignId('id_agence')->constrained('agencies', 'id_agence');
            $table->foreignId('id_region')->constrained('regions', 'id_region');
            $table->foreignId('id_coord')->constrained('coordinates', 'id_coord');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agency_activities');
    }
};
