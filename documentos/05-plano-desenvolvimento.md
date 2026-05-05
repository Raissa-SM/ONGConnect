# 05. Plano de Desenvolvimento

> **Próxima fase:** Implementação. Codificação baseada nos padrões MVC, com o controlador processando o fluxo e o modelo refletindo o estado dos dados.

## 1. Visão geral

Período: **05/05/2026 → 23/06/2026** (7 semanas).
Equipe: **dupla** (chamados aqui de Dev A e Dev B).
Modelo de trabalho: **iterativo por etapas semanais**, cada etapa entregando incremento testável e fechando com push integrado para a branch `main` do GitHub.

```
[ semana 1 ]  Etapa 0  - Setup do projeto e fundamentos
[ semana 2 ]  Etapa 1  - Modelagem, migrations e Sanctum
[ semana 3 ]  Etapa 2  - CRUDs core (ONG, Voluntário, Categoria)
[ semana 4 ]  Etapa 3  - Demandas e Inscrições com workflow
[ semana 5 ]  Etapa 4  - Match (algoritmo) e geolocalização
[ semana 6 ]  Etapa 5  - Avaliações, Dashboard e Blade views
[ semana 7 ]  Etapa 6  - Polimento, Swagger, vídeo e apresentação
```

## 2. Etapa 0 — Setup do projeto e fundamentos

**Período:** 05/05 a 11/05
**Objetivo:** preparar todo o terreno para o desenvolvimento sem mais ter que mexer em infraestrutura.

### Tarefas

- [ ] Instalar Laragon ou XAMPP em ambas as máquinas; conferir PHP 8.2+, MySQL 8 e Composer ativos.
- [ ] Criar repositório no GitHub (`match-voluntarios-ongs`) com `README.md` inicial e `.gitignore` do Laravel.
- [ ] Subir esta pasta `documentos/` para o repositório.
- [ ] Rodar `composer create-project laravel/laravel match-voluntarios-ongs` dentro do diretório clonado.
- [ ] Configurar `.env` (banco `match_voluntarios`, charset utf8mb4).
- [ ] Criar o banco no MySQL local; rodar `php artisan migrate` para validar conexão.
- [ ] Instalar Laravel Sanctum: `composer require laravel/sanctum` e publicar config.
- [ ] Instalar L5-Swagger: `composer require darkaonline/l5-swagger` e publicar config.
- [ ] Instalar Tailwind via CDN no layout principal Blade.
- [ ] Criar branches de trabalho: `dev` (integração) + branches por feature.
- [ ] Definir convenção de commits (sugestão: Conventional Commits — `feat:`, `fix:`, `docs:`, `refactor:`).
- [ ] Configurar GitHub Project (kanban) com colunas: Backlog, Em desenvolvimento, Em revisão, Concluído.

### Entregáveis

- Repositório com Laravel rodando e respondendo na rota inicial.
- `php artisan serve` levanta sem erros.
- Sanctum e L5-Swagger instalados.
- Pasta `documentos/` versionada.

### Divisão sugerida

| Dev A | Dev B |
|-------|-------|
| Setup do Laravel e .env | Configuração de Sanctum e Swagger |
| Layout Blade base | GitHub Project + convenções de branch/commit |

## 3. Etapa 1 — Modelagem, migrations e Sanctum

**Período:** 12/05 a 18/05
**Objetivo:** materializar o modelo de dados e ter autenticação funcionando.

### Tarefas

- [ ] Criar todas as migrations (na ordem): `users` (já existe — apenas adicionar coluna `tipo_perfil`), `categorias`, `ongs`, `voluntarios`, `demandas`, `categoria_voluntario`, `categoria_demanda`, `inscricoes`, `avaliacoes`.
- [ ] Criar todos os Models Eloquent com relacionamentos declarados (`hasOne`, `hasMany`, `belongsToMany`, etc.).
- [ ] Implementar enums PHP 8 (`StatusDemanda`, `StatusInscricao`, `TipoDemanda`, `TipoPerfil`, `AutorAvaliacao`).
- [ ] Configurar `User` para usar Sanctum (`HasApiTokens`).
- [ ] Implementar `AuthController` com `registro`, `login`, `logout`, `eu`.
- [ ] Criar FormRequests para registro e login.
- [ ] Criar middleware ou Policy básica para distinguir perfil ONG vs Voluntário.
- [ ] Criar `CategoriaSeeder` e `DatabaseSeeder` com chamadas iniciais.
- [ ] Testar fluxo de registro/login pelo Postman.

### Entregáveis

- `php artisan migrate:fresh --seed` cria toda a estrutura e popula categorias.
- Endpoints de autenticação funcionais e testados.
- Token Sanctum sendo emitido e respeitado em rota protegida de teste.

### Divisão sugerida

| Dev A | Dev B |
|-------|-------|
| Migrations + relacionamentos Eloquent | Sanctum, AuthController, FormRequests |
| Enums PHP | CategoriaSeeder e DatabaseSeeder |

## 4. Etapa 2 — CRUDs core (ONG, Voluntário, Categoria)

**Período:** 19/05 a 25/05
**Objetivo:** ter os três CRUDs base funcionando completamente, com validação e formatação de resposta.

### Tarefas

- [ ] Implementar `CategoriaController` (CRUD completo) com FormRequests e CategoriaResource.
- [ ] Implementar `ONGController` (read + update do próprio perfil) com Policies.
- [ ] Implementar `VoluntarioController` (read + update do próprio perfil + sync de categorias).
- [ ] Adicionar anotações OpenAPI em todos os métodos para gerar Swagger.
- [ ] Criar `ONGSeeder` e `VoluntarioSeeder` com 5 ONGs e 20 voluntários fictícios do Alto Vale.
- [ ] Validar todas as rotas via Postman e Swagger UI.
- [ ] Implementar Policies: `ONGPolicy::update`, `VoluntarioPolicy::update`.

### Entregáveis

- Três CRUDs operacionais via API.
- Swagger UI já lista os endpoints com docs preenchidas.
- Banco populado com dados realistas para testes.

### Divisão sugerida

| Dev A | Dev B |
|-------|-------|
| CRUD Categoria + Voluntário + seeder voluntário | CRUD ONG + seeder ONG + Policies |
| Anotações OpenAPI dos endpoints próprios | Anotações OpenAPI dos endpoints próprios |

## 5. Etapa 3 — Demandas e Inscrições com workflow

**Período:** 26/05 a 01/06
**Objetivo:** o coração funcional do sistema — publicar demandas e gerenciar inscrições.

### Tarefas

- [ ] Implementar `DemandaController` (CRUD completo) com filtros (`?tipo=`, `?categoria=`, `?q=`).
- [ ] Implementar ações de transição de status: `publicar`, `encerrar`.
- [ ] Implementar `InscricaoController` com endpoints: criar, listar minhas, listar da demanda, aceitar, recusar, concluir, cancelar.
- [ ] Criar `InscricaoWorkflowService` encapsulando todas as transições válidas (state machine simples).
- [ ] Garantir as regras: RN-02 (só inscreve se aberta), RN-03 (não duplica), RN-04 (fecha quando esgotam vagas), RN-07 (só ONG dona aceita/recusa).
- [ ] Criar `DemandaPolicy` e `InscricaoPolicy`.
- [ ] Criar `DemandaSeeder` (15 demandas) e `InscricaoSeeder` (30 inscrições distribuídas pelos status).
- [ ] Documentar tudo em Swagger.

### Entregáveis

- API completa de demandas com filtros funcionando.
- Workflow de inscrição testável de ponta a ponta no Postman.
- Banco com dados que cobrem todos os cenários do fluxo.

### Divisão sugerida

| Dev A | Dev B |
|-------|-------|
| DemandaController + filtros + Policy | InscricaoController + WorkflowService + Policy |
| DemandaSeeder | InscricaoSeeder |

## 6. Etapa 4 — Match e geolocalização

**Período:** 02/06 a 08/06
**Objetivo:** implementar o diferencial técnico do projeto.

### Tarefas

- [ ] Implementar `MatchService::calcularScore(Voluntario, Demanda)` conforme fórmula em `03-arquitetura-design.md` §5.
- [ ] Implementar função utilitária de Haversine em `app/Support/Geo.php`.
- [ ] Implementar filtro por raio na listagem de demandas (`?lat=&lng=&raio=`).
- [ ] Implementar `MatchController` com endpoints `/api/match/sugestoes` e `/api/match/score`.
- [ ] Otimizar consultas: pré-filtrar demandas pelo raio antes de pontuar (evita score desnecessário).
- [ ] Validar resultado com voluntário fictício pré-conhecido (verificar manualmente que o score retorna valores esperados).

### Entregáveis

- Endpoint de sugestões funcionando: voluntário autenticado recebe lista ordenada por score.
- Filtro de raio aplicado no catálogo público.
- Score documentado e reproduzível.

### Divisão sugerida

| Dev A | Dev B |
|-------|-------|
| Geo helper (Haversine) + filtro por raio | MatchService + MatchController |
| Validação manual com dados de seed | Anotações Swagger do match |

## 7. Etapa 5 — Avaliações, Dashboard e Blade views

**Período:** 09/06 a 15/06
**Objetivo:** fechar a parte funcional e entregar a camada de apresentação MVC.

### Tarefas

#### Avaliações
- [ ] Implementar `AvaliacaoController` (criar, listar por voluntário, listar por ONG).
- [ ] Implementar `AvaliacaoService` com regra: só permite criar se inscrição está concluída e o autor está autorizado (RN-05).
- [ ] Criar `AvaliacaoSeeder` para todas as inscrições concluídas do seed anterior.
- [ ] Recalcular `fator_avaliacao` no MatchService.

#### Dashboard
- [ ] Implementar `DashboardController` (Web e Api) com estatísticas agregadas.
- [ ] Endpoints `/api/dashboard/voluntario` e `/api/dashboard/ong`.

#### Blade views (camada de demonstração MVC)
- [ ] `resources/views/layouts/app.blade.php` — layout base com Tailwind via CDN.
- [ ] `home.blade.php` — catálogo público de demandas com filtros.
- [ ] `demandas/show.blade.php` — detalhe de demanda.
- [ ] `auth/login.blade.php` e `auth/registro.blade.php`.
- [ ] `dashboard/voluntario.blade.php` — sugestões + minhas inscrições.
- [ ] `dashboard/ong.blade.php` — minhas demandas + inscrições recebidas.
- [ ] `demandas/form.blade.php` — criação/edição.
- [ ] Implementar `HomeController`, `DashboardController` (web) e `DemandaWebController`.

### Entregáveis

- Avaliações funcionais e refletidas no score de match.
- Dashboards com números reais retornados pelos seeders.
- Pelo menos 6 páginas Blade navegáveis demonstrando o MVC clássico.

### Divisão sugerida

| Dev A | Dev B |
|-------|-------|
| Avaliações + DashboardController API | Layout Blade + páginas públicas (home, detalhe) |
| AvaliacaoSeeder | Páginas Blade autenticadas (dashboards e formulários) |

## 8. Etapa 6 — Polimento, Swagger, vídeo e apresentação

**Período:** 16/06 a 22/06 (entrega 23/06)
**Objetivo:** qualidade final e materiais de entrega.

### Tarefas

- [ ] Revisar todas as anotações OpenAPI; garantir que cada endpoint tem exemplo de request e response.
- [ ] Reorganizar grupos (tags) no Swagger para leitura clara.
- [ ] Caçar e corrigir bugs identificados no uso real das telas.
- [ ] Padronizar mensagens de erro em português.
- [ ] Garantir que `php artisan migrate:fresh --seed` rode sem erros do zero.
- [ ] Atualizar `README.md` raiz com: descrição, stack, requisitos, passo a passo de instalação, comandos úteis, créditos da equipe.
- [ ] Atualizar `documentos/` se houver discrepâncias entre docs e implementação.
- [ ] Gravar vídeo de apresentação (até 3 min). Roteiro sugerido:
  - 0:00–0:30 — Contexto e propósito (Universidade Solidária, dores reais).
  - 0:30–1:30 — Demonstração: catálogo público → cadastro voluntário → match sugerido → inscrição → ONG aceita → conclusão → avaliação.
  - 1:30–2:30 — Tour pelo Swagger e pela arquitetura técnica.
  - 2:30–3:00 — Encerramento: equipe e link do repositório.
- [ ] Preparar slides curtos para apresentação em sala (5-10 slides).
- [ ] Marcar entrega no Class Room (ambos os membros).

### Entregáveis finais (para o Class Room)

1. Link do repositório GitHub público.
2. Pasta `documentos/` atualizada com todos os 6 arquivos `.md`.
3. Vídeo de apresentação (até 3 minutos).
4. Slides da apresentação.

## 9. Definição de pronto (Definition of Done)

Uma tarefa só é considerada concluída quando:

- ✅ Código mergeado na branch `dev` (ou `main`) via Pull Request revisado pela dupla.
- ✅ Testado manualmente via Postman ou Swagger UI.
- ✅ Anotações OpenAPI atualizadas (se for endpoint).
- ✅ Migration / seeder funcionam em ambiente do zero (`migrate:fresh --seed`).
- ✅ Convenção de commits respeitada.
- ✅ Sem warnings ou notices visíveis no log durante uso normal.

## 10. Riscos e mitigações

| Risco | Probabilidade | Impacto | Mitigação |
|-------|---------------|---------|-----------|
| Atraso na etapa 1 (modelagem) compromete tudo | Média | Alto | Reservar a primeira semana inteira para isso e não cortar caminho. |
| Conflitos no Git por trabalho paralelo nas mesmas pastas | Média | Médio | Usar branches por feature e fazer rebase frequente; combinar quem mexe em quê via GitHub Project. |
| Algoritmo de match com dados de teste insuficientes | Baixa | Médio | Garantir que o seed cria voluntários e demandas geograficamente distribuídos no Alto Vale. |
| Dificuldade com Sanctum em ambiente Apache (Laragon/XAMPP) | Baixa | Médio | Documentar config de CORS e `.htaccess`; testar cedo na etapa 1. |
| Subestimação do tempo das views Blade | Média | Baixo | Usar Tailwind via CDN para cortar configuração; aceitar que o foco é o backend. |
| Vídeo demorar para ser gravado/editado | Média | Médio | Gravar sem edição complexa; aceitar OBS + corte simples; reservar dia 22/06 só para isso. |

## 11. Ferramentas e workflow

- **GitHub** — código + Issues + Project (kanban).
- **GitHub Discussions** ou **WhatsApp** — comunicação rápida da dupla.
- **Postman/Insomnia** — coleção compartilhada para testes manuais.
- **Swagger UI** — fonte de verdade da API durante e após o desenvolvimento.
- **Loom ou OBS** — gravação do vídeo final.

## 12. Checkpoint semanal

Toda **sexta-feira ao final da etapa**, fazer:

1. Pull request da branch da semana para `main`.
2. Tag de versão (`v0.1`, `v0.2`, ...).
3. Atualização da coluna do GitHub Project.
4. Reunião curta entre a dupla (15 min) revisando o que foi feito e o que vem na próxima.
