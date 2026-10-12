<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('interseccions', function (Blueprint $table) {
            $table->decimal('latitud_destino', 10, 7)->nullable()->after('longitud');
            $table->decimal('longitud_destino', 10, 7)->nullable()->after('latitud_destino');
        });
    }
    public function down(): void {
        Schema::table('interseccions', function (Blueprint $table) {
            $table->dropColumn(['latitud_destino', 'longitud_destino']);
        });
    }
};
