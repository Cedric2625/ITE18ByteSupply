<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_activities', function (Blueprint $table) {
            $table->id();
            $table->enum('user_type', ['admin', 'buyer']);
            $table->unsignedBigInteger('user_id');
            $table->enum('event', ['login','logout']);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_type','user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_activities');
    }
};


