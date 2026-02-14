<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->text('message');
            $table->enum('status', ['unread', 'read'])->default('unread');
            $table->timestamp('created_at');
            $table->string('type')->nullable();
            $table->json('data')->nullable();
            $table->foreignId('user_id')->constrained('users', 'user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
