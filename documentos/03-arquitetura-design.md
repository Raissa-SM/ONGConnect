# 03. Arquitetura e Design

> **Etapa da Pirâmide de Projeto:** Projeto lógico (estrutura, navegação, componentes) e projeto físico (interface, layout, lógica de servidor).

## 1. Visão geral da arquitetura

O sistema segue uma arquitetura **monolítica modular** baseada em Laravel, dividida nas camadas clássicas do padrão MVC, exposta tanto como API REST (consumível por qualquer cliente HTTP) quanto como aplicação web tradicional via Blade.

```
+-------------------------------------------------------+
|                    Cliente (HTTP)                     |
|   Browser  |  Swagger UI  |  Postman  |  App futuro   |
+-----------------------+-------------------------------+
                        |
                        | HTTP/JSON ou HTTP/HTML
                        v
+-------------------------------------------------------+
|             Camada de roteamento (Laravel)            |
|   routes/api.php (REST)   |   routes/web.php (Blade)  |
+-----------------------+-------------------------------+
                        |
                        v
+-------------------------------------------------------+
|                  Middleware Sanctum                   |
|   Validação de token, throttle, CORS, CSRF (web)      |
+-----------------------+-------------------------------+
                        |
                        v
+-------------------------------------------------------+
|                    Controllers                        |
|   Recebe request, valida (FormRequest), delega        |
+-----------------------+-------------------------------+
                        |
                        v
+-------------------------------------------------------+
|             Services / Domain Logic                   |
|   MatchService, InscricaoService, AvaliacaoService    |
+-----------------------+-------------------------------+
                        |
                        v
+-------------------------------------------------------+
|              Models (Eloquent ORM)                    |
|   User, ONG, Voluntario, Categoria, Demanda,          |
|   Inscricao, Avaliacao                                |
+-----------------------+-------------------------------+
                        |
                        v
+-------------------------------------------------------+
|                   MySQL 8 Database                    |
+-------------------------------------------------------+
```

## 2. Projeto lógico

### 2.1 Camadas da aplicação

| Camada | Responsabilidade | Componentes Laravel |
|--------|------------------|---------------------|
| **Apresentação** | Interface com o usuário (HTML/JSON) | Blade views, Resources, Swagger UI |
| **Roteamento** | Mapear URLs para handlers | `routes/api.php`, `routes/web.php` |
| **Middleware** | Cross-cutting concerns (auth, throttle, validação inicial) | Sanctum, custom middleware |
| **Controle** | Orquestrar request/response, validar entrada, formatar saída | Controllers, FormRequests |
| **Domínio** | Regras de negócio que transcendem um único modelo | Services (`app/Services/`) |
| **Persistência** | Mapeamento objeto-relacional, queries | Eloquent Models, Migrations, Seeders |
| **Infraestrutura** | Banco de dados, cache, filas | MySQL, config files |

### 2.2 Padrão MVC no Laravel

```
[ Request ] -> [ Route ] -> [ Middleware ] -> [ Controller ]
                                                    |
                                  +-----------------+-----------------+
                                  |                                   |
                                  v                                   v
                          [ FormRequest ]                       [ Service ]
                          (validação)                           (regra negócio)
                                                                      |
                                                                      v
                                                              [ Model (Eloquent) ]
                                                                      |
                                                                      v
                                                              [ Database (MySQL) ]
                                                                      |
                                                                      v
                                                              [ Resource / View ]
                                                                      |
                                                                      v
                                                                [ Response ]
```

**Observação importante sobre o MVC tradicional vs API REST:**

- Em uma rota web (`routes/web.php`), o Controller retorna uma **View Blade** com dados — esse é o MVC clássico que será apresentado nas telas de demonstração.
- Em uma rota de API (`routes/api.php`), o Controller retorna um **Resource (JSON)** — esse é o "View" da arquitetura REST.

Ambos compartilham os mesmos Models e Services, garantindo consistência de regra de negócio.

### 2.3 Estrutura de navegação (sitemap das views Blade)

```
[ / ]  Home / Catálogo público
   |
   +-- [ /demandas/{id} ]            Detalhe de uma demanda (público)
   |
   +-- [ /login ]                    Tela de login
   |       |
   |       +-- [ /registro ]         Cadastro (escolhe perfil ONG ou voluntário)
   |
   +-- [ /dashboard/voluntario ]     (auth)
   |       |
   |       +-- [ /minhas-inscricoes ]    Histórico
   |       +-- [ /perfil/voluntario ]    Editar perfil
   |       +-- [ /sugestoes ]            Demandas sugeridas (match)
   |
   +-- [ /dashboard/ong ]            (auth)
   |       |
   |       +-- [ /minhas-demandas ]      Lista e gerenciamento
   |       +-- [ /demandas/nova ]        Formulário de criação
   |       +-- [ /demandas/{id}/inscricoes ]  Gerenciar inscritos
   |       +-- [ /perfil/ong ]           Editar perfil
   |
   +-- [ /api/documentation ]        Swagger UI (público)
```

### 2.4 Arquitetura de componentes

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/                    <- controllers REST (JSON)
│   │   │   ├── AuthController.php
│   │   │   ├── ONGController.php
│   │   │   ├── VoluntarioController.php
│   │   │   ├── CategoriaController.php
│   │   │   ├── DemandaController.php
│   │   │   ├── InscricaoController.php
│   │   │   ├── AvaliacaoController.php
│   │   │   └── MatchController.php
│   │   └── Web/                    <- controllers Blade (HTML)
│   │       ├── HomeController.php
│   │       ├── DashboardController.php
│   │       └── DemandaWebController.php
│   ├── Requests/                   <- FormRequests (validação)
│   ├── Resources/                  <- API Resources (formatação JSON)
│   └── Middleware/
├── Models/                         <- Eloquent Models
├── Services/                       <- regras de negócio
│   ├── MatchService.php
│   ├── InscricaoWorkflowService.php
│   └── AvaliacaoService.php
└── Providers/

routes/
├── api.php
└── web.php

resources/views/                    <- Blade templates
├── layouts/
├── home/
├── dashboard/
└── demandas/

database/
├── migrations/
├── seeders/
└── factories/
```

### 2.5 Fluxo request/response — exemplo: voluntário se inscreve em uma demanda

```
1. POST /api/demandas/{id}/inscricoes
   Header: Authorization: Bearer {token}

2. Middleware auth:sanctum valida o token e injeta o User autenticado.

3. InscricaoController@store recebe a request.

4. CreateInscricaoRequest valida payload (regras: demanda existe, voluntário
   tem perfil completo, não há inscrição duplicada — RN-03).

5. InscricaoController delega para InscricaoWorkflowService->inscrever(...).

6. Service verifica:
      - status da demanda == "aberta"  (RN-02)
      - vagas disponíveis             (consulta agregada)
   E chama Inscricao::create(['status' => 'pendente', ...])  (RF-17)

7. Eloquent persiste no MySQL.

8. Service retorna o model criado.

9. Controller embrulha em InscricaoResource (formatação JSON).

10. Laravel devolve HTTP 201 Created com o JSON.
```

## 3. Projeto físico

### 3.1 Stack tecnológica detalhada

| Componente | Tecnologia | Versão alvo | Justificativa |
|------------|-----------|-------------|---------------|
| Linguagem servidor | PHP | 8.2+ | Recursos modernos (enums, readonly, named args) |
| Framework | Laravel | 11.x | Padrão da disciplina, MVC robusto, ecossistema rico |
| Banco de dados | MySQL | 8.x | Relacional, suporte a JSON, padrão do Laragon/XAMPP |
| ORM | Eloquent | (Laravel) | Camada de abstração expressiva exigida pelo guia |
| Autenticação | Laravel Sanctum | 4.x | Tokens leves, ideal para API REST + SPA opcional |
| Documentação API | DarkaOnLine/L5-Swagger | 8.x | Geração de OpenAPI 3 a partir de annotations PHP |
| Template engine | Blade | (Laravel) | Camada de visão padrão do Laravel |
| Frontend (Blade) | Tailwind CSS via CDN | 3.x | Estilização rápida sem build pipeline |
| Versionamento | Git + GitHub | — | Exigência do guia (entrega via link GitHub) |
| Ambiente local | Laragon ou XAMPP | recente | PHP + Apache/Nginx + MySQL integrados |

### 3.2 Estrutura de pastas (raiz do projeto)

```
match-voluntarios-ongs/
├── app/                <- código da aplicação Laravel
├── bootstrap/
├── config/
├── database/           <- migrations, seeders, factories
├── documentos/         <- esta pasta de documentação
├── public/             <- ponto de entrada HTTP (index.php)
├── resources/          <- views Blade, assets crus
├── routes/             <- definição de rotas
├── storage/            <- logs, cache, uploads
├── tests/              <- testes (opcional para esta entrega)
├── vendor/             <- dependências (gitignore)
├── .env.example        <- template de configuração
├── composer.json
├── package.json
└── README.md
```

### 3.3 Padrões de código

- **PSR-12** para estilo de código.
- **Convenções de nomenclatura Laravel:**
  - Models no singular (`Demanda`, `Voluntario`).
  - Tabelas no plural snake_case (`demandas`, `voluntarios`).
  - Controllers no padrão `RecursoController` (`DemandaController`).
  - FormRequests no padrão `Acao + Recurso + Request` (`StoreDemandaRequest`).
  - Resources no padrão `Recurso + Resource` (`DemandaResource`).
- **Idioma do código:** nomes de classes, métodos e variáveis em **português** (alinhamento com domínio e com o público leitor da documentação acadêmica). Termos técnicos universais permanecem em inglês (`controller`, `migration`, `service`).
- **Anotações OpenAPI** em todos os métodos de controllers de API.
- **Comentários** apenas onde a intenção não é óbvia pelo código.

### 3.4 Layout das views Blade

A interface adota uma identidade visual coerente baseada em uma paleta sóbria de extensão universitária:

- **Cor primária:** azul institucional (`#1B4F8E`)
- **Cor de destaque:** verde solidariedade (`#3BAA75`)
- **Tipografia:** sans-serif do sistema operacional (sem dependências externas)
- **Componentes recorrentes:** card de demanda, badge de tipo, badge de status, lista paginada, formulário em duas colunas, breadcrumb.

Todos os templates herdam de `resources/views/layouts/app.blade.php`, que define cabeçalho, rodapé e estrutura de navegação consistentes.

### 3.5 Documentação da API (Swagger / OpenAPI)

A documentação interativa estará disponível em `/api/documentation` e será gerada automaticamente a partir de anotações nos controllers usando o pacote `darkaonline/l5-swagger`. Cada endpoint documenta:

- Caminho, método HTTP e parâmetros.
- Esquema do payload de entrada (FormRequest).
- Esquemas de resposta (200, 201, 401, 403, 404, 422, 500).
- Exemplos de payload.
- Tags de agrupamento (Auth, Demandas, Inscrições, Match, Avaliações).
- Indicação de quais endpoints exigem autenticação Bearer.

## 4. Segurança

| Aspecto | Tratamento |
|---------|-----------|
| Autenticação | Tokens Sanctum, expiráveis, revogáveis individualmente. |
| Autorização | Policies do Laravel para restringir ações por papel (ONG só altera suas próprias demandas — RN-07). |
| Senhas | Hash bcrypt automático via `Hash::make()`. |
| Validação | FormRequests obrigatórias em todos os endpoints de escrita. |
| SQL Injection | Mitigado pelo Eloquent (queries parametrizadas). |
| XSS (Blade) | Escape automático com `{{ }}`. |
| CSRF (rotas web) | Tokens CSRF automáticos do Laravel em formulários Blade. |
| Throttle | Rate limiter padrão do Laravel nas rotas de API (60 req/min). |
| Dados sensíveis | Senhas e tokens nunca aparecem em logs ou em respostas de API. |

## 5. Algoritmo de match — detalhamento técnico

O `MatchService::calcularScore(Voluntario $v, Demanda $d)` retorna um número entre 0 e 1, calculado como:

```
score = 0.5 * afinidade_categoria
      + 0.3 * proximidade_geografica
      + 0.2 * fator_avaliacao
```

Onde:

- `afinidade_categoria` = (categorias em comum entre voluntário e demanda) / (categorias da demanda). Resulta em valor entre 0 e 1.
- `proximidade_geografica` = `max(0, 1 - distancia_km / raio_max)`, com `raio_max = 50 km` (configurável). Distância calculada pela fórmula de Haversine.
- `fator_avaliacao` = (média_avaliacoes_recebidas - 1) / 4, mapeando o intervalo [1, 5] para [0, 1]. Aplicado apenas se o voluntário tem ≥ 3 avaliações (RN-08); caso contrário, vale 0.5 (neutro).

Os pesos (0.5 / 0.3 / 0.2) são fixos nesta entrega, mas centralizados em uma constante para facilitar evolução futura para a versão "configurável pela ONG".

## 6. Integrações externas

Esta entrega não depende de integrações externas em produção. Em ambiente de desenvolvimento, **opcionalmente**, pode ser usado:

- **OpenStreetMap Nominatim** (gratuito, sem chave) para geocodificação de endereços durante o seed — converter endereço textual em latitude/longitude. Implementação opcional; o seeder pode trabalhar com coordenadas fixas pré-definidas para ONGs e voluntários fictícios.
