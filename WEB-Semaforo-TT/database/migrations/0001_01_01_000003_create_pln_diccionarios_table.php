<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pln_diccionarios', function (Blueprint $table) {
            $table->id();
            $table->string('palabra')->unique(); 
            $table->string('categoria'); 
            $table->decimal('peso', 8, 2); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pln_diccionarios');
    }
};