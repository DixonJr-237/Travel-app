<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agencies', function (Blueprint $table) {
            $table->id('id_agence');
            $table->string('name');
            $table->string('phone');
            $table->foreignId('user_id')->constrained('users', 'user_id');
            $table->string('email')->unique();
            $table->foreignId('id_company')->constrained('companies', 'id_company');
            $table->foreignId('id_coord')->constrained('coordinates', 'id_coord');
            $table->foreignId('id_city')->constrained('cities', 'id_city');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agencies');
    }
};
