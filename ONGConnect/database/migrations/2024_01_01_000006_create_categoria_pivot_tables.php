<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('categoria_voluntario', function (Blueprint $table) {
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->foreignId('voluntario_id')->constrained('voluntarios')->cascadeOnDelete();
            $table->primary(['categoria_id', 'voluntario_id']);
        });
        Schema::create('categoria_demanda', function (Blueprint $table) {
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->foreignId('demanda_id')->constrained('demandas')->cascadeOnDelete();
            $table->primary(['categoria_id', 'demanda_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('categoria_demanda'); Schema::dropIfExists('categoria_voluntario'); }
};
