<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('inscricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voluntario_id')->constrained('voluntarios')->cascadeOnDelete();
            $table->foreignId('demanda_id')->constrained('demandas')->cascadeOnDelete();
            $table->enum('status', ['pendente', 'aceita', 'recusada', 'concluida', 'cancelada'])->default('pendente');
            $table->text('mensagem')->nullable();
            $table->timestamp('respondida_em')->nullable();
            $table->timestamp('concluida_em')->nullable();
            $table->timestamps();
            $table->unique(['voluntario_id', 'demanda_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('inscricoes'); }
};
