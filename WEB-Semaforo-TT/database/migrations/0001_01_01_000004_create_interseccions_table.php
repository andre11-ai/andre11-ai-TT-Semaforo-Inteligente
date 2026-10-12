<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interseccions', function (Blueprint $table) {
            $table->id(); 
            $table->string('nombre')->unique(); 
            $table->string('ip_controlador')->unique(); 
            
            $table->decimal('latitud', 10, 7);
            $table->decimal('longitud', 10, 7);
            
            $table->integer('tiempo_verde')->default(45);
            $table->integer('tiempo_amarillo')->default(4);
            $table->integer('tiempo_rojo')->default(45);
            
            $table->string('estado_operativo')->default('Inteligente');
            
            $table->boolean('estado_red')->default(true); 
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interseccions');
    }
};