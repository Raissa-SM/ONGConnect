<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategoriaSeeder::class,  // 1º — categorias sem dependência
            ONGSeeder::class,        // 2º — ONGs (dependem de users)
            VoluntarioSeeder::class, // 3º — voluntários (dependem de categorias)
            DemandaSeeder::class,    // 4º — demandas (dependem de ONGs e categorias)
            // Etapa 5 adicionará:
            // InscricaoSeeder::class,
            // AvaliacaoSeeder::class,
        ]);
    }
}
