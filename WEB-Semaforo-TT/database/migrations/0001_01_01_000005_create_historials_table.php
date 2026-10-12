<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('accion'); 
            $table->text('detalles')->nullable(); 
            $table->string('ip_address', 45)->nullable(); 
            
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historials');
    }
};