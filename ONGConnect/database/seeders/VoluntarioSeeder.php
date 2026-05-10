<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\User;
use App\Models\Voluntario;
use Illuminate\Database\Seeder;

class VoluntarioSeeder extends Seeder
{
    public function run(): void
    {
        // Busca IDs das categorias pelo slug para associar aos voluntários
        $cats = Categoria::pluck('id', 'slug');

        $voluntarios = [
            [
                'user' => ['name' => 'João Carlos Pereira',   'email' => 'joao.pereira@email.com',    'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0001', 'descricao' => 'Desenvolvedor web com 5 anos de experiência.', 'habilidades' => ['desenvolvimento web', 'Laravel', 'Vue.js'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2140, 'longitude' => -49.6440],
                'cats' => ['tecnologia', 'educacao'],
            ],
            [
                'user' => ['name' => 'Maria Fernanda Luz',     'email' => 'maria.luz@email.com',        'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0002', 'descricao' => 'Enfermeira com interesse em saúde comunitária.', 'habilidades' => ['enfermagem', 'primeiros socorros', 'educação em saúde'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2100, 'longitude' => -49.6400],
                'cats' => ['saude', 'idosos', 'criancas'],
            ],
            [
                'user' => ['name' => 'Rafael Souza Melo',     'email' => 'rafael.melo@email.com',      'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0003', 'descricao' => 'Professor de matemática disponível aos fins de semana.', 'habilidades' => ['ensino de matemática', 'reforço escolar'], 'cidade' => 'Ituporanga', 'uf' => 'SC', 'latitude' => -27.4080, 'longitude' => -49.5950],
                'cats' => ['educacao', 'criancas'],
            ],
            [
                'user' => ['name' => 'Camila Rodrigues',       'email' => 'camila.rodrigues@email.com', 'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0004', 'descricao' => 'Designer gráfica e fotógrafa.', 'habilidades' => ['design gráfico', 'fotografia', 'edição de vídeo'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2200, 'longitude' => -49.6500],
                'cats' => ['cultura', 'tecnologia'],
            ],
            [
                'user' => ['name' => 'Lucas Henrique Bauer',  'email' => 'lucas.bauer@email.com',      'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0005', 'descricao' => 'Estudante de Direito, interesse em cidadania.', 'habilidades' => ['orientação jurídica', 'redação'], 'cidade' => 'Taió', 'uf' => 'SC', 'latitude' => -27.1090, 'longitude' => -49.9950],
                'cats' => ['acao-social'],
            ],
            [
                'user' => ['name' => 'Ana Paula Zimmermann',  'email' => 'ana.zimmermann@email.com',   'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0006', 'descricao' => 'Nutricionista voluntária em campanhas de alimentação saudável.', 'habilidades' => ['nutrição', 'culinária', 'educação alimentar'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2050, 'longitude' => -49.6350],
                'cats' => ['saude', 'idosos'],
            ],
            [
                'user' => ['name' => 'Pedro Henrique Santos', 'email' => 'pedro.santos@email.com',     'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0007', 'descricao' => 'Engenheiro civil, disponível para mutirões de construção.', 'habilidades' => ['engenharia civil', 'marcenaria', 'elétrica básica'], 'cidade' => 'Trombudo Central', 'uf' => 'SC', 'latitude' => -27.2960, 'longitude' => -49.7960],
                'cats' => ['acao-social'],
            ],
            [
                'user' => ['name' => 'Bruna Cristina Alves',  'email' => 'bruna.alves@email.com',      'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0008', 'descricao' => 'Psicóloga, foco em saúde mental comunitária.', 'habilidades' => ['psicologia', 'grupos terapêuticos', 'escuta ativa'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2170, 'longitude' => -49.6480],
                'cats' => ['saude', 'idosos', 'criancas'],
            ],
            [
                'user' => ['name' => 'Felipe Augusto Costa',  'email' => 'felipe.costa@email.com',     'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0009', 'descricao' => 'Músico e educador, projetos de musicalização infantil.', 'habilidades' => ['violão', 'canto', 'musicalização infantil'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2120, 'longitude' => -49.6420],
                'cats' => ['cultura', 'educacao', 'criancas'],
            ],
            [
                'user' => ['name' => 'Juliana Aparecida Lima', 'email' => 'juliana.lima@email.com',    'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0010', 'descricao' => 'Assistente social com experiência em terceiro setor.', 'habilidades' => ['assistência social', 'gestão de projetos sociais'], 'cidade' => 'Ituporanga', 'uf' => 'SC', 'latitude' => -27.4100, 'longitude' => -49.5970],
                'cats' => ['acao-social', 'idosos'],
            ],
            [
                'user' => ['name' => 'Thiago Renan Becker',   'email' => 'thiago.becker@email.com',   'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0011', 'descricao' => 'Contador, auxílio em gestão financeira para ONGs.', 'habilidades' => ['contabilidade', 'gestão financeira', 'excel avançado'], 'cidade' => 'Rio do Oeste', 'uf' => 'SC', 'latitude' => -27.1890, 'longitude' => -49.7460],
                'cats' => ['acao-social'],
            ],
            [
                'user' => ['name' => 'Natália Vitória Schuh',  'email' => 'natalia.schuh@email.com',   'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0012', 'descricao' => 'Veterinária voluntária em feiras de adoção.', 'habilidades' => ['medicina veterinária', 'castração', 'educação animal'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2080, 'longitude' => -49.6360],
                'cats' => ['animal'],
            ],
            [
                'user' => ['name' => 'Vinícius Eduardo Neis',  'email' => 'vinicius.neis@email.com',   'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0013', 'descricao' => 'Educador físico, treinos coletivos gratuitos.', 'habilidades' => ['educação física', 'treinamento funcional', 'yoga'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2160, 'longitude' => -49.6460],
                'cats' => ['esporte', 'saude'],
            ],
            [
                'user' => ['name' => 'Larissa Gonçalves',      'email' => 'larissa.goncalves@email.com','password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0014', 'descricao' => 'Bióloga com foco em educação ambiental.', 'habilidades' => ['biologia', 'educação ambiental', 'botânica'], 'cidade' => 'Agronômica', 'uf' => 'SC', 'latitude' => -27.2556, 'longitude' => -49.8256],
                'cats' => ['ambiental'],
            ],
            [
                'user' => ['name' => 'Diego Fabian Wendt',     'email' => 'diego.wendt@email.com',     'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0015', 'descricao' => 'Mecânico de automóveis, voluntário em transporte social.', 'habilidades' => ['mecânica', 'motorista', 'manutenção geral'], 'cidade' => 'Laurentino', 'uf' => 'SC', 'latitude' => -27.2356, 'longitude' => -49.7256],
                'cats' => ['acao-social'],
            ],
            [
                'user' => ['name' => 'Carla Regina Hoepers',   'email' => 'carla.hoepers@email.com',   'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0016', 'descricao' => 'Professora de inglês e espanhol.', 'habilidades' => ['inglês', 'espanhol', 'tradução'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2095, 'longitude' => -49.6395],
                'cats' => ['educacao'],
            ],
            [
                'user' => ['name' => 'Rodrigo Luan Pinheiro',  'email' => 'rodrigo.pinheiro@email.com', 'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0017', 'descricao' => 'Técnico em TI, suporte e capacitação digital.', 'habilidades' => ['suporte técnico', 'redes', 'capacitação digital'], 'cidade' => 'Taió', 'uf' => 'SC', 'latitude' => -27.1100, 'longitude' => -49.9960],
                'cats' => ['tecnologia', 'educacao'],
            ],
            [
                'user' => ['name' => 'Priscila Fátima Krüger', 'email' => 'priscila.kruger@email.com',  'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0018', 'descricao' => 'Cozinheira, coordenação de cozinhas solidárias.', 'habilidades' => ['culinária', 'gestão de cozinha', 'nutrição básica'], 'cidade' => 'Ituporanga', 'uf' => 'SC', 'latitude' => -27.4095, 'longitude' => -49.5965],
                'cats' => ['acao-social', 'saude'],
            ],
            [
                'user' => ['name' => 'Marcelo Antônio Boing',  'email' => 'marcelo.boing@email.com',   'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0019', 'descricao' => 'Advogado, orientação jurídica gratuita.', 'habilidades' => ['direito civil', 'direito trabalhista', 'mediação'], 'cidade' => 'Rio do Sul', 'uf' => 'SC', 'latitude' => -27.2130, 'longitude' => -49.6430],
                'cats' => ['acao-social', 'idosos'],
            ],
            [
                'user' => ['name' => 'Aline Beatriz Frank',    'email' => 'aline.frank@email.com',     'password' => 'senha1234', 'tipo_perfil' => 'voluntario'],
                'vol'  => ['telefone' => '(47) 99111-0020', 'descricao' => 'Assistente administrativo e captadora de recursos.', 'habilidades' => ['captação de recursos', 'escrita de projetos', 'gestão administrativa'], 'cidade' => 'Trombudo Central', 'uf' => 'SC', 'latitude' => -27.2950, 'longitude' => -49.7950],
                'cats' => ['acao-social', 'cultura'],
            ],
        ];

        foreach ($voluntarios as $dados) {
            $user = User::firstOrCreate(
                ['email' => $dados['user']['email']],
                $dados['user']
            );

            $voluntario = Voluntario::firstOrCreate(
                ['user_id' => $user->id],
                array_merge($dados['vol'], ['user_id' => $user->id])
            );

            // Associa categorias (busca os slugs)
            $ids = array_filter(
                array_map(fn($slug) => $cats[$slug] ?? null, $dados['cats'])
            );
            $voluntario->categorias()->sync($ids);
        }

        $this->command->info('✅ Voluntários criados: ' . count($voluntarios));
    }
}
