<?php

namespace Tests\Feature;

use App\Models\Demanda;
use App\Models\Inscricao;
use App\Models\ONG;
use App\Models\User;
use App\Models\Voluntario;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Smoke test que renderiza todas as telas Web contra o banco MySQL real
 * (com dados já semeados), buscando erros de runtime introduzidos pela
 * revisão visual. As rotas GET são somente leitura; os fluxos de escrita
 * rodam dentro de transações revertidas para não alterar os dados reais.
 */
class SmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // phpunit.xml força sqlite :memory:; aqui apontamos para o MySQL real.
        config([
            'database.default'                     => 'mysql',
            'database.connections.mysql.host'      => '127.0.0.1',
            'database.connections.mysql.port'      => '3306',
            'database.connections.mysql.database'  => 'match_voluntarios',
            'database.connections.mysql.username'  => 'root',
            'database.connections.mysql.password'  => '',
        ]);
        DB::purge('mysql');
        DB::setDefaultConnection('mysql');

        // Ambiente sem o MySQL local semeado (ex.: CI) — pula em vez de falhar.
        try {
            if (Demanda::count() === 0) {
                $this->markTestSkipped('Banco MySQL real sem dados semeados.');
            }
        } catch (\Throwable $e) {
            $this->markTestSkipped('Banco MySQL real indisponível: '.$e->getMessage());
        }
    }

    private function voluntarioUser(): User
    {
        return User::where('tipo_perfil', 'voluntario')
            ->whereHas('voluntario')
            ->firstOrFail();
    }

    /** @return array{0: User, 1: Demanda} */
    private function ongUserComDemanda(): array
    {
        $demanda = Demanda::with('ong.user')
            ->whereHas('ong.user')
            ->firstOrFail();

        return [$demanda->ong->user, $demanda];
    }

    public function test_rotas_publicas_renderizam_sem_erro(): void
    {
        $demanda = Demanda::firstOrFail();
        $ong     = ONG::firstOrFail();

        $this->get('/')->assertOk();
        $this->get(route('demandas.index'))->assertOk();
        $this->get(route('demandas.index', ['encerradas' => 1]))->assertOk();
        $this->get(route('demandas.index', ['q' => 'a', 'tipo' => 'presencial']))->assertOk();
        $this->get(route('demandas.show', $demanda->id))->assertOk();
        $this->get(route('ongs.index'))->assertOk();
        $this->get(route('ongs.show', $ong->id))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('registro'))->assertOk();
    }

    public function test_rotas_do_voluntario_renderizam_sem_erro(): void
    {
        $this->actingAs($this->voluntarioUser());

        $this->get(route('dashboard.voluntario'))->assertOk();
        $this->get(route('perfil.voluntario'))->assertOk();
        $this->get(route('inscricoes.minhas'))->assertOk();
        $this->get(route('match.sugestoes'))->assertOk();
        $this->get(route('match.sugestoes', ['raio_km' => 500]))->assertOk();
    }

    public function test_rotas_da_ong_renderizam_sem_erro(): void
    {
        [$ongUser, $demanda] = $this->ongUserComDemanda();
        $this->actingAs($ongUser);

        $this->get(route('dashboard.ong'))->assertOk();
        $this->get(route('perfil.ong'))->assertOk();
        $this->get(route('demandas.minhas'))->assertOk();
        $this->get(route('demandas.criar'))->assertOk();
        $this->get(route('demandas.editar', $demanda->id))->assertOk();
        $this->get(route('inscricoes.demanda', $demanda->id))->assertOk();
    }

    public function test_pagina_404_personalizada_renderiza(): void
    {
        $this->get('/demandas/999999999')
            ->assertNotFound()
            ->assertSee('não encontrada', false);
    }

    public function test_formatacao_de_documentos_nos_models(): void
    {
        $ong = ONG::whereNotNull('cnpj')->first();
        if ($ong) {
            $digitos = preg_replace('/\D/', '', $ong->cnpj);
            if (strlen($digitos) === 14) {
                $this->assertMatchesRegularExpression(
                    '/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/',
                    $ong->cnpj_formatado
                );
            }
        }

        $vol = Voluntario::whereNotNull('cpf')->first();
        if ($vol) {
            $digitos = preg_replace('/\D/', '', $vol->cpf);
            if (strlen($digitos) === 11) {
                $this->assertMatchesRegularExpression(
                    '/^\d{3}\.\d{3}\.\d{3}-\d{2}$/',
                    $vol->cpf_formatado
                );
            }
        }

        $this->assertTrue(true);
    }

    public function test_fluxo_de_inscricao_do_voluntario(): void
    {
        $vol = $this->voluntarioUser();

        $demanda = Demanda::where('status', 'aberta')
            ->whereDoesntHave('inscricoes', fn ($q) => $q->where('voluntario_id', $vol->voluntario->id))
            ->first();

        if (! $demanda) {
            $this->markTestSkipped('Sem demanda aberta disponível para inscrição.');
        }

        DB::beginTransaction();
        try {
            $resp = $this->actingAs($vol)->post(route('inscricoes.store', $demanda->id), [
                'mensagem' => 'Teste automatizado de smoke.',
            ]);
            // Sucesso (redirect) OU regra de negócio bloqueou (redirect back) — nunca 500.
            $resp->assertRedirect();
            $this->assertNotEquals(500, $resp->getStatusCode());
        } finally {
            DB::rollBack();
        }
    }

    public function test_fluxo_de_resposta_da_ong(): void
    {
        $inscricao = Inscricao::where('status', 'pendente')
            ->whereHas('demanda.ong.user')
            ->with('demanda.ong.user')
            ->first();

        if (! $inscricao) {
            $this->markTestSkipped('Sem inscrição pendente para testar resposta da ONG.');
        }

        $ongUser = $inscricao->demanda->ong->user;

        DB::beginTransaction();
        try {
            $resp = $this->actingAs($ongUser)->post(route('inscricoes.aceitar', $inscricao->id));
            $resp->assertRedirect();
            $this->assertNotEquals(500, $resp->getStatusCode());
        } finally {
            DB::rollBack();
        }
    }
}
