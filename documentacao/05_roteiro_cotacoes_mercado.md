# Roteiro para obter as 3 cotações de mercado (Edital, item 6.9)

> Documento de trabalho interno — **não é para enviar à CPRM**. Serve para você
> conseguir 3 cotações **reais** que comprovem que os R$ 75.000,00 da proposta
> são compatíveis (na verdade, abaixo) do preço de mercado para uma solução
> equivalente. As cotações em si (e-mails, orçamentos em PDF, prints) é que vão
> anexadas à submissão — nunca invente ou estime uma cotação em nome de
> terceiros.

## Por que isso importa

O item 6.9 do edital exige que você demonstre que o valor proposto é
compatível com o mercado. Você já tem, de fato, **um preço abaixo do mercado**
— o objetivo das cotações é só **provar isso com documentos**, não é um
obstáculo. Quanto mais alta vier a cotação de um concorrente, melhor para
você: reforça que R$ 75.000,00 é um preço justo e competitivo.

Já existe uma peça pronta: o **orçamento do engenheiro de segurança do
trabalho** (R$ 7.000,00 a R$ 10.000,00, conversado em 2026-07-14). **Peça a
ele um orçamento por escrito** (e-mail ou PDF, com CNPJ da empresa dele e
data) — isso já conta como uma cotação de apoio a um item de custo real da
proposta. As duas abaixo completam o conjunto de 3.

---

## Cotação 1 — Engenheiro de segurança do trabalho (já em andamento)

**O que fazer:** peça ao engenheiro/empresa dele para confirmar por escrito
(e-mail ou orçamento em PDF timbrado) o valor já conversado, com:
- Razão social e CNPJ da empresa dele
- Descrição do serviço (elaboração/validação técnica de PGR e documentação de
  SST compatível com o escopo do desafio)
- Valor (a faixa R$ 7.000,00–10.000,00 ou o valor fechado)
- Data

**Por que serve como cotação:** é uma cotação real de um fornecedor
específico para um serviço que compõe o custo da sua proposta.

---

## Cotação 2 — Fábrica de software / empresa de desenvolvimento

Peça um orçamento para o **mesmo escopo técnico do seu sistema já pronto** —
isso mostra quanto custaria contratar terceiros para entregar o que você já
construiu sozinho, evidenciando que seu preço é econômico.

### Mensagem pronta para enviar (copie e adapte)

> Olá, gostaria de um orçamento para o desenvolvimento de uma solução de
> software com o seguinte escopo:
>
> - **Backend**: API REST (PHP/Laravel ou equivalente), com motor de regras
>   determinístico para análise de risco ocupacional (cruza atividade ×
>   ambiente × condições para recomendar EPIs), geração de relatórios em PDF e
>   DOCX, autenticação por token, banco de dados relacional.
> - **Painel web** (React ou equivalente): dashboard com indicadores em tempo
>   real, cadastro de usuários com 3 níveis de permissão, formulário de
>   criação de registros com geração de análise automática.
> - **Aplicativo mobile nativo** (Android e iOS, React Native/Expo ou
>   equivalente): funcionalidade **offline-first** (o app precisa funcionar
>   sem internet, salvando dados localmente em banco SQLite embarcado, e
>   sincronizar automaticamente quando a conexão voltar), captura de
>   geolocalização via GPS, geração de PDF no próprio aparelho sem
>   conectividade.
> - **Integração por API aberta** com serviços de terceiros (ex.: dados
>   meteorológicos, geolocalização de pontos de apoio).
> - Suporte à escala de 1.200 usuários simultâneos.
>
> Preciso do orçamento fechado (desenvolvimento completo, sem custos
> recorrentes de assinatura) para efeito de comparação de mercado. Pode ser
> por e-mail ou PDF, com CNPJ da empresa e validade da proposta.

### Onde buscar
- Fábricas de software / consultorias de desenvolvimento da sua região
  (Teresina/PI ou remoto) — busque por "fábrica de software", "desenvolvimento
  de aplicativo sob medida".
- Marketplaces B2B de desenvolvimento (ex.: Workana, 99Freelas, GetNinjas
  Empresas) — peça orçamento a 1–2 empresas cadastradas, não freelancer
  individual (para esta cotação especificamente; a próxima cobre o freelancer).
- **Referência de mercado já levantada** (não é cotação formal, mas contextualiza
  o pedido): projetos de médio porte com escopo parecido (app + web +
  integrações) costumam ficar entre R$ 120.000 e R$ 195.000 por sistema
  operacional no Brasil.

---

## Cotação 3 — Desenvolvedor(a) sênior freelancer OU plataforma SaaS pronta

Escolha **uma das duas opções** (a que for mais rápida de conseguir):

### Opção A — Freelancer sênior
Peça a um desenvolvedor freelancer sênior (Laravel + React + React Native) um
orçamento para o mesmo escopo da mensagem da Cotação 2, mas como projeto
avulso (sem estrutura de empresa). Serve para mostrar a faixa de custo mesmo
no cenário mais barato do mercado.

### Opção B — Plataforma de gestão de SST/EPI já existente (SaaS)
Peça uma proposta comercial de uma plataforma pronta de gestão de SST/EPI
(mesmo que não seja 100% equivalente — ex.: sem app offline-first ou motor de
risco automatizado) para o número de usuários equivalente ao da CPRM (~1.200).
Serve como benchmark de custo recorrente do tipo de solução.

Exemplos de plataformas do setor a considerar contatar (verifique
disponibilidade e condições atuais direto com cada uma antes de citar valores,
pois preços mudam):
- OnSafety
- Indexmed
- Sistema Metra
- GRO Safety
- Bevart

---

## Como documentar cada cotação para anexar

Para cada uma das 3, guarde:
1. **Nome/razão social e CNPJ** do fornecedor.
2. **Data** em que a cotação foi obtida (precisa ser de 2026, próxima da
   submissão).
3. **O valor** (print de e-mail, PDF de orçamento, ou mensagem formal).
4. Se for só uma mensagem de WhatsApp/chat, peça para o fornecedor confirmar
   por e-mail também — fica mais defensável como documento formal.

Depois de reunir as 3, me avise que eu monto o quadro comparativo final
(cotação × valor × economia gerada) para anexar como justificativa de
compatibilidade de custo junto ao Anexo IV-A.
