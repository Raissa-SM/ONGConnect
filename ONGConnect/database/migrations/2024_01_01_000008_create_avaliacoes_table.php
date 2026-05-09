<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('avaliacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscricao_id')->constrained('inscricoes')->cascadeOnDelete();
            $table->enum('autor_tipo', ['ong', 'voluntario']);
            $table->tinyInteger('nota')->unsigned();
            $table->text('comentario')->nullable();
            $table->timestamps();
            $table->unique(['inscricao_id', 'autor_tipo']);
        });
    }
    public function down(): void { Schema::dropIfExists('avaliacoes'); }
};
