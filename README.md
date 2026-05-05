# Match Voluntários ↔ ONGs

Sistema web de conexão entre ONGs do Alto Vale do Itajaí e voluntários da comunidade acadêmica e regional, com catálogo aberto de demandas e algoritmo de sugestão automática baseado em pontuação multi-critério.

## Sobre o projeto

Este é um projeto de extensão desenvolvido para as disciplinas de **Programação Orientada a Objeto** e **Programação Web 2** do curso de Bacharelado em Sistemas de Informação da Unidavi, vinculado ao programa de extensão **Universidade Solidária**.

A solução foi pensada para reduzir a distância entre ONGs que precisam de apoio (voluntariado presencial, doações materiais e habilidades específicas) e pessoas dispostas a ajudar, automatizando a descoberta de oportunidades compatíveis com o perfil de cada voluntário.

## Disciplinas atendidas

| Disciplina | Requisitos cobertos |
|------------|---------------------|
| Programação Orientada a Objeto | WebService REST, 4+ entidades, controllers, CRUD com persistência, testável via Swagger/Postman |
| Programação Web 2 | MVC com Laravel, MySQL + Eloquent, tema social (extensão), Blade views, levantamento de requisitos |

## Stack tecnológica

- **Linguagem:** PHP 8.2+
- **Framework:** Laravel 11
- **Banco de dados:** MySQL 8
- **Autenticação:** Laravel Sanctum (tokens)
- **Documentação da API:** L5-Swagger (OpenAPI)
- **Camada de apresentação:** Blade (server-side) + Swagger UI
- **Ambiente de desenvolvimento:** Laragon ou XAMPP
- **Versionamento:** Git + GitHub

## Estrutura do repositório

```
match-voluntarios-ongs/
├── documentos/                     <- documentação acadêmica e técnica
│   ├── 01-levantamento-proposito.md
│   ├── 02-requisitos.md
│   ├── 03-arquitetura-design.md
│   ├── 04-modelo-dados.md
│   ├── 05-plano-desenvolvimento.md
│   └── README.md
└── (código Laravel será adicionado nas etapas seguintes)
```

## Equipe

Trabalho desenvolvido em dupla.

- Desenvolvedor 1: _a definir_
- Desenvolvedor 2: _a definir_

## Cronograma

- **Início:** 05/05/2026
- **Entrega final:** 23/06/2026
- **Duração:** 7 semanas / 7 etapas de desenvolvimento

Veja `05-plano-desenvolvimento.md` para o detalhamento de cada etapa.

## Como navegar pela documentação

Recomenda-se a leitura na ordem numerada — cada documento é base para o próximo, seguindo a metodologia da **Pirâmide de Projeto** apresentada no guia da disciplina:

1. **Levantamento do Propósito** — quem, por quê e para quê
2. **Requisitos** — o que o sistema deve fazer
3. **Arquitetura e Design** — como o sistema será estruturado
4. **Modelo de Dados** — como a informação será organizada
5. **Plano de Desenvolvimento** — como vamos construir, etapa por etapa
