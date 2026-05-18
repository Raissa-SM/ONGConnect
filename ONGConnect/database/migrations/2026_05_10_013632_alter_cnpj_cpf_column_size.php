<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ongs', function (Blueprint $table) {
            $table->string('cnpj', 20)->nullable()->change();
        });

        Schema::table('voluntarios', function (Blueprint $table) {
            $table->string('cpf', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('ongs', function (Blueprint $table) {
            $table->string('cnpj', 14)->nullable()->change();
        });

        Schema::table('voluntarios', function (Blueprint $table) {
            $table->string('cpf', 11)->nullable()->change();
        });
    }
};
