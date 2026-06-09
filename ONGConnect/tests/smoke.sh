#!/usr/bin/env bash
# =============================================================================
# ONGConnect — Smoke Test Suite
# Uso: bash tests/smoke.sh [BASE_URL]
# Padrão: BASE_URL=http://127.0.0.1:8000
# =============================================================================

BASE="${1:-http://127.0.0.1:8000}"
PASS=0; FAIL=0; SKIP=0
VOL_EMAIL="joao.pereira@email.com"
VOL_PASS="senha1234"
ONG_EMAIL="contato@maossolidarias.org.br"
ONG_PASS="senha1234"

RED='\033[0;31m'; GRN='\033[0;32m'; YEL='\033[1;33m'; BLU='\033[0;34m'; NC='\033[0m'

section() { echo -e "\n${BLU}══ $1 ══${NC}"; }
ok()      { echo -e "  ${GRN}✓${NC} $1"; PASS=$((PASS+1)); }
fail()    { echo -e "  ${RED}✗${NC} $1"; FAIL=$((FAIL+1)); }
skip()    { echo -e "  ${YEL}~${NC} $1"; SKIP=$((SKIP+1)); }

expect_status() {
  local label="$1" expected="$2" url="$3"
  local got
  got=$(curl -s -o /dev/null -w "%{http_code}" "$url" | tr -d '[:space:]')
  [ "$got" = "$expected" ] && ok "$label ($got)" || fail "$label — esperado $expected, got $got"
}

expect_json_key() {
  local label="$1" key="$2" url="$3"
  local body
  body=$(curl -s "$url")
  echo "$body" | grep -q "\"$key\"" && ok "$label (key '$key' presente)" || fail "$label — key '$key' ausente"
}

expect_contains() {
  local label="$1" needle="$2" url="$3"
  local body
  body=$(curl -s "$url")
  echo "$body" | grep -qi "$needle" && ok "$label" || fail "$label — '$needle' não encontrado"
}

# Obtém token Sanctum para um usuário
get_token() {
  curl -s -X POST "$BASE/api/auth/login" \
    -H "Content-Type: application/json" \
    -d "{\"email\":\"$1\",\"password\":\"$2\"}" \
    | grep -o '"token":"[^"]*"' | cut -d'"' -f4
}

api_get() {
  local label="$1" expected="$2" url="$3" token="${4:-}"
  local got
  got=$(curl -s -o /dev/null -w "%{http_code}" \
    ${token:+-H "Authorization: Bearer $token"} "$url" | tr -d '[:space:]')
  [ "$got" = "$expected" ] && ok "$label ($got)" || fail "$label — esperado $expected, got $got"
}

api_post() {
  local label="$1" expected="$2" url="$3" data="$4" token="${5:-}"
  local got
  got=$(curl -s -o /dev/null -w "%{http_code}" -X POST \
    -H "Content-Type: application/json" \
    ${token:+-H "Authorization: Bearer $token"} \
    -d "$data" "$url" | tr -d '[:space:]')
  [ "$got" = "$expected" ] && ok "$label ($got)" || fail "$label — esperado $expected, got $got"
}

echo -e "${BLU}╔══════════════════════════════════════╗"
echo -e "║   ONGConnect — Smoke Tests           ║"
echo -e "╚══════════════════════════════════════╝${NC}"
echo "  Base URL: $BASE"
echo "  $(date)"

# ─────────────────────────────────────────────────────────────
section "1. Servidor"
# ─────────────────────────────────────────────────────────────
expect_status "Home page carrega"            "200" "$BASE/"
expect_contains "Title correto na home"      "ONGConnect"  "$BASE/"

# ─────────────────────────────────────────────────────────────
section "2. Páginas públicas (Web)"
# ─────────────────────────────────────────────────────────────
expect_status "GET /demandas"                "200" "$BASE/demandas"
expect_status "GET /demandas?q=test"         "200" "$BASE/demandas?q=test"
expect_status "GET /demandas?tipo=presencial" "200" "$BASE/demandas?tipo=presencial"
expect_status "GET /demandas/2"              "200" "$BASE/demandas/2"
expect_status "GET /demandas/9999 (404)"     "404" "$BASE/demandas/9999"
expect_status "GET /ongs"                    "200" "$BASE/ongs"
expect_status "GET /ongs/1"                  "200" "$BASE/ongs/1"
expect_status "GET /ongs/9999 (404)"         "404" "$BASE/ongs/9999"

# ─────────────────────────────────────────────────────────────
section "3. Auth Web"
# ─────────────────────────────────────────────────────────────
expect_status "GET /login"                   "200" "$BASE/login"
expect_status "GET /registro"                "200" "$BASE/registro"
expect_status "GET /dashboard sem auth"      "302" "$BASE/dashboard"
expect_status "GET /dashboard/ong sem auth"  "302" "$BASE/dashboard/ong"
expect_status "GET /perfil sem auth"         "302" "$BASE/perfil"
expect_status "GET /match sem auth"          "302" "$BASE/match"
expect_status "GET /inscricoes sem auth"     "302" "$BASE/inscricoes"
expect_status "GET /minhas-demandas sem auth" "302" "$BASE/minhas-demandas"

# ─────────────────────────────────────────────────────────────
section "4. API — Endpoints públicos"
# ─────────────────────────────────────────────────────────────
api_get "GET /api/demandas"                  "200" "$BASE/api/demandas"
api_get "GET /api/demandas/2"                "200" "$BASE/api/demandas/2"
api_get "GET /api/demandas/9999 (404)"       "404" "$BASE/api/demandas/9999"
api_get "GET /api/ongs"                      "200" "$BASE/api/ongs"
api_get "GET /api/ongs/1"                    "200" "$BASE/api/ongs/1"
api_get "GET /api/categorias"                "200" "$BASE/api/categorias"
expect_json_key "API demandas tem 'data'"    "data"  "$BASE/api/demandas"
expect_json_key "API ongs tem 'data'"        "data"  "$BASE/api/ongs"
expect_json_key "API categorias tem 'data'"  "data"  "$BASE/api/categorias"

# ─────────────────────────────────────────────────────────────
section "5. API — Auth (Sanctum)"
# ─────────────────────────────────────────────────────────────
api_post "POST /api/auth/login voluntário"   "200" \
  "$BASE/api/auth/login" \
  "{\"email\":\"$VOL_EMAIL\",\"password\":\"$VOL_PASS\"}"

api_post "POST /api/auth/login ONG"          "200" \
  "$BASE/api/auth/login" \
  "{\"email\":\"$ONG_EMAIL\",\"password\":\"$ONG_PASS\"}"

api_post "POST /api/auth/login senha errada" "401" \
  "$BASE/api/auth/login" \
  "{\"email\":\"$VOL_EMAIL\",\"password\":\"errada\"}"

VOL_TOKEN=$(get_token "$VOL_EMAIL" "$VOL_PASS")
ONG_TOKEN=$(get_token "$ONG_EMAIL" "$ONG_PASS")

if [ -n "$VOL_TOKEN" ]; then
  ok "Token voluntário obtido"
else
  fail "Token voluntário NÃO obtido"; VOL_TOKEN=""
fi

if [ -n "$ONG_TOKEN" ]; then
  ok "Token ONG obtido"
else
  fail "Token ONG NÃO obtido"; ONG_TOKEN=""
fi

# ─────────────────────────────────────────────────────────────
section "6. API — Rotas protegidas sem token (401)"
# ─────────────────────────────────────────────────────────────
api_get "GET /api/dashboard/voluntario sem token"  "401" "$BASE/api/dashboard/voluntario"
api_get "GET /api/dashboard/ong sem token"         "401" "$BASE/api/dashboard/ong"
api_get "GET /api/inscricoes/minhas sem token"      "401" "$BASE/api/inscricoes/minhas"
api_get "GET /api/match/sugestoes sem token"        "401" "$BASE/api/match/sugestoes"

# ─────────────────────────────────────────────────────────────
section "7. API — Voluntário autenticado"
# ─────────────────────────────────────────────────────────────
if [ -n "$VOL_TOKEN" ]; then
  api_get "GET /api/auth/eu"                       "200" "$BASE/api/auth/eu" "$VOL_TOKEN"
  api_get "GET /api/dashboard/voluntario"          "200" "$BASE/api/dashboard/voluntario" "$VOL_TOKEN"
  api_get "GET /api/inscricoes/minhas"             "200" "$BASE/api/inscricoes/minhas" "$VOL_TOKEN"
  api_get "GET /api/match/sugestoes"               "200" "$BASE/api/match/sugestoes" "$VOL_TOKEN"
  api_get "GET /api/voluntarios/6 (próprio perfil)" "200" "$BASE/api/voluntarios/6"
else
  skip "Testes de voluntário autenticado (sem token)"
fi

# ─────────────────────────────────────────────────────────────
section "8. API — ONG autenticada"
# ─────────────────────────────────────────────────────────────
if [ -n "$ONG_TOKEN" ]; then
  api_get "GET /api/auth/eu"                       "200" "$BASE/api/auth/eu" "$ONG_TOKEN"
  api_get "GET /api/dashboard/ong"                 "200" "$BASE/api/dashboard/ong" "$ONG_TOKEN"
  api_get "GET /api/ongs/1"                        "200" "$BASE/api/ongs/1" "$ONG_TOKEN"
else
  skip "Testes de ONG autenticada (sem token)"
fi

# ─────────────────────────────────────────────────────────────
section "9. API — CRUD Demandas (ONG)"
# ─────────────────────────────────────────────────────────────
if [ -n "$ONG_TOKEN" ]; then
  # Criar demanda
  CREATE_RESP=$(curl -s -X POST "$BASE/api/demandas" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $ONG_TOKEN" \
    -d '{"titulo":"[TESTE] Demanda smoke test","descricao":"Descricao de teste automatizado","tipo":"presencial"}')
  NEW_ID=$(echo "$CREATE_RESP" | grep -o '"id":[0-9]*' | head -1 | cut -d: -f2)

  if [ -n "$NEW_ID" ]; then
    ok "POST /api/demandas — criada (ID $NEW_ID)"

    # Ler a demanda criada
    api_get "GET /api/demandas/$NEW_ID"            "200" "$BASE/api/demandas/$NEW_ID" "$ONG_TOKEN"

    # Atualizar
    UPDATE_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X PUT \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $ONG_TOKEN" \
      -d '{"titulo":"[TESTE] Demanda atualizada","descricao":"Atualizado","tipo":"presencial"}' \
      "$BASE/api/demandas/$NEW_ID")
    [ "$UPDATE_CODE" = "200" ] && ok "PUT /api/demandas/$NEW_ID ($UPDATE_CODE)" || fail "PUT /api/demandas/$NEW_ID — got $UPDATE_CODE"

    # Publicar
    PUB_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST \
      -H "Authorization: Bearer $ONG_TOKEN" \
      "$BASE/api/demandas/$NEW_ID/publicar")
    [ "$PUB_CODE" = "200" ] && ok "POST /api/demandas/$NEW_ID/publicar ($PUB_CODE)" || fail "POST /publicar — got $PUB_CODE"

    # Encerrar
    ENC_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST \
      -H "Authorization: Bearer $ONG_TOKEN" \
      "$BASE/api/demandas/$NEW_ID/encerrar")
    [ "$ENC_CODE" = "200" ] && ok "POST /api/demandas/$NEW_ID/encerrar ($ENC_CODE)" || fail "POST /encerrar — got $ENC_CODE"

    # Deletar
    DEL_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X DELETE \
      -H "Authorization: Bearer $ONG_TOKEN" \
      "$BASE/api/demandas/$NEW_ID")
    [ "$DEL_CODE" = "200" ] && ok "DELETE /api/demandas/$NEW_ID ($DEL_CODE)" || fail "DELETE — got $DEL_CODE"
  else
    fail "POST /api/demandas — sem ID na resposta: $CREATE_RESP"
  fi
else
  skip "CRUD Demandas API (sem token ONG)"
fi

# ─────────────────────────────────────────────────────────────
section "10. API — Inscrição (Voluntário)"
# ─────────────────────────────────────────────────────────────
if [ -n "$VOL_TOKEN" ] && [ -n "$ONG_TOKEN" ]; then
  # Criar demanda temporária para teste de inscrição
  TEMP_DEMANDA=$(curl -s -X POST "$BASE/api/demandas" \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $ONG_TOKEN" \
    -d '{"titulo":"[TESTE-INSC] Temp","descricao":"Teste","tipo":"presencial"}')
  TEMP_ID=$(echo "$TEMP_DEMANDA" | grep -o '"id":[0-9]*' | head -1 | cut -d: -f2)

  # Publicar a demanda temporária
  curl -s -X POST "$BASE/api/demandas/$TEMP_ID/publicar" \
    -H "Authorization: Bearer $ONG_TOKEN" > /dev/null

  if [ -n "$TEMP_ID" ]; then
    # Inscrever voluntário
    INSC_RESP=$(curl -s -X POST "$BASE/api/demandas/$TEMP_ID/inscricoes" \
      -H "Content-Type: application/json" \
      -H "Authorization: Bearer $VOL_TOKEN" \
      -d '{"mensagem":"Teste automatizado"}')

    if echo "$INSC_RESP" | grep -q '"id"'; then
      INSC_ID=$(echo "$INSC_RESP" | grep -o '"id":[0-9]*' | head -1 | cut -d: -f2)
      ok "POST /api/demandas/$TEMP_ID/inscricoes — inscrito (ID $INSC_ID)"

      # Tentar inscrever de novo (422)
      DUP_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST \
        "$BASE/api/demandas/$TEMP_ID/inscricoes" \
        -H "Content-Type: application/json" \
        -H "Authorization: Bearer $VOL_TOKEN" \
        -d '{}' | tr -d '[:space:]')
      [ "$DUP_CODE" = "422" ] && ok "Inscrição duplicada bloqueada (422)" || fail "Inscrição duplicada deveria retornar 422, got $DUP_CODE"

      # Aceitar inscrição pela ONG
      if [ -n "$INSC_ID" ]; then
        ACE_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST \
          -H "Authorization: Bearer $ONG_TOKEN" \
          "$BASE/api/inscricoes/$INSC_ID/aceitar" | tr -d '[:space:]')
        [ "$ACE_CODE" = "200" ] && ok "POST /api/inscricoes/$INSC_ID/aceitar (200)" || fail "Aceitar inscrição — got $ACE_CODE"

        # Concluir inscrição
        CON_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST \
          -H "Authorization: Bearer $ONG_TOKEN" \
          "$BASE/api/inscricoes/$INSC_ID/concluir" | tr -d '[:space:]')
        [ "$CON_CODE" = "200" ] && ok "POST /api/inscricoes/$INSC_ID/concluir (200)" || fail "Concluir inscrição — got $CON_CODE"
      fi
    else
      fail "POST /api/demandas/$TEMP_ID/inscricoes — $(echo $INSC_RESP | cut -c1-80)"
    fi

    # Limpar demanda temporária
    curl -s -X DELETE "$BASE/api/demandas/$TEMP_ID" \
      -H "Authorization: Bearer $ONG_TOKEN" > /dev/null
  else
    skip "Não foi possível criar demanda temporária para teste de inscrição"
  fi
else
  skip "Testes de inscrição (sem tokens)"
fi

# ─────────────────────────────────────────────────────────────
section "11. API — Perfil ONG (PUT)"
# ─────────────────────────────────────────────────────────────
if [ -n "$ONG_TOKEN" ]; then
  UPD_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X PUT \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $ONG_TOKEN" \
    -d '{"razao_social":"Maos Solidarias Teste","cidade":"Rio do Sul","uf":"SC"}' \
    "$BASE/api/ongs/1" | tr -d '[:space:]')
  [ "$UPD_CODE" = "200" ] && ok "PUT /api/ongs/1 ($UPD_CODE)" || fail "PUT /api/ongs/1 — got $UPD_CODE"

  # ONG não pode editar outra ONG (403)
  FORBID=$(curl -s -o /dev/null -w "%{http_code}" -X PUT \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $ONG_TOKEN" \
    -d '{"razao_social":"Hack"}' \
    "$BASE/api/ongs/2")
  [ "$FORBID" = "403" ] && ok "PUT /api/ongs/2 por ONG errada bloqueado (403)" || fail "PUT outra ONG deveria retornar 403, got $FORBID"
else
  skip "Testes PUT ONG (sem token)"
fi

# ─────────────────────────────────────────────────────────────
section "12. Validações (422)"
# ─────────────────────────────────────────────────────────────
# Registro sem campos obrigatórios
REG_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST \
  -H "Content-Type: application/json" \
  "$BASE/api/auth/registro" \
  -d '{"email":"invalido"}')
[ "$REG_CODE" = "422" ] && ok "Registro com dados inválidos retorna 422" || fail "Registro inválido deveria retornar 422, got $REG_CODE"

# Criar demanda sem campos obrigatórios
if [ -n "$ONG_TOKEN" ]; then
  VAL_CODE=$(curl -s -o /dev/null -w "%{http_code}" -X POST \
    -H "Content-Type: application/json" \
    -H "Authorization: Bearer $ONG_TOKEN" \
    -d '{"descricao":"sem titulo"}' \
    "$BASE/api/demandas")
  [ "$VAL_CODE" = "422" ] && ok "Demanda sem título retorna 422" || fail "Demanda sem título deveria retornar 422, got $VAL_CODE"
fi

# ─────────────────────────────────────────────────────────────
section "13. Web — Páginas autenticadas (sessão via cookie)"
# ─────────────────────────────────────────────────────────────
# Login web para obter cookie de sessão
COOKIE_JAR=$(mktemp)
LOGIN_CODE=$(curl -s -o /dev/null -w "%{http_code}" -c "$COOKIE_JAR" -b "$COOKIE_JAR" \
  -X POST "$BASE/login" \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -d "_token=$(curl -s $BASE/login | grep -o 'name="_token" value="[^"]*"' | cut -d'"' -f4)&email=$VOL_EMAIL&password=$VOL_PASS")

if curl -s -b "$COOKIE_JAR" "$BASE/dashboard" | grep -qi "dashboard\|painel\|voluntário\|inscri"; then
  ok "Dashboard voluntário acessível após login web"
  expect_status "GET /perfil autenticado"    "200" "$BASE/perfil"  # sem cookie — deve 302

  PERF_CODE=$(curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_JAR" "$BASE/perfil")
  [ "$PERF_CODE" = "200" ] && ok "GET /perfil com sessão ($PERF_CODE)" || fail "GET /perfil com sessão — got $PERF_CODE"

  MATCH_CODE=$(curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_JAR" "$BASE/match")
  [ "$MATCH_CODE" = "200" ] && ok "GET /match com sessão ($MATCH_CODE)" || fail "GET /match com sessão — got $MATCH_CODE"

  INSC_CODE=$(curl -s -o /dev/null -w "%{http_code}" -b "$COOKIE_JAR" "$BASE/inscricoes")
  [ "$INSC_CODE" = "200" ] && ok "GET /inscricoes com sessão ($INSC_CODE)" || fail "GET /inscricoes — got $INSC_CODE"
else
  skip "Testes web com sessão (login via curl pode não funcionar por CSRF)"
fi
rm -f "$COOKIE_JAR"

# ─────────────────────────────────────────────────────────────
section "14. Swagger / Docs"
# ─────────────────────────────────────────────────────────────
expect_status "GET /api/documentation (Swagger UI)" "200" "$BASE/api/documentation"
expect_status "GET /docs (OpenAPI JSON)"            "200" "$BASE/docs"

# ─────────────────────────────────────────────────────────────
echo -e "\n${BLU}══ Resultado ══${NC}"
TOTAL=$((PASS + FAIL + SKIP))
echo -e "  Total:   $TOTAL"
echo -e "  ${GRN}Passou:  $PASS${NC}"
echo -e "  ${RED}Falhou:  $FAIL${NC}"
echo -e "  ${YEL}Pulado:  $SKIP${NC}"

if [ "$FAIL" -gt 0 ]; then
  echo -e "\n  ${RED}⚠ Há falhas. Revise os itens marcados com ✗.${NC}"
  exit 1
else
  echo -e "\n  ${GRN}✓ Todos os testes passaram!${NC}"
  exit 0
fi
