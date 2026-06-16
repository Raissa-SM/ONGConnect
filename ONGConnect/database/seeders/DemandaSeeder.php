<?php

namespace Database\Seeders;

use App\Enums\StatusDemanda;
use App\Enums\TipoDemanda;
use App\Models\Categoria;
use App\Models\Demanda;
use App\Models\ONG;
use Illuminate\Database\Seeder;

class DemandaSeeder extends Seeder
{
    public function run(): void
    {
        $cats = Categoria::pluck('id', 'slug');

        $ong = fn (string $email) => ONG::whereHas('user', fn ($q) => $q->where('email', $email))->first();

        $maos       = $ong('contato@maossolidarias.org.br');
        $lar        = $ong('saofrancisco@larsf.org.br');
        $casa       = $ong('casacrianca@taio.org.br');
        $verde      = $ong('verdedvida@verdedvida.org.br');
        $reintegrar = $ong('reintegrar@reintegrar.org.br');

        $demandas = [
            // ── Mãos Solidárias ──────────────────────────────────────────────
            [
                'ong'    => $maos,
                'dados'  => [
                    'titulo'      => 'Reforço escolar para crianças em vulnerabilidade',
                    'descricao'   => 'Buscamos voluntários para dar aulas de reforço de matemática e português para crianças de 7 a 12 anos em situação de vulnerabilidade social.',
                    'tipo'        => TipoDemanda::Presencial,
                    'status'      => StatusDemanda::Aberta,
                    'data_inicio' => '2026-06-01',
                    'data_limite' => '2026-08-31',
                    'evento_inicio' => '2026-06-20 14:00',
                    'evento_fim'    => '2026-06-20 16:00',
                    'vagas'       => 4,
                    'cidade'      => 'Rio do Sul',
                    'uf'          => 'SC',
                    'latitude'    => -27.2138,
                    'longitude'   => -49.6438,
                ],
                'cats' => ['educacao', 'criancas'],
            ],
            [
                'ong'    => $maos,
                'dados'  => [
                    'titulo'      => 'Campanha de arrecadação de cestas básicas',
                    'descricao'   => 'Voluntários para organizar e distribuir cestas básicas para 50 famílias cadastradas. Atividade presencial em um sábado.',
                    'tipo'        => TipoDemanda::Doacao,
                    'status'      => StatusDemanda::Aberta,
                    'data_inicio' => '2026-05-24',
                    'data_limite' => '2026-05-24',
                    'evento_inicio' => '2026-05-24 09:00',
                    'evento_fim'    => '2026-05-24 13:00',
                    'vagas'       => 8,
                    'cidade'      => 'Rio do Sul',
                    'uf'          => 'SC',
                    'latitude'    => -27.2138,
                    'longitude'   => -49.6438,
                ],
                'cats' => ['acao-social'],
            ],
            [
                'ong'   => $maos,
                'dados' => [
                    'titulo'    => 'Criação de site institucional',
                    'descricao' => 'Desenvolvedor web voluntário para criar site simples com informações sobre projetos e formulário de doação.',
                    'tipo'      => TipoDemanda::Habilidade,
                    'status'    => StatusDemanda::Rascunho,
                    'vagas'     => 1,
                    'cidade'    => 'Rio do Sul',
                    'uf'        => 'SC',
                ],
                'cats' => ['tecnologia'],
            ],
            // ── Lar São Francisco ─────────────────────────────────────────────
            [
                'ong'   => $lar,
                'dados' => [
                    'titulo'      => 'Visitas de companhia para idosos',
                    'descricao'   => 'Voluntários para visitar e conversar com idosos acolhidos, realizando jogos, leitura e música. Periodicidade: fins de semana.',
                    'tipo'        => TipoDemanda::Presencial,
                    'status'      => StatusDemanda::Aberta,
                    'data_inicio' => '2026-06-07',
                    'data_limite' => '2026-12-31',
                    'evento_inicio' => '2026-06-21 15:00',
                    'evento_fim'    => '2026-06-21 17:00',
                    'vagas'       => 6,
                    'cidade'      => 'Ituporanga',
                    'uf'          => 'SC',
                    'latitude'    => -27.4089,
                    'longitude'   => -49.5956,
                ],
                'cats' => ['idosos', 'saude'],
            ],
            [
                'ong'   => $lar,
                'dados' => [
                    'titulo'      => 'Fisioterapeuta voluntário',
                    'descricao'   => 'Profissional de fisioterapia para atender idosos com mobilidade reduzida, 2 vezes por semana no período da manhã.',
                    'tipo'        => TipoDemanda::Habilidade,
                    'status'      => StatusDemanda::Aberta,
                    'data_inicio' => '2026-06-01',
                    'data_limite' => '2026-11-30',
                    'vagas'       => 1,
                    'cidade'      => 'Ituporanga',
                    'uf'          => 'SC',
                    'latitude'    => -27.4089,
                    'longitude'   => -49.5956,
                ],
                'cats' => ['idosos', 'saude'],
            ],
            // ── Casa da Criança Feliz ─────────────────────────────────────────
            [
                'ong'   => $casa,
                'dados' => [
                    'titulo'      => 'Oficina de arte e pintura para crianças',
                    'descricao'   => 'Artista ou educador para ministrar oficinas de artes plásticas para crianças de 6 a 14 anos. Atividade mensal.',
                    'tipo'        => TipoDemanda::Habilidade,
                    'status'      => StatusDemanda::Aberta,
                    'data_inicio' => '2026-06-14',
                    'data_limite' => '2026-11-15',
                    'evento_inicio' => '2026-06-28 10:00',
                    'evento_fim'    => '2026-06-28 12:00',
                    'vagas'       => 2,
                    'cidade'      => 'Taió',
                    'uf'          => 'SC',
                    'latitude'    => -27.1089,
                    'longitude'   => -49.9956,
                ],
                'cats' => ['criancas', 'cultura'],
            ],
            [
                'ong'   => $casa,
                'dados' => [
                    'titulo'      => 'Doação de material escolar',
                    'descricao'   => 'Arrecadação e entrega de material escolar para 80 crianças antes do início das aulas.',
                    'tipo'        => TipoDemanda::Doacao,
                    'status'      => StatusDemanda::Encerrada,
                    'data_inicio' => '2026-01-20',
                    'data_limite' => '2026-02-10',
                    'evento_inicio' => '2026-02-08 09:00',
                    'evento_fim'    => '2026-02-08 12:00',
                    'vagas'       => 10,
                    'cidade'      => 'Taió',
                    'uf'          => 'SC',
                ],
                'cats' => ['criancas', 'educacao'],
            ],
            // ── ONG Verde Vida ────────────────────────────────────────────────
            [
                'ong'   => $verde,
                'dados' => [
                    'titulo'      => 'Mutirão de reflorestamento',
                    'descricao'   => 'Voluntários para plantio de mudas nativas em área de preservação permanente às margens do Rio Itajaí. Atividade de 1 dia.',
                    'tipo'        => TipoDemanda::Presencial,
                    'status'      => StatusDemanda::Aberta,
                    'data_inicio' => '2026-06-21',
                    'data_limite' => '2026-06-21',
                    'evento_inicio' => '2026-06-21 08:00',
                    'evento_fim'    => '2026-06-21 12:00',
                    'vagas'       => 20,
                    'cidade'      => 'Trombudo Central',
                    'uf'          => 'SC',
                    'latitude'    => -27.2956,
                    'longitude'   => -49.7956,
                ],
                'cats' => ['ambiental'],
            ],
            [
                'ong'   => $verde,
                'dados' => [
                    'titulo'      => 'Educação ambiental em escolas',
                    'descricao'   => 'Biólogo ou educador para ministrar palestras sobre meio ambiente e separação de resíduos em escolas públicas da região.',
                    'tipo'        => TipoDemanda::Habilidade,
                    'status'      => StatusDemanda::Aberta,
                    'data_inicio' => '2026-06-01',
                    'data_limite' => '2026-10-31',
                    'vagas'       => 3,
                    'cidade'      => 'Trombudo Central',
                    'uf'          => 'SC',
                ],
                'cats' => ['ambiental', 'educacao'],
            ],
            // ── Projeto Reintegrar ────────────────────────────────────────────
            [
                'ong'   => $reintegrar,
                'dados' => [
                    'titulo'      => 'Curso de informática básica para egressos',
                    'descricao'   => 'Voluntário com conhecimentos em informática para ministrar curso de 20h voltado à empregabilidade de egressos do sistema prisional.',
                    'tipo'        => TipoDemanda::Habilidade,
                    'status'      => StatusDemanda::Aberta,
                    'data_inicio' => '2026-06-09',
                    'data_limite' => '2026-07-18',
                    'evento_inicio' => '2026-06-14 09:00',
                    'evento_fim'    => '2026-06-16 18:00',
                    'vagas'       => 2,
                    'cidade'      => 'Rio do Oeste',
                    'uf'          => 'SC',
                    'latitude'    => -27.1889,
                    'longitude'   => -49.7456,
                ],
                'cats' => ['tecnologia', 'acao-social'],
            ],
        ];

        $criadas = 0;

        foreach ($demandas as $item) {
            if (!$item['ong']) {
                continue;
            }

            $demanda = Demanda::create(array_merge(
                $item['dados'],
                ['ong_id' => $item['ong']->id]
            ));

            $ids = array_values(array_filter(
                array_map(fn ($slug) => $cats[$slug] ?? null, $item['cats'])
            ));

            if (!empty($ids)) {
                $demanda->categorias()->sync($ids);
            }

            $criadas++;
        }

        $this->command->info("✅ Demandas criadas: {$criadas}");
    }
}
