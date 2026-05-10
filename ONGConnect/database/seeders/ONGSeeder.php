<?php

namespace Database\Seeders;

use App\Models\ONG;
use App\Models\User;
use Illuminate\Database\Seeder;

class ONGSeeder extends Seeder
{
    public function run(): void
    {
        $ongs = [
            [
                'user' => [
                    'name'        => 'Mãos Solidárias',
                    'email'       => 'contato@maossolidarias.org.br',
                    'password'    => 'senha1234',
                    'tipo_perfil' => 'ong',
                ],
                'ong' => [
                    'razao_social'     => 'Associação Mãos Solidárias do Alto Vale',
                    'cnpj'             => '12345678000101',
                    'telefone'         => '(47) 3521-1001',
                    'descricao_missao' => 'Apoio a famílias em situação de vulnerabilidade social no Alto Vale do Itajaí, com foco em alimentação e educação.',
                    'endereco'         => 'Rua Felipe Schmidt, 320',
                    'cidade'           => 'Rio do Sul',
                    'uf'               => 'SC',
                    'latitude'         => -27.2138,
                    'longitude'        => -49.6438,
                ],
            ],
            [
                'user' => [
                    'name'        => 'Lar São Francisco',
                    'email'       => 'saofrancisco@larsf.org.br',
                    'password'    => 'senha1234',
                    'tipo_perfil' => 'ong',
                ],
                'ong' => [
                    'razao_social'     => 'Lar dos Idosos São Francisco de Assis',
                    'cnpj'             => '23456789000102',
                    'telefone'         => '(47) 3528-2002',
                    'descricao_missao' => 'Acolhimento e cuidado de idosos em situação de abandono ou vulnerabilidade em Ituporanga e região.',
                    'endereco'         => 'Rua Sete de Setembro, 88',
                    'cidade'           => 'Ituporanga',
                    'uf'               => 'SC',
                    'latitude'         => -27.4089,
                    'longitude'        => -49.5956,
                ],
            ],
            [
                'user' => [
                    'name'        => 'Casa da Criança',
                    'email'       => 'casacrianca@taio.org.br',
                    'password'    => 'senha1234',
                    'tipo_perfil' => 'ong',
                ],
                'ong' => [
                    'razao_social'     => 'Casa da Criança Feliz de Taió',
                    'cnpj'             => '34567890000103',
                    'telefone'         => '(47) 3543-3003',
                    'descricao_missao' => 'Proteção e desenvolvimento integral de crianças e adolescentes em risco social na região de Taió.',
                    'endereco'         => 'Rua Nereu Ramos, 215',
                    'cidade'           => 'Taió',
                    'uf'               => 'SC',
                    'latitude'         => -27.1089,
                    'longitude'        => -49.9956,
                ],
            ],
            [
                'user' => [
                    'name'        => 'ONG Verde Vida',
                    'email'       => 'verdedvida@verdedvida.org.br',
                    'password'    => 'senha1234',
                    'tipo_perfil' => 'ong',
                ],
                'ong' => [
                    'razao_social'     => 'ONG Verde Vida — Sustentabilidade e Meio Ambiente',
                    'cnpj'             => '45678901000104',
                    'telefone'         => '(47) 3551-4004',
                    'descricao_missao' => 'Preservação do meio ambiente e educação ambiental no Vale do Itajaí, com projetos de reflorestamento e coleta seletiva.',
                    'endereco'         => 'Estrada Geral do Morro, km 3',
                    'cidade'           => 'Trombudo Central',
                    'uf'               => 'SC',
                    'latitude'         => -27.2956,
                    'longitude'        => -49.7956,
                ],
            ],
            [
                'user' => [
                    'name'        => 'Projeto Reintegrar',
                    'email'       => 'reintegrar@reintegrar.org.br',
                    'password'    => 'senha1234',
                    'tipo_perfil' => 'ong',
                ],
                'ong' => [
                    'razao_social'     => 'Projeto Reintegrar — Ressocialização e Cidadania',
                    'cnpj'             => '56789012000105',
                    'telefone'         => '(47) 3549-5005',
                    'descricao_missao' => 'Apoio à ressocialização de egressos do sistema prisional e pessoas em situação de rua, com capacitação profissional.',
                    'endereco'         => 'Rua Marechal Deodoro, 55',
                    'cidade'           => 'Rio do Oeste',
                    'uf'               => 'SC',
                    'latitude'         => -27.1889,
                    'longitude'        => -49.7456,
                ],
            ],
        ];

        foreach ($ongs as $dados) {
            $user = User::firstOrCreate(
                ['email' => $dados['user']['email']],
                $dados['user']
            );
            ONG::firstOrCreate(
                ['user_id' => $user->id],
                array_merge($dados['ong'], ['user_id' => $user->id])
            );
        }

        $this->command->info('✅ ONGs criadas: ' . count($ongs));
    }
}
