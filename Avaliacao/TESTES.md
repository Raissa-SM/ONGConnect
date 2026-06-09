# ONGConnect — Checklist de Testes

**Como rodar os testes automatizados:**
```bash
# A partir de ONGConnect/
bash tests/smoke.sh
# ou com URL diferente:
bash tests/smoke.sh http://localhost:8000
```

---

## 1. Autenticação

### Registro
- [ ] Registrar como **Voluntário** (campos: nome, email, senha, CPF)
- [ ] Registrar como **ONG** (campos: nome, email, senha, razão social, CNPJ)
- [ ] Registrar com e-mail já existente → erro "e-mail já cadastrado"
- [ ] Registrar com senha < 8 caracteres → erro de validação
- [ ] Registrar sem preencher campos obrigatórios → erros inline no formulário
- [ ] Após registro de voluntário → redireciona para `/dashboard`
- [ ] Após registro de ONG → redireciona para `/dashboard/ong`

### Login
- [ ] Login com credenciais corretas (voluntário) → dashboard voluntário
- [ ] Login com credenciais corretas (ONG) → dashboard ONG
- [ ] Login com senha errada → mensagem "E-mail ou senha incorretos"
- [ ] Login com e-mail inexistente → mesma mensagem (não revelar se e-mail existe)
- [ ] Logout → redireciona para home, navbar mostra "Entrar"

### Proteção de rotas
- [ ] Acessar `/dashboard` sem login → redireciona para `/login`
- [ ] Acessar `/match` sem login → redireciona para `/login`
- [ ] Voluntário tentando acessar `/dashboard/ong` → redireciona (middleware `ong`)
- [ ] ONG tentando acessar `/dashboard` (voluntário) → redireciona (middleware `voluntario`)

---

## 2. Home Page

- [ ] Página carrega com stats (voluntários, ONGs, demandas abertas)
- [ ] Seção "Como funciona" com 3 passos visíveis
- [ ] Cards de demandas em destaque (máx 6)
- [ ] Botão "Quero ser voluntário" visível para visitantes
- [ ] Botão "Ver minhas sugestões" para voluntário logado
- [ ] Botão "Criar demanda" para ONG logada
- [ ] Seção CTA azul visível apenas para visitantes

---

## 3. Demandas (público)

### Listagem
- [ ] `/demandas` lista apenas demandas com status `aberta`
- [ ] Filtro por texto (q) funciona (busca em título e descrição)
- [ ] Filtro por cidade funciona
- [ ] Filtro por tipo (presencial / doação / habilidade) funciona
- [ ] Filtro por categoria funciona
- [ ] Paginação funciona (12 por página)
- [ ] Combinar múltiplos filtros funciona

### Detalhe
- [ ] `/demandas/{id}` exibe título, descrição, tipo, datas, vagas, categorias, ONG
- [ ] Badge de tipo com cor correta (azul=presencial, âmbar=doação, roxo=habilidade)
- [ ] Formulário de inscrição visível para voluntário logado
- [ ] Para visitante: link "Faça login para se inscrever"
- [ ] Para ONG logada: sem formulário de inscrição
- [ ] Se voluntário já inscrito: mostra status da inscrição (não o formulário)
- [ ] `/demandas/9999` retorna página 404

---

## 4. ONGs (público)

- [ ] `/ongs` lista todas as ONGs com contador de demandas abertas
- [ ] `/ongs/{id}` exibe perfil completo + demandas abertas da ONG
- [ ] `/ongs/9999` retorna 404

---

## 5. Dashboard — Voluntário

- [ ] 4 cards de stats: total inscrições, pendentes, aceitas, concluídas
- [ ] Seção "Próximas atividades" (inscrições aceitas com data futura)
- [ ] Seção "Últimas inscrições" (últimas 5)
- [ ] CTA de match se perfil incompleto (sem localização ou categorias)
- [ ] Sem CTA de match se perfil completo

---

## 6. Dashboard — ONG

- [ ] 4 cards: demandas abertas, pendentes, inscrições pendentes, avaliação média
- [ ] Lista de inscrições pendentes (máx 5) com botões Aceitar/Recusar inline
- [ ] Lista de demandas abertas com vagas disponíveis
- [ ] Aceitar inscrição direto do dashboard → atualiza status

---

## 7. Perfil — Voluntário

- [ ] Formulário pré-preenchido com dados atuais
- [ ] Atualizar nome, CPF, telefone, cidade, UF, descrição → salva com sucesso
- [ ] Atualizar latitude/longitude → persiste (importante para o match)
- [ ] Selecionar/desselecionar categorias → persiste
- [ ] Toast de sucesso após salvar
- [ ] Validação: latitude fora de [-90, 90] → erro
- [ ] Validação: longitude fora de [-180, 180] → erro

---

## 8. Perfil — ONG

- [ ] Formulário pré-preenchido com dados atuais
- [ ] Atualizar razão social, CNPJ, telefone, cidade, UF, endereço → salva
- [ ] Campo "Missão/Descrição" (textarea longo) → salva
- [ ] Campo website com URL válida → salva; URL inválida → erro
- [ ] Atualizar latitude/longitude → persiste

---

## 9. Gerenciar Demandas (ONG)

### Listagem
- [ ] `/minhas-demandas` mostra apenas demandas da ONG logada
- [ ] Badge de status correto (rascunho=cinza, aberta=verde, encerrada=vermelho)
- [ ] Contador de inscrições visível por demanda

### Criar
- [ ] Criar com todos os campos obrigatórios → rascunho criado
- [ ] Criar sem título → erro de validação
- [ ] Criar sem tipo → erro de validação
- [ ] Campo vagas vazio (ilimitado) → aceita sem erro
- [ ] Criar com categorias selecionadas → salva corretamente
- [ ] Data limite anterior à data início → erro de validação

### Editar
- [ ] Editar demanda própria → salva alterações
- [ ] Tentar editar demanda de outra ONG → 403

### Status
- [ ] Publicar rascunho → status muda para `aberta`
- [ ] Publicar demanda que já está aberta → erro (não deve publicar duas vezes)
- [ ] Encerrar demanda aberta → status muda para `encerrada`
- [ ] Encerrar demanda já encerrada → erro

### Excluir
- [ ] Excluir demanda sem inscrições → exclui com confirmação
- [ ] Excluir demanda com inscrições aceitas → erro bloqueando exclusão

---

## 10. Inscrições — Voluntário

### Inscrever-se
- [ ] Inscrever em demanda aberta com vagas → sucesso
- [ ] Inscrever em demanda sem vagas → erro "sem vagas disponíveis"
- [ ] Inscrever em demanda já inscrito → erro "já inscrito"
- [ ] Inscrever em demanda encerrada → erro "não está aceitando inscrições"
- [ ] Campo mensagem (opcional) → salva com e sem mensagem

### Ver inscrições
- [ ] `/inscricoes` lista todas as inscrições com status colorido
- [ ] Mensagem opcional exibida em itálico
- [ ] Data de inscrição exibida

### Cancelar
- [ ] Cancelar inscrição pendente → status muda para `cancelada`
- [ ] Cancelar inscrição aceita → verificar se é permitido (depende da regra)
- [ ] Cancelar inscrição de outro voluntário → 403

### Avaliar ONG
- [ ] Botão "Avaliar" visível apenas para inscrições `concluídas`
- [ ] Formulário de avaliação toggle (não recarrega a página)
- [ ] Enviar nota 1–5 com comentário → sucesso
- [ ] Após avaliar, botão some e aparece "Avaliado"
- [ ] Tentar avaliar duas vezes → erro "já avaliou"

---

## 11. Inscrições — ONG (por demanda)

- [ ] `/minhas-demandas/{id}/inscricoes` exibe lista paginada
- [ ] Tentativa de acessar inscrições de demanda de outra ONG → 403
- [ ] Botões Aceitar/Recusar visíveis para inscrições pendentes
- [ ] Botão Concluir visível para inscrições aceitas
- [ ] Aceitar inscrição → status muda para `aceita`
- [ ] Recusar inscrição → status muda para `recusada`
- [ ] Concluir inscrição → status muda para `concluída`
- [ ] Botão "Avaliar voluntário" visível após concluída
- [ ] Após avaliar, botão some e aparece "Avaliado"

---

## 12. Match

### Perfil incompleto
- [ ] Voluntário sem localização → banner "Perfil incompleto" + link para perfil
- [ ] Voluntário sem categorias → mesmo banner

### Perfil completo
- [ ] Lista de demandas com score % e barra colorida (verde ≥70%, âmbar ≥40%, vermelho <40%)
- [ ] Scores de categoria e proximidade exibidos
- [ ] Distância em km exibida quando disponível
- [ ] Filtro de raio (25/50/100/200/500 km) refiltra a lista
- [ ] Demandas já inscritas NÃO aparecem na lista
- [ ] Lista ordenada do maior para o menor score

---

## 13. API REST (via Swagger/Postman)

### Autenticação
- [ ] `POST /api/auth/registro` — cria usuário + perfil
- [ ] `POST /api/auth/login` — retorna Bearer token
- [ ] `GET /api/auth/eu` — retorna dados do usuário autenticado
- [ ] `POST /api/auth/logout` — invalida token

### Demandas
- [ ] `GET /api/demandas` — lista pública com paginação
- [ ] `GET /api/demandas/{id}` — detalhe público
- [ ] `POST /api/demandas` — cria (requer auth ONG)
- [ ] `PUT /api/demandas/{id}` — edita (apenas ONG dona)
- [ ] `DELETE /api/demandas/{id}` — exclui (apenas ONG dona)
- [ ] `POST /api/demandas/{id}/publicar`
- [ ] `POST /api/demandas/{id}/encerrar`

### ONGs
- [ ] `GET /api/ongs` — lista pública
- [ ] `GET /api/ongs/{id}` — detalhe público
- [ ] `PUT /api/ongs/{id}` — edita (apenas ONG dona)

### Inscrições
- [ ] `POST /api/demandas/{id}/inscricoes` — inscrever (voluntário)
- [ ] `POST /api/inscricoes/{id}/aceitar` — (ONG)
- [ ] `POST /api/inscricoes/{id}/recusar` — (ONG)
- [ ] `POST /api/inscricoes/{id}/concluir` — (ONG)
- [ ] `POST /api/inscricoes/{id}/cancelar` — (voluntário)

### Avaliações
- [ ] `POST /api/inscricoes/{id}/avaliacoes` — (após concluída)
- [ ] `GET /api/ongs/{id}/avaliacoes`
- [ ] `GET /api/voluntarios/{id}/avaliacoes`

### Match
- [ ] `GET /api/match/sugestoes` — lista rankeada
- [ ] `GET /api/match/score?demanda_id={id}` — score de uma demanda específica

---

## 14. Visual / UX

- [ ] Navbar sticky e backdrop-blur funciona ao rolar
- [ ] Responsivo em mobile (320px) — nav itens se ajustam
- [ ] Toast de sucesso aparece e desaparece (verde)
- [ ] Toast de erro aparece (vermelho)
- [ ] Erros de validação aparecem inline nos campos
- [ ] Botões com hover state visível
- [ ] Cards com hover shadow e translateY (-0.5) funcionam
- [ ] Footer exibido em todas as páginas
- [ ] Todas as páginas têm `<title>` correto

---

## 15. Edge Cases / Segurança

- [ ] SQL injection no filtro de busca → não quebra, sanitizado pelo Eloquent
- [ ] XSS em campos de texto → Blade escapa automaticamente
- [ ] Voluntário tentando aceitar inscrição de outra pessoa → 403
- [ ] ONG tentando cancelar inscrição de voluntário → 403
- [ ] Demanda com `data_limite` passada ainda aceita inscrição (se `aberta`) — confirmar comportamento esperado
- [ ] CSRF token presente em todos os formulários POST/PUT/DELETE
- [ ] `/api/...` sempre retorna JSON (não HTML) mesmo em erros 404/401/403
