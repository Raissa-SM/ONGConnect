# 01. Levantamento do Propósito — Interação Dialógica

> **Etapa da Pirâmide de Projeto:** Diagnóstico inicial e definição de objetivos sociais e de negócio.

## 1. Contexto

A região do Alto Vale do Itajaí concentra dezenas de organizações do terceiro setor — ONGs, instituições de acolhimento, projetos comunitários e iniciativas filantrópicas — que dependem fortemente do trabalho voluntário e de doações para sustentar suas operações. Em paralelo, existe uma população estudantil e profissional disposta a contribuir, mas que frequentemente desconhece quais demandas existem, onde estão e como se conectar a elas.

Este projeto se insere no programa de extensão **Universidade Solidária** da Unidavi, cujo objetivo é canalizar o esforço voluntário da academia para o atendimento de necessidades críticas de ONGs e instituições de acolhimento.

## 2. Vinculação ao programa de extensão

O sistema foi pensado como uma materialização concreta do conceito de **interação dialógica** previsto nas diretrizes da extensão Unidavi: o conhecimento técnico produzido em sala de aula (engenharia de software, programação web, banco de dados) se retroalimenta com a demanda comunitária real (organização do trabalho voluntário no terceiro setor regional), gerando uma solução com utilidade prática e impacto social mensurável.

## 3. Diagnóstico das dores

A pesquisa diagnóstica foi conduzida com base em fontes secundárias (publicações sobre gestão do terceiro setor, relatórios de instituições como a ABCR e o IDIS, e materiais de divulgação de ONGs da região). A partir dela, foram identificadas as seguintes dores recorrentes:

### 3.1 Dores das ONGs

- **Dificuldade em divulgar demandas** de forma sistemática — a comunicação geralmente acontece via redes sociais pessoais dos diretores, com baixo alcance.
- **Falta de cadastro estruturado de voluntários**, o que impede a busca rápida por alguém com a habilidade necessária quando surge uma demanda urgente.
- **Ausência de histórico** de quem participou de quais ações — o que dificulta reconhecer engajamento, emitir declarações e fidelizar voluntários.
- **Recebimento descoordenado de doações materiais**, com excesso de itens não prioritários e escassez dos verdadeiramente necessários.
- **Dificuldade em avaliar a confiabilidade** de novos voluntários, especialmente em ações que envolvem público vulnerável.

### 3.2 Dores dos voluntários

- **Não saber por onde começar** — a maioria não conhece quais ONGs existem na região nem o que precisam.
- **Esforço manual** de seguir múltiplas páginas e grupos para descobrir oportunidades.
- **Incompatibilidade frequente** entre o que sabem fazer (habilidades específicas como design, programação, contabilidade, etc.) e o que está sendo divulgado.
- **Falta de feedback** sobre o impacto da própria contribuição.
- **Insegurança** ao entrar em contato pela primeira vez com uma instituição desconhecida.

## 4. Cenários representativos (personas e situações)

Como a fonte do levantamento é baseada em pesquisa, foram construídos cenários fictícios mas realistas, calibrados pelas dores acima:

### Cenário A — Mutirão emergencial

> *A "ONG Mãos Solidárias", de Rio do Sul, recebeu uma doação de 500 cestas básicas que precisam ser embaladas e entregues em até 4 dias. A coordenadora Marta abre o sistema, publica uma demanda do tipo "voluntariado presencial" com data, local, número de vagas e categoria "ação social". O sistema notifica voluntários cadastrados próximos à região e dentro da categoria de interesse. Em 24 horas, 18 voluntários já se inscreveram.*

### Cenário B — Habilidade específica

> *O "Lar dos Idosos São Francisco" precisa reformular seu site institucional para captar doações via Pix, mas não tem orçamento para contratar uma agência. Publicam uma demanda do tipo "habilidade específica" com a tag "desenvolvimento web". O sistema sugere automaticamente três voluntários cadastrados como desenvolvedores no Alto Vale, com pontuação alta no critério de habilidade.*

### Cenário C — Voluntário de primeira viagem

> *João é estudante de Sistemas de Informação na Unidavi, mora em Ituporanga, sabe programar e tem disponibilidade aos sábados pela manhã. Cria seu perfil de voluntário, marca categorias de interesse (educação e tecnologia), cadastra suas habilidades e endereço. Recebe na sua aba de "demandas sugeridas" três oportunidades pontuadas por proximidade, categoria e habilidade. Inscreve-se em uma oficina de letramento digital para idosos.*

### Cenário D — Doação direcionada

> *A "Casa da Criança" precisa de fraldas geriátricas (sim, para os familiares acompanhantes) e leite em pó. Publicam uma demanda do tipo "doação material" com lista específica de itens. Pessoas que querem doar sabem exatamente o que é prioridade naquele momento, evitando o desperdício comum em campanhas genéricas.*

### Cenário E — Reconhecimento e fidelização

> *Após concluir a participação no mutirão do Cenário A, Marta avalia os voluntários que efetivamente compareceram. Cada voluntário avalia também a experiência. As avaliações alimentam o histórico de participação, que pode ser exportado pelo voluntário como comprovante de horas extensionistas, e pelas próximas ONGs como referência de confiabilidade.*

## 5. Objetivos sociais

- **Democratizar o acesso à informação** sobre oportunidades de voluntariado e doação na região.
- **Profissionalizar a gestão de voluntários** em ONGs que hoje operam de forma artesanal.
- **Reduzir o desperdício** em campanhas de doação por meio de demandas materiais específicas.
- **Reconhecer formalmente o engajamento** dos voluntários com histórico exportável.
- **Fortalecer o tecido social regional** ao aumentar a frequência e a qualidade das interações entre cidadãos e organizações do terceiro setor.

## 6. Objetivos de negócio (do sistema)

- Disponibilizar um catálogo público de demandas filtrável por tipo, categoria e localização.
- Sugerir automaticamente, para cada voluntário cadastrado, as demandas mais compatíveis com seu perfil (algoritmo de pontuação multi-critério).
- Manter um histórico imutável de participações concluídas, com avaliação mútua entre ONG e voluntário.
- Gerenciar todo o ciclo de vida de uma inscrição: pendente → aceita → concluída ou cancelada.
- Servir como API REST consumível por aplicações futuras (mobile, integrações com sites institucionais das ONGs, etc.).

## 7. Stakeholders

| Stakeholder | Papel | Interesse principal |
|-------------|-------|---------------------|
| ONG | Publica demandas, gerencia inscrições, avalia voluntários | Encontrar voluntários adequados rapidamente |
| Voluntário | Cria perfil, busca/recebe sugestões, se inscreve, é avaliado | Encontrar oportunidades alinhadas ao seu perfil e disponibilidade |
| Coordenação acadêmica | Acompanha o uso e o impacto do sistema | Validar o projeto como contribuição extensionista |
| Comunidade regional | Beneficiária indireta do trabalho realizado pelas ONGs | Acesso ampliado a serviços e ações sociais |
| Equipe de desenvolvimento | Acadêmicos responsáveis pela construção e manutenção | Atender requisitos técnicos das disciplinas e gerar valor real |

## 8. Justificativa da escolha

A escolha por uma plataforma de match entre voluntários e ONGs atende simultaneamente três critérios estruturantes:

1. **Pertinência social:** o problema é real, recorrente e tem impacto direto em populações vulneráveis atendidas pelas ONGs.
2. **Adequação acadêmica:** o domínio comporta com naturalidade as quatro entidades mínimas exigidas pela disciplina de Programação Orientada a Objeto, com relacionamentos suficientemente ricos para justificar uso de Eloquent ORM, autenticação tokenizada e arquitetura MVC completa.
3. **Replicabilidade:** uma vez funcional, o sistema pode ser oferecido a qualquer ONG da região como produto extensionista contínuo, transcendendo o ciclo da disciplina.
