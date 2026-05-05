# 02. Definição de Requisitos

> **Etapa da Pirâmide de Projeto:** Tradução das necessidades e objetivos em especificações técnicas — o que o sistema deve fazer.

## 1. Escopo do sistema

O sistema é um **WebService REST com camada de apresentação Blade** que permite a ONGs e voluntários se conectarem por meio de demandas categorizadas, com sugestão automática de match e ciclo completo de inscrição com avaliação mútua.

### Está dentro do escopo

- Cadastro e autenticação de usuários (ONG e voluntário) via tokens Sanctum.
- CRUD de demandas com três tipos (presencial, doação material, habilidade específica).
- Catálogo público filtrável de demandas abertas.
- Algoritmo de sugestão (match) por pontuação multi-critério.
- Inscrição com workflow de status (pendente → aceita → concluída/cancelada/recusada).
- Avaliação mútua após conclusão da participação.
- Histórico de participações por voluntário (dashboard).
- Geolocalização básica (latitude/longitude e cálculo de distância).
- Documentação interativa via Swagger UI.
- Páginas Blade para demonstração do padrão MVC tradicional.

### Está fora do escopo (pode ser evolução futura)

- Notificações por email ou push.
- Integração com redes sociais.
- Sistema de chat entre ONG e voluntário.
- Pagamentos online de doações monetárias.
- Aplicativo mobile nativo.
- Múltiplos idiomas.

## 2. Atores do sistema

| Ator | Descrição |
|------|-----------|
| **Visitante** | Usuário não autenticado; pode navegar pelo catálogo público de demandas. |
| **Voluntário** | Usuário autenticado com perfil de voluntário; pode se inscrever em demandas, receber sugestões e ser avaliado. |
| **ONG** | Usuário autenticado representando uma organização; pode publicar e gerenciar demandas, aceitar/recusar inscrições e avaliar voluntários. |
| **Administrador** | Papel reservado para gestão da plataforma — moderação e suporte (escopo mínimo nesta entrega). |

## 3. Requisitos funcionais (RF)

### 3.1 Autenticação e perfis

| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-01 | O sistema deve permitir o cadastro de usuários com perfil "voluntário" ou "ONG". | Alta |
| RF-02 | O sistema deve autenticar usuários por email + senha, retornando um token Sanctum. | Alta |
| RF-03 | O sistema deve permitir logout (revogação do token). | Alta |
| RF-04 | O voluntário deve poder editar seu perfil: nome, contato, endereço, latitude/longitude, categorias de interesse, habilidades, descrição. | Alta |
| RF-05 | A ONG deve poder editar seu perfil: razão social, CNPJ, endereço, latitude/longitude, telefone, descrição da missão. | Alta |

### 3.2 Categorias e habilidades

| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-06 | O sistema deve manter um cadastro de categorias (ex.: educação, saúde, ação social, ambiental). | Alta |
| RF-07 | O sistema deve permitir associar múltiplas categorias a um voluntário e a uma demanda. | Alta |
| RF-08 | O voluntário deve poder cadastrar habilidades em texto livre (ex.: "desenvolvimento web", "fisioterapia"). | Média |

### 3.3 Demandas

| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-09 | A ONG deve poder criar, editar, publicar, arquivar e excluir demandas. | Alta |
| RF-10 | Cada demanda deve ter: título, descrição, tipo (presencial / doação / habilidade), categorias, data de início, data limite, vagas, endereço, latitude/longitude, status. | Alta |
| RF-11 | O sistema deve disponibilizar um endpoint público para listar demandas com status "aberta". | Alta |
| RF-12 | O sistema deve permitir filtros no catálogo: por tipo, categoria, raio de km a partir de uma coordenada, e busca textual. | Alta |

### 3.4 Match (sugestão)

| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-13 | O sistema deve calcular um score de compatibilidade entre voluntário e demanda. | Alta |
| RF-14 | O score deve combinar: afinidade de categoria (peso 0.5), proximidade geográfica (peso 0.3) e avaliação média do voluntário (peso 0.2). | Alta |
| RF-15 | O voluntário autenticado deve ter um endpoint que retorne as N demandas mais compatíveis com seu perfil, ordenadas por score. | Alta |

### 3.5 Inscrições e workflow

| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-16 | O voluntário deve poder se inscrever em uma demanda aberta. | Alta |
| RF-17 | A inscrição deve nascer no status "pendente". | Alta |
| RF-18 | A ONG dona da demanda deve poder aceitar ou recusar inscrições pendentes. | Alta |
| RF-19 | Inscrições aceitas podem ser marcadas como "concluídas" ou "canceladas" pela ONG ou pelo voluntário. | Alta |
| RF-20 | O voluntário deve poder cancelar sua própria inscrição enquanto pendente ou aceita. | Alta |

### 3.6 Avaliação

| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-21 | Após uma inscrição ser marcada como "concluída", ONG e voluntário podem deixar uma avaliação um do outro (nota de 1 a 5 + comentário opcional). | Alta |
| RF-22 | A média das avaliações recebidas pelo voluntário deve ser calculável e usada como fator no algoritmo de match. | Alta |
| RF-23 | Avaliações são imutáveis após registro (não podem ser editadas pelo autor). | Média |

### 3.7 Dashboard e histórico

| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-24 | O voluntário autenticado deve ter acesso a um endpoint que lista todas as suas inscrições, separadas por status. | Alta |
| RF-25 | A ONG deve ter acesso a um endpoint que lista todas as suas demandas e respectivas inscrições. | Alta |
| RF-26 | O sistema deve agregar estatísticas básicas no dashboard: total de horas voluntariadas, número de participações concluídas, avaliação média recebida. | Média |

### 3.8 Apresentação web

| ID | Descrição | Prioridade |
|----|-----------|------------|
| RF-27 | O sistema deve servir páginas Blade para: home/catálogo público, login, dashboard do voluntário, dashboard da ONG, detalhes de demanda. | Média |
| RF-28 | O sistema deve disponibilizar Swagger UI em rota dedicada (`/api/documentation`). | Alta |

## 4. Requisitos não-funcionais (RNF)

| ID | Categoria | Descrição |
|----|-----------|-----------|
| RNF-01 | **Arquitetura** | Seguir o padrão MVC do framework Laravel. |
| RNF-02 | **Arquitetura** | A API deve seguir a metodologia REST com uso correto dos verbos HTTP e códigos de status. |
| RNF-03 | **Persistência** | Usar MySQL como SGBD e Eloquent ORM como camada de abstração. |
| RNF-04 | **Segurança** | Senhas armazenadas com hash bcrypt (padrão Laravel). |
| RNF-05 | **Segurança** | Endpoints sensíveis protegidos por middleware `auth:sanctum`. |
| RNF-06 | **Segurança** | Validação de entrada em todos os endpoints via FormRequest. |
| RNF-07 | **Manutenibilidade** | Código aderente ao PSR-12 e às convenções do Laravel. |
| RNF-08 | **Documentação** | Toda rota pública da API documentada via anotações OpenAPI (L5-Swagger). |
| RNF-09 | **Testabilidade** | A API deve ser testável de ponta a ponta via Postman/Insomnia/Swagger. |
| RNF-10 | **Versionamento** | Código versionado em repositório Git público no GitHub, com commits descritivos. |
| RNF-11 | **Portabilidade** | A aplicação deve rodar em ambiente PHP 8.2+ com MySQL 8 (Laragon ou XAMPP). |
| RNF-12 | **Internacionalização** | Mensagens de erro e validação em português brasileiro. |

## 5. Regras de negócio (RN)

| ID | Regra |
|----|-------|
| RN-01 | Um usuário só pode ter um perfil ativo (ou voluntário, ou ONG — não ambos). |
| RN-02 | Uma demanda só pode receber inscrições enquanto seu status for "aberta". |
| RN-03 | Um voluntário não pode se inscrever duas vezes na mesma demanda. |
| RN-04 | Uma demanda fecha automaticamente quando o número de inscrições aceitas iguala o número de vagas. |
| RN-05 | Avaliação só é possível em inscrições com status "concluída". |
| RN-06 | Uma inscrição cancelada não pode ser reativada — o voluntário precisa se inscrever novamente, gerando novo registro. |
| RN-07 | Apenas a ONG dona da demanda pode aceitar, recusar ou marcar como concluída uma inscrição daquela demanda. |
| RN-08 | A média de avaliação só passa a contar no algoritmo de match após o voluntário ter no mínimo 3 avaliações. |
| RN-09 | O score de match só é calculado para voluntários com latitude/longitude e ao menos uma categoria de interesse cadastrada. |
| RN-10 | Demandas com data limite vencida têm status alterado automaticamente para "encerrada". |

## 6. Histórias de usuário (resumo)

- **US-01.** Como **voluntário**, quero criar meu perfil com habilidades e localização para receber sugestões compatíveis.
- **US-02.** Como **voluntário**, quero ver demandas próximas à minha cidade para ajudar localmente.
- **US-03.** Como **voluntário**, quero ver meu histórico de participações para comprovar horas extensionistas.
- **US-04.** Como **ONG**, quero publicar uma demanda urgente em poucos minutos para conseguir voluntários a tempo.
- **US-05.** Como **ONG**, quero ver as inscrições recebidas e decidir quem aceitar para organizar a equipe da ação.
- **US-06.** Como **ONG**, quero avaliar voluntários após o evento para construir um histórico de confiabilidade.
- **US-07.** Como **visitante**, quero navegar o catálogo de demandas sem login para conhecer ONGs antes de me cadastrar.

## 7. Restrições do projeto

- **Tecnologia obrigatória:** Laravel (Web 2). Pode opcionalmente usar Sail/Docker, mas a documentação assume Laragon/XAMPP.
- **Prazo:** entrega final em 23/06/2026.
- **Equipe:** dois desenvolvedores.
- **Avaliação:** o backend é o principal item avaliado pela disciplina de POO. Frontend não é avaliado por POO, mas tem peso na Web 2 via demonstração do MVC com Blade.
- **Dados:** uso de cenários e dados de seed fictícios — nenhum dado pessoal real será coletado durante o desenvolvimento.

## 8. Critérios de aceitação da entrega

A entrega final será considerada completa quando:

1. Todos os RF de prioridade Alta estiverem implementados e testáveis via Swagger.
2. O repositório no GitHub contiver o código completo, este conjunto de documentos, e instruções de instalação que permitam um terceiro rodar a aplicação localmente em até 30 minutos.
3. O banco vier populado por seeds com pelo menos 5 ONGs, 20 voluntários, 15 demandas e algumas inscrições concluídas com avaliações.
4. Houver um vídeo de até 3 minutos demonstrando o sistema em funcionamento.
5. As rotas da API estiverem devidamente documentadas em Swagger UI.
6. Existir ao menos uma página Blade funcional para cada um dos cenários de uso principais (catálogo, login, dashboard).
