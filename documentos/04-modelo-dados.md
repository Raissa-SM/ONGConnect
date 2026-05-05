# 04. Modelo de Dados

> **Complementa:** `03-arquitetura-design.md`. Este documento detalha a estrutura de persistência e os endpoints REST.

## 1. Visão geral

O modelo é composto por **7 entidades** principais (excede o mínimo de 4 exigido pela disciplina de POO), com relacionamentos predominantemente um-para-muitos e uma relação muitos-para-muitos pivotada (categorias).

```
                    +--------+
                    |  User  |
                    +---+----+
                        |
              +---------+---------+
              |                   |
              v                   v
        +----------+        +-----------+
        |   ONG    |        | Voluntario|
        +-----+----+        +-----+-----+
              |                   |
              | publica           | se inscreve
              v                   |
        +-----------+             |
        | Demanda   |<------------+
        +-----+-----+    via Inscricao
              |
              | tem várias
              v
        +-----------+
        | Categoria |   (relação N:M com Demanda e com Voluntario)
        +-----------+

        +-----------+
        | Inscricao | -- ligação Voluntario <-> Demanda + status
        +-----+-----+
              |
              | gera
              v
        +-----------+
        | Avaliacao |
        +-----------+
```

## 2. Dicionário de entidades

### 2.1 `users`

Tabela base de autenticação (Sanctum). Todo usuário do sistema é um `User`.

| Coluna | Tipo | Notas |
|--------|------|-------|
| id | bigint PK | |
| name | string | Nome de exibição |
| email | string unique | Login |
| password | string | Hash bcrypt |
| tipo_perfil | enum('ong','voluntario') | Determina qual perfil está vinculado |
| email_verified_at | timestamp nullable | Padrão Laravel |
| remember_token | string nullable | Padrão Laravel |
| created_at, updated_at | timestamps | |

Relacionamentos:

- `hasOne` ONG (se `tipo_perfil = 'ong'`)
- `hasOne` Voluntario (se `tipo_perfil = 'voluntario'`)

### 2.2 `ongs`

Perfil estendido de uma organização.

| Coluna | Tipo | Notas |
|--------|------|-------|
| id | bigint PK | |
| user_id | bigint FK → users | unique |
| razao_social | string | |
| cnpj | string(14) unique | |
| telefone | string | |
| descricao_missao | text | |
| endereco | string | |
| cidade | string | |
| uf | string(2) | |
| latitude | decimal(10,7) | |
| longitude | decimal(10,7) | |
| created_at, updated_at | timestamps | |

Relacionamentos:

- `belongsTo` User
- `hasMany` Demanda

### 2.3 `voluntarios`

Perfil estendido de um voluntário.

| Coluna | Tipo | Notas |
|--------|------|-------|
| id | bigint PK | |
| user_id | bigint FK → users | unique |
| cpf | string(11) unique nullable | |
| telefone | string nullable | |
| descricao | text nullable | Bio curta |
| habilidades | json nullable | Array de strings: `["desenvolvimento web", "fisioterapia"]` |
| disponibilidade | json nullable | Ex.: `["sabado_manha", "domingo_tarde"]` |
| endereco | string nullable | |
| cidade | string nullable | |
| uf | string(2) nullable | |
| latitude | decimal(10,7) nullable | |
| longitude | decimal(10,7) nullable | |
| created_at, updated_at | timestamps | |

Relacionamentos:

- `belongsTo` User
- `belongsToMany` Categoria (via `categoria_voluntario`)
- `hasMany` Inscricao
- `hasMany` Avaliacao (avaliações recebidas)

### 2.4 `categorias`

Áreas de interesse padronizadas.

| Coluna | Tipo | Notas |
|--------|------|-------|
| id | bigint PK | |
| nome | string unique | Ex.: "Educação", "Saúde", "Ambiental" |
| slug | string unique | |
| descricao | text nullable | |
| created_at, updated_at | timestamps | |

Relacionamentos:

- `belongsToMany` Voluntario (via `categoria_voluntario`)
- `belongsToMany` Demanda (via `categoria_demanda`)

### 2.5 `demandas`

A oportunidade publicada pela ONG.

| Coluna | Tipo | Notas |
|--------|------|-------|
| id | bigint PK | |
| ong_id | bigint FK → ongs | |
| titulo | string | |
| descricao | text | |
| tipo | enum('presencial','doacao','habilidade') | |
| status | enum('rascunho','aberta','encerrada','arquivada') | default 'rascunho' |
| data_inicio | date nullable | |
| data_limite | date nullable | |
| vagas | int | default 1 |
| endereco | string nullable | |
| cidade | string nullable | |
| uf | string(2) nullable | |
| latitude | decimal(10,7) nullable | |
| longitude | decimal(10,7) nullable | |
| created_at, updated_at | timestamps | |

Relacionamentos:

- `belongsTo` ONG
- `belongsToMany` Categoria (via `categoria_demanda`)
- `hasMany` Inscricao

### 2.6 `inscricoes`

Liga voluntário e demanda; carrega o status do workflow.

| Coluna | Tipo | Notas |
|--------|------|-------|
| id | bigint PK | |
| voluntario_id | bigint FK → voluntarios | |
| demanda_id | bigint FK → demandas | |
| status | enum('pendente','aceita','recusada','concluida','cancelada') | default 'pendente' |
| mensagem | text nullable | Mensagem opcional do voluntário no momento da inscrição |
| respondida_em | timestamp nullable | Quando ONG aceitou/recusou |
| concluida_em | timestamp nullable | Quando foi marcada como concluída |
| created_at, updated_at | timestamps | |

Relacionamentos:

- `belongsTo` Voluntario
- `belongsTo` Demanda
- `hasOne` Avaliacao para `avaliador = voluntario`
- `hasOne` Avaliacao para `avaliador = ong`

Constraint:

- Unique composto em (`voluntario_id`, `demanda_id`) — evita inscrição duplicada (RN-03).

### 2.7 `avaliacoes`

Avaliação mútua após conclusão de uma inscrição.

| Coluna | Tipo | Notas |
|--------|------|-------|
| id | bigint PK | |
| inscricao_id | bigint FK → inscricoes | |
| autor_tipo | enum('ong','voluntario') | Quem está avaliando |
| nota | tinyint | 1 a 5 |
| comentario | text nullable | |
| created_at, updated_at | timestamps | |

Constraint:

- Unique composto em (`inscricao_id`, `autor_tipo`) — cada lado avalia uma única vez.

## 3. Tabelas pivot (relacionamentos N:M)

### `categoria_voluntario`
- `voluntario_id` FK
- `categoria_id` FK
- PK composta

### `categoria_demanda`
- `demanda_id` FK
- `categoria_id` FK
- PK composta

## 4. Endpoints REST

Convenção: prefixo `/api`. Endpoints autenticados marcados com 🔒.

### Autenticação

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/auth/registro` | Cria User + perfil ONG ou Voluntário |
| POST | `/api/auth/login` | Retorna Bearer token |
| POST | `/api/auth/logout` | 🔒 Revoga token atual |
| GET | `/api/auth/eu` | 🔒 Retorna o usuário autenticado com perfil |

### Categorias

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/categorias` | Lista todas (público) |
| GET | `/api/categorias/{id}` | Detalhe (público) |
| POST | `/api/categorias` | 🔒 admin (escopo mínimo: aberto a qualquer usuário autenticado nesta entrega) |
| PUT | `/api/categorias/{id}` | 🔒 |
| DELETE | `/api/categorias/{id}` | 🔒 |

### ONGs

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/ongs` | Lista (público) |
| GET | `/api/ongs/{id}` | Detalhe (público) |
| PUT | `/api/ongs/{id}` | 🔒 (somente a própria ONG) |

### Voluntários

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/voluntarios/{id}` | Detalhe (público — perfil sem dados sensíveis) |
| PUT | `/api/voluntarios/{id}` | 🔒 (somente o próprio) |
| POST | `/api/voluntarios/{id}/categorias` | 🔒 Atualiza categorias de interesse (sync) |

### Demandas

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/demandas` | Lista pública filtrável (`?tipo=`, `?categoria=`, `?lat=&lng=&raio=`, `?q=`) |
| GET | `/api/demandas/{id}` | Detalhe (público) |
| POST | `/api/demandas` | 🔒 ONG cria |
| PUT | `/api/demandas/{id}` | 🔒 ONG dona |
| DELETE | `/api/demandas/{id}` | 🔒 ONG dona |
| POST | `/api/demandas/{id}/publicar` | 🔒 muda status `rascunho` → `aberta` |
| POST | `/api/demandas/{id}/encerrar` | 🔒 muda status para `encerrada` |

### Inscrições

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/inscricoes/minhas` | 🔒 Voluntário lista as próprias |
| GET | `/api/demandas/{id}/inscricoes` | 🔒 ONG lista inscrições da demanda |
| POST | `/api/demandas/{id}/inscricoes` | 🔒 Voluntário se inscreve |
| POST | `/api/inscricoes/{id}/aceitar` | 🔒 ONG dona aceita |
| POST | `/api/inscricoes/{id}/recusar` | 🔒 ONG dona recusa |
| POST | `/api/inscricoes/{id}/concluir` | 🔒 ONG ou voluntário marca como concluída |
| POST | `/api/inscricoes/{id}/cancelar` | 🔒 voluntário cancela |

### Avaliações

| Método | Rota | Descrição |
|--------|------|-----------|
| POST | `/api/inscricoes/{id}/avaliacoes` | 🔒 cria avaliação (autor é o usuário autenticado) |
| GET | `/api/voluntarios/{id}/avaliacoes` | Avaliações recebidas pelo voluntário (público) |
| GET | `/api/ongs/{id}/avaliacoes` | Avaliações recebidas pela ONG (público) |

### Match (sugestões)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/match/sugestoes` | 🔒 Retorna as N demandas mais compatíveis para o voluntário autenticado |
| GET | `/api/match/score?demanda_id=X` | 🔒 Calcula score voluntário autenticado ↔ demanda específica |

### Dashboard

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/dashboard/voluntario` | 🔒 Estatísticas do voluntário autenticado |
| GET | `/api/dashboard/ong` | 🔒 Estatísticas da ONG autenticada |

## 5. Códigos HTTP padronizados

| Situação | Código |
|----------|--------|
| Sucesso em listagem ou consulta | 200 OK |
| Sucesso em criação | 201 Created |
| Sucesso em ação sem retorno (ex.: aceitar inscrição) | 204 No Content |
| Token ausente ou inválido | 401 Unauthorized |
| Usuário autenticado mas sem permissão | 403 Forbidden |
| Recurso não encontrado | 404 Not Found |
| Conflito de regra de negócio (ex.: inscrição duplicada) | 409 Conflict |
| Erro de validação de payload | 422 Unprocessable Entity |
| Erro inesperado no servidor | 500 Internal Server Error |

## 6. Seeders previstos

Para garantir que a aplicação seja avaliada com dados representativos:

- **CategoriaSeeder:** ~10 categorias fixas (Educação, Saúde, Ambiental, Ação Social, Tecnologia, Cultura, Esporte, Animal, Idosos, Crianças).
- **ONGSeeder:** 5 ONGs fictícias com endereços reais do Alto Vale (Rio do Sul, Ituporanga, Taió, Trombudo Central, Rio do Oeste).
- **VoluntarioSeeder:** 20 voluntários com habilidades e localizações distribuídas.
- **DemandaSeeder:** 15 demandas variando os 3 tipos e os 4 status possíveis.
- **InscricaoSeeder:** ~30 inscrições, distribuídas pelos 5 status.
- **AvaliacaoSeeder:** avaliações para todas as inscrições com status `concluida`.

Os seeders devem ser idempotentes e rodar com `php artisan db:seed`.
