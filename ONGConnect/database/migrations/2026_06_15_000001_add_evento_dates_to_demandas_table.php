<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('demandas', function (Blueprint $table) {
            // Data/hora do evento em si — separada da janela de inscrição (data_inicio/data_limite).
            $table->dateTime('evento_inicio')->nullable()->after('data_limite');
            $table->dateTime('evento_fim')->nullable()->after('evento_inicio');
        });
    }
    public function down(): void {
        Schema::table('demandas', function (Blueprint $table) {
            $table->dropColumn(['evento_inicio', 'evento_fim']);
        });
    }
};
