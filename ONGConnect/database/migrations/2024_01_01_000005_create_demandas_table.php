<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('demandas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ong_id')->constrained('ongs')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descricao');
            $table->enum('tipo', ['presencial', 'doacao', 'habilidade']);
            $table->enum('status', ['rascunho', 'aberta', 'encerrada', 'arquivada'])->default('rascunho');
            $table->date('data_inicio')->nullable();
            $table->date('data_limite')->nullable();
            $table->unsignedInteger('vagas')->default(1);
            $table->string('endereco')->nullable();
            $table->string('cidade')->nullable();
            $table->string('uf', 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('demandas'); }
};
