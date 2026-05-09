<?php
namespace Database\Seeders;
use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class CategoriaSeeder extends Seeder
{
    public function run(): void {
        $categorias = [
            ['nome' => 'Educação',    'descricao' => 'Atividades de ensino, tutoria e letramento.'],
            ['nome' => 'Saúde',       'descricao' => 'Apoio a ações de saúde preventiva e assistencial.'],
            ['nome' => 'Ambiental',   'descricao' => 'Ações de preservação e sustentabilidade ambiental.'],
            ['nome' => 'Ação Social', 'descricao' => 'Campanhas e mutirões de assistência à comunidade.'],
            ['nome' => 'Tecnologia',  'descricao' => 'Suporte técnico, desenvolvimento e capacitação digital.'],
            ['nome' => 'Cultura',     'descricao' => 'Arte, música, teatro e preservação da identidade regional.'],
            ['nome' => 'Esporte',     'descricao' => 'Atividades físicas, jogos e bem-estar.'],
            ['nome' => 'Animal',      'descricao' => 'Proteção e adoção responsável de animais.'],
            ['nome' => 'Idosos',      'descricao' => 'Cuidado, companhia e atividades para a terceira idade.'],
            ['nome' => 'Crianças',    'descricao' => 'Apoio a crianças em situação de vulnerabilidade.'],
        ];
        foreach ($categorias as $dados) {
            Categoria::firstOrCreate(
                ['slug' => Str::slug($dados['nome'])],
                array_merge($dados, ['slug' => Str::slug($dados['nome'])])
            );
        }
        $this->command->info('✅ Categorias criadas: ' . count($categorias));
    }
}
