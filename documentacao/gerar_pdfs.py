"""Gera os PDFs de apoio à inscrição no Edital CPSI nº 0001/2026 (CPRM/SGB).

Este script é uma ferramenta de documentação isolada da aplicação (Laravel +
React) — usa Python só porque é prático para gerar PDF programaticamente, mas
não faz parte da "solução" entregável em si.

Uso: rodar com um Python que tenha reportlab instalado:
    pip install --user reportlab
    python gerar_pdfs.py

Conteúdo baseado exclusivamente nos documentos-fonte em fontes/ e no
CLAUDE.md do projeto. Não adicionar informação sobre o edital aqui sem
conferir a fonte.
"""
from __future__ import annotations

from reportlab.lib import colors
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    ListFlowable,
    ListItem,
    PageBreak,
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)

styles = getSampleStyleSheet()

TITLE = ParagraphStyle("TitleCustom", parent=styles["Title"], fontSize=20, spaceAfter=6)
SUBTITLE = ParagraphStyle("SubtitleCustom", parent=styles["Normal"], fontSize=11, textColor=colors.grey, spaceAfter=18)
H1 = ParagraphStyle("H1Custom", parent=styles["Heading1"], fontSize=15, spaceBefore=18, spaceAfter=8, textColor=colors.HexColor("#1565C0"))
H2 = ParagraphStyle("H2Custom", parent=styles["Heading2"], fontSize=12.5, spaceBefore=12, spaceAfter=6, textColor=colors.HexColor("#0D47A1"))
BODY = ParagraphStyle("BodyCustom", parent=styles["Normal"], fontSize=10.3, leading=15, spaceAfter=6)
NOTE = ParagraphStyle("NoteCustom", parent=styles["Normal"], fontSize=9.5, leading=13, textColor=colors.HexColor("#B71C1C"), spaceBefore=4, spaceAfter=8, borderColor=colors.HexColor("#B71C1C"))
SOURCE = ParagraphStyle("SourceCustom", parent=styles["Normal"], fontSize=8.5, textColor=colors.grey, spaceBefore=2, spaceAfter=10)
CELL = ParagraphStyle("CellCustom", parent=styles["Normal"], fontSize=8.3, leading=11)
CELL_HEAD = ParagraphStyle("CellHeadCustom", parent=styles["Normal"], fontSize=8.8, leading=11, textColor=colors.white, fontName="Helvetica-Bold")


def cellify(rows: list[list[str]]) -> list[list]:
    """Converte uma tabela de strings em células Paragraph que quebram linha."""
    out = []
    for i, row in enumerate(rows):
        style = CELL_HEAD if i == 0 else CELL
        out.append([Paragraph(cell, style) for cell in row])
    return out


def bullets(items: list[str], style=BODY) -> ListFlowable:
    return ListFlowable(
        [ListItem(Paragraph(i, style), bulletColor=colors.HexColor("#1565C0")) for i in items],
        bulletType="bullet",
        leftIndent=14,
        spaceAfter=8,
    )


def build_doc(filename: str, title: str, subtitle: str, story_builder) -> None:
    doc = SimpleDocTemplate(
        filename,
        pagesize=A4,
        topMargin=2.2 * cm,
        bottomMargin=2 * cm,
        leftMargin=2 * cm,
        rightMargin=2 * cm,
        title=title,
    )
    story = [Paragraph(title, TITLE), Paragraph(subtitle, SUBTITLE)]
    story += story_builder()
    doc.build(story)
    print(f"Gerado: {filename}")


# ---------------------------------------------------------------------------
# Documento 1 — Guia passo a passo de inscrição
# ---------------------------------------------------------------------------

def story_guia() -> list:
    s = []

    s.append(Paragraph("1. Visão geral do processo", H1))
    s.append(Paragraph(
        "A CPRM (Serviço Geológico do Brasil) abriu o Edital nº 0001-CPSI/2026 "
        "(Processo nº 48086.009199/2024-41), na modalidade CPSI — Contrato Público "
        "de Solução Inovadora (Lei Complementar nº 182/2021). O desafio único é "
        "<b>\"Segurança nas Saídas para Campo com Definição e Monitoramento de EPIs\"</b>. "
        "Podem ser selecionadas até 2 propostas, cada uma com valor máximo de "
        "R$ 200.000,00 na fase de teste (20% de um CPSI potencial de R$ 1.000.000,00; "
        "os 80% restantes só existem se houver, depois, um Contrato de Fornecimento "
        "separado, não garantido).", BODY))
    s.append(Paragraph("Fonte: Edital, item 2.1 e 2.2; Documento de Descrição do Desafio, item 5.", SOURCE))

    s.append(Paragraph("2. Cronograma oficial", H1))
    dados_cronograma = [
        ["Fase", "Data"],
        ["Divulgação do edital", "23/06/2026"],
        ["Prazo final para envio da proposta (via plataforma SOLV)", "até 23h59 de 06/08/2026"],
        ["Pitch Day — apresentação oral (até 20 min)", "20/08/2026 a 21/08/2026"],
        ["Avaliação das propostas pela Comissão", "até 10/09/2026"],
        ["Divulgação do resultado do julgamento", "11/09/2026"],
        ["Envio dos documentos de habilitação", "14/09 a 15/09/2026"],
        ["Divulgação do resultado da habilitação", "17/09/2026"],
        ["Negociação com selecionados", "18/09/2026"],
        ["Fase recursal", "21/09 a 25/09/2026"],
        ["Julgamento de recursos (se houver)", "até 09/10/2026"],
        ["Adjudicação e homologação", "até 15/10/2026"],
        ["Assinatura do CPSI", "até 35 dias úteis após homologação"],
    ]
    t = Table(cellify(dados_cronograma), colWidths=[10.5 * cm, 5.5 * cm])
    t.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1565C0")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
        ("FONTSIZE", (0, 0), (-1, -1), 9),
        ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#CCCCCC")),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#F2F6FC")]),
        ("VALIGN", (0, 0), (-1, -1), "MIDDLE"),
        ("TOPPADDING", (0, 0), (-1, -1), 5),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
    ]))
    s.append(t)
    s.append(Paragraph(
        "Datas podem ser retificadas pela CPRM. Confirme sempre em "
        "https://sgb.gov.br/cpsi/ antes de uma etapa com prazo apertado.", NOTE))
    s.append(Paragraph("Fonte: Edital, item 3.2; Documento de Descrição do Desafio, item 9.5.2.3.", SOURCE))

    s.append(PageBreak())
    s.append(Paragraph("3. Quem pode participar", H1))
    s.append(bullets([
        "Pessoas físicas (maiores/emancipadas) ou jurídicas, nacionais ou estrangeiras.",
        "Com ou sem fins lucrativos, isoladas ou em consórcio.",
        "ICTs públicas ou privadas, incluindo universidades.",
        "Sociedades cooperativas.",
        "ME/EPP/cooperativas: proposta só é considerada se o valor for inferior a R$ 80.000,00.",
    ]))
    s.append(Paragraph(
        "Estão impedidos: empresas/pessoas com vínculo com dirigentes ou empregados da CPRM "
        "envolvidos na contratação (inclusive parentesco até 3º grau), empresas suspensas/"
        "impedidas/inidôneas perante a Administração Pública, autores do Termo de Referência, "
        "condenados por trabalho escravo/infantil ou crimes contra a Administração, entre "
        "outras vedações do art. 9º e 14 da Lei nº 14.133/2021. Ver Edital, itens 4.1 a 4.3 "
        "para a lista completa.", BODY))

    s.append(Paragraph("4. Passo a passo da inscrição (1ª etapa)", H1))
    s.append(Paragraph(
        "A inscrição é feita <b>exclusivamente</b> pela plataforma SOLV, neste endereço:", BODY))
    s.append(Paragraph(
        "https://sbg.solv.network/show_program/formulario-de-inscricao-cpsi-sgb", BODY))
    s.append(Paragraph(
        "O e-mail cpsi@sgb.gov.br <b>não aceita propostas</b> — serve só para dúvidas, "
        "esclarecimentos e impugnações. O site https://sgb.gov.br/cpsi/ é só de divulgação.", NOTE))

    s.append(Paragraph("Passo 1 — Reunir os dados da empresa/proponente", H2))
    s.append(bullets([
        "Razão social, CNPJ (ou CPF, se pessoa física), endereço completo.",
        "Dados do representante legal que assinará o contrato (CPF, RG, cargo, mandato).",
        "Dados bancários da empresa (agência, conta, banco).",
        "Se for consórcio: identificar a empresa líder e preparar o compromisso de consórcio.",
    ]))

    s.append(Paragraph("Passo 2 — Preparar o vídeo Pitch (obrigatório, até 5 minutos)", H2))
    s.append(Paragraph("O vídeo deve abordar, no mínimo:", BODY))
    s.append(bullets([
        "A solução proposta;",
        "A maturidade comercial da solução (nível TRL, se já testada em campo real);",
        "Casos de aplicação (cases anteriores, se houver);",
        "Experiência da equipe;",
        "Geração de valor para a CPRM;",
        "Modelo de negócios do proponente.",
    ]))
    s.append(Paragraph(
        "Suba o vídeo em uma plataforma pública ou não-listada (YouTube, Vimeo) e "
        "inclua o link no formulário — não é feito upload de arquivo de vídeo.", BODY))

    s.append(Paragraph("Passo 3 — Preencher o Anexo IV-A (Proposta de Preços)", H2))
    s.append(bullets([
        "Identificação completa do proponente.",
        "Planilha de formação de preços (valor total ≤ R$ 200.000,00 para a fase de teste; "
        "≤ R$ 80.000,00 se ME/EPP/cooperativa).",
        "Declarar que todos os impostos, taxas e encargos já estão incluídos no valor.",
        "Dados bancários e dados do representante legal.",
    ]))

    s.append(Paragraph("Passo 4 — Preencher o Anexo IV-B (Plano de Trabalho)", H2))
    s.append(bullets([
        "Dados gerais da empresa e do representante legal.",
        "Identificação do responsável técnico do projeto (ver Passo 6 sobre o engenheiro "
        "de segurança do trabalho).",
        "Descrição da realidade / nexo entre o desafio e a solução proposta.",
        "Metas quantitativas e qualitativas, com parâmetros de aferição e periodicidade.",
        "Cronograma físico de execução (mês a mês).",
        "Capacidade instalada: equipe, instalações físicas, equipamentos.",
        "Detalhamento da aplicação dos recursos financeiros e cronograma de desembolso.",
        "Matriz de riscos do projeto (sugestão de modelo incluída no próprio Anexo IV-B).",
    ]))

    s.append(Paragraph("Passo 5 — Escrever a apresentação técnico-comercial", H2))
    s.append(bullets([
        "Escopo, duração e custos estimados da proposta.",
        "Esboço do modelo de negócios para fornecimento em escala (2ª etapa, caso a "
        "fase de teste seja bem-sucedida).",
    ]))

    s.append(Paragraph("Passo 6 — Verificar a qualificação técnica exigida", H2))
    s.append(Paragraph(
        "O edital exige, na fase de habilitação, um <b>engenheiro de segurança do trabalho</b> "
        "com atuação em \"Gerenciamento e Controle de Riscos\" ou \"Gestão da Segurança do "
        "Trabalho\", com atestado de elaboração de PGR em empresa de grau de risco mínimo 2 "
        "(NR-04). Providencie esse profissional (próprio ou parceiro) desde já — é um dos "
        "pontos mais fáceis de reprovar a proposta se deixado para depois.", BODY))
    s.append(Paragraph("Fonte: Documento de Descrição do Desafio, item 12.5.3.", SOURCE))

    s.append(Paragraph("Passo 7 — Submeter tudo pela plataforma SOLV", H2))
    s.append(Paragraph(
        "Envie todos os documentos em <b>PDF</b> (obrigatório — outro formato pode levar a "
        "eliminação sumária). Confira se não falta nenhum dos itens do Passo 2 ao Passo 5 "
        "antes de confirmar o envio. Você pode substituir a proposta quantas vezes quiser "
        "até o prazo final; só a última versão enviada vale.", BODY))
    s.append(Paragraph("Fonte: Documento de Descrição do Desafio, item 9.2.2; Edital, item 5.1.2 e 5.3.", SOURCE))

    s.append(PageBreak())
    s.append(Paragraph("5. Organização dos documentos (pasta local)", H1))
    s.append(Paragraph(
        "Sugestão de organização de pastas no computador, para não perder nada antes do "
        "envio pela plataforma SOLV:", BODY))
    s.append(bullets([
        "<b>01_inscricao/</b> — vídeo pitch (ou link), Anexo IV-A preenchido, Anexo IV-B "
        "preenchido, apresentação técnico-comercial.",
        "<b>02_habilitacao/</b> — documentos jurídicos (contrato social/ato constitutivo), "
        "certidões fiscais/trabalhistas/FGTS, balanço patrimonial e índices contábeis, "
        "atestado de capacidade técnica do engenheiro de segurança, Anexos III-A a III-D "
        "preenchidos e assinados.",
        "<b>03_negociacao/</b> — versão final negociada do plano de trabalho, matriz de "
        "riscos, minuta contratual ajustada.",
        "<b>04_execucao/</b> — relatórios mensais de andamento, relatório final (só depois "
        "da assinatura do CPSI).",
    ]))

    s.append(Paragraph("6. Documentos de habilitação (2ª etapa — só se pré-selecionado)", H1))
    s.append(Paragraph(
        "Exigidos apenas após ser selecionado na 1ª etapa (julgamento), no prazo de "
        "14 a 15/09/2026:", BODY))
    s.append(bullets([
        "<b>Habilitação jurídica</b>: ato constitutivo/contrato social conforme o tipo "
        "societário (empresário individual, MEI, sociedade simples/empresária, ICT, "
        "cooperativa etc.) — ver item 12.2 do Documento de Descrição do Desafio para o "
        "tipo específico do proponente.",
        "<b>Habilitação fiscal/social/trabalhista</b>: CNPJ/CPF, certidão conjunta RFB/PGFN, "
        "CRF-FGTS, CNDT, declaração de não emprego de menor.",
        "<b>Qualificação econômico-financeira</b>: certidão negativa de falência/recuperação "
        "judicial, balanço patrimonial com índices LG, LC e SG > 1.",
        "<b>Qualificação técnica</b>: registro no conselho profissional; atestado de "
        "responsabilidade técnica do engenheiro de segurança do trabalho (PGR, grau de "
        "risco mínimo 2, NR-04).",
        "<b>Anexos III-A a III-D</b> preenchidos e assinados: Declaração de Responsabilidade "
        "Social e Ambiental, Declaração de Atendimento às Condições de Contratação, Termo "
        "de Cessão Não Onerosa de Direitos Patrimoniais, Termo de Confidencialidade.",
    ]))
    s.append(Paragraph(
        "Nota sobre propriedade intelectual: apesar do nome \"cessão não onerosa\" no "
        "Anexo III-C, o Documento de Descrição do Desafio (itens 9.4.6 e 17.2.4) esclarece "
        "que a titularidade do código-fonte permanece com a CONTRATADA — a CPRM recebe "
        "apenas licença de uso perpétua e não exclusiva. Leia os dois documentos juntos "
        "antes de assinar.", NOTE))

    s.append(Paragraph("7. Checklist final antes de enviar", H1))
    s.append(bullets([
        "[ ] Todos os arquivos estão em PDF (não .docx, não .zip solto).",
        "[ ] O valor da proposta respeita o teto (R$ 200.000,00 geral / R$ 80.000,00 ME-EPP).",
        "[ ] O vídeo pitch está no ar e o link funciona sem exigir login/senha.",
        "[ ] O Anexo IV-B tem todos os 12 blocos preenchidos (não deixar campo em branco "
        "sem justificativa).",
        "[ ] Foi identificado (ou contratado) o engenheiro de segurança do trabalho exigido.",
        "[ ] A submissão foi feita pela plataforma SOLV, não por e-mail.",
        "[ ] Ficou uma cópia local de tudo o que foi enviado (mesma versão), na pasta "
        "01_inscricao/.",
    ]))

    return s


# ---------------------------------------------------------------------------
# Documento 2 — Escopo técnico e arquitetura
# ---------------------------------------------------------------------------

def story_arquitetura() -> list:
    s = []

    s.append(Paragraph("1. Objetivo da solução", H1))
    s.append(Paragraph(
        "Antes de uma equipe de campo do SGB sair para uma expedição, o sistema gera "
        "automaticamente: (a) uma <b>análise de risco da missão</b>, com base na atividade, "
        "no ambiente e nos dados do PGR (Programa de Gerenciamento de Riscos) da CPRM; "
        "(b) a <b>lista de EPIs obrigatórios</b> para aquela atividade específica; "
        "(c) o mapeamento de <b>pontos de apoio de emergência</b> próximos (hospitais, "
        "delegacias, UBS); e (d) permite ao colaborador consultar tudo isso "
        "<b>offline em campo</b>. Gestores acompanham tudo por um <b>dashboard de "
        "indicadores</b> de conformidade e segurança.", BODY))
    s.append(Paragraph("Fonte: Documento de Descrição do Desafio, item 2.1; Ficha Técnica do Desafio (Anexo II).", SOURCE))

    s.append(Paragraph("2. Arquitetura geral", H1))
    s.append(Paragraph(
        "A solução é dividida em 3 camadas, desacopladas por contratos de API bem "
        "definidos, permitindo evoluir cada uma independentemente. Diferente de um app "
        "nativo separado, o acesso em campo é feito pelo mesmo painel web, responsivo, "
        "funcionando tanto em desktop quanto em celular (ver seção 6 sobre o roadmap de "
        "modo offline):", BODY))
    s.append(bullets([
        "<b>Painel web responsivo (gestão + campo)</b> — React + TypeScript, layout "
        "mobile-first testado em viewport de celular (375px) sem rolagem horizontal. "
        "Usado tanto por gestores de segurança para acompanhar indicadores quanto por "
        "colaboradores em campo para consultar o plano de risco pelo navegador do celular.",
        "<b>API / motor de domínio</b> — PHP + Laravel, contém o motor de análise de "
        "risco, o catálogo de EPIs, a geração de relatórios e a orquestração das "
        "integrações externas.",
        "<b>Camada de dados e integrações</b> — banco relacional (SQLite no protótipo, "
        "MySQL/PostgreSQL em produção) + adapters para sistemas legados (TOTVS, e-Social, "
        "PGR) e APIs públicas (INMET, ANA, Defesa Civil, CNES).",
    ]))

    s.append(Paragraph("3. Stack tecnológica", H1))
    dados_stack = [
        ["Camada", "Tecnologia", "Motivo da escolha"],
        ["Backend / API", "PHP 8.2 + Laravel 12", "Framework maduro, curva de adoção baixa para equipes de manutenção no setor público brasileiro, Eloquent ORM produtivo, geração de rotas/documentação de API simples."],
        ["Banco de dados", "SQLite (protótipo) / MySQL ou PostgreSQL (produção)", "SQLite permite rodar o protótipo sem infraestrutura extra; troca para MySQL/PostgreSQL em produção é só configuração (Laravel abstrai o driver)."],
        ["Painel web (responsivo)", "React 18 + TypeScript + Vite", "Componentização, tipagem, build rápido; CSS com breakpoints mobile-first elimina a necessidade de um app nativo separado nesta fase."],
        ["Geração de relatório", "barryvdh/laravel-dompdf + phpoffice/phpword", "Exportação nativa em .pdf e .docx a partir do backend Laravel, prontos para assinatura digital, sem depender de serviço externo."],
        ["Motor de recomendação", "Regras determinísticas versionadas (PHP) + camada de IA (LLM) para texto explicativo (roadmap)", "Regra determinística é auditável e defensável em segurança do trabalho; IA pura teria risco de alucinação inaceitável nesse domínio."],
        ["Autenticação", "Laravel Sanctum + SSO corporativo (a integrar)", "Compatibilidade com o ambiente de identidade já usado pela CPRM; não implementado nesta fase do protótipo (ver roadmap)."],
        ["Infraestrutura", "Containers (Docker) + orquestração gerenciada (K8s ou similar)", "Escalabilidade horizontal para o pico de 1.200 usuários simultâneos exigido no edital."],
        ["Observabilidade", "Laravel Pail/logs estruturados + métricas (Prometheus/Grafana ou equivalente)", "Necessário para comprovar o indicador contratual de redução de não conformidades."],
    ]
    t = Table(cellify(dados_stack), colWidths=[3.0 * cm, 4.2 * cm, 8.8 * cm])
    t.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1565C0")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
        ("FONTSIZE", (0, 0), (-1, -1), 8.5),
        ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#CCCCCC")),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#F2F6FC")]),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("TOPPADDING", (0, 0), (-1, -1), 5),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
    ]))
    s.append(t)

    s.append(PageBreak())
    s.append(Paragraph("4. Fluxo funcional principal", H1))
    s.append(bullets([
        "1. Gestor ou colaborador cria uma nova missão pelo painel web (funciona igual "
        "em desktop ou no navegador do celular), informando divisão, projeto, atividade, "
        "ambiente e localização.",
        "2. O motor de risco cruza atividade × ambiente × tempo de exposição × alerta "
        "meteorológico (INMET/Defesa Civil) × histórico de acidentes do local, retornando "
        "um nível de risco (1–10), a lista de riscos identificados e os EPIs obrigatórios, "
        "cada um com instruções de uso e manutenção.",
        "3. O sistema busca pontos de apoio (hospital, delegacia, UBS, bombeiros) próximos "
        "às coordenadas informadas.",
        "4. É gerado um relatório de gestão de risco em PDF/DOCX, pronto para assinatura "
        "digital do responsável técnico.",
        "5. O colaborador acessa o plano de risco e a lista de EPIs pelo próprio celular, "
        "no navegador, antes ou durante a expedição (o modo totalmente offline é um item "
        "de roadmap — ver seção 6).",
        "6. Ao final ou durante a missão, quase-acidentes (RQA) podem ser registrados, "
        "alimentando o indicador contratual de não conformidades.",
        "7. O painel consolida indicadores por missão, divisão e período para os "
        "gestores de segurança do trabalho.",
    ]))

    s.append(Paragraph("5. Requisitos do edital mapeados para decisões técnicas", H1))
    dados_req = [
        ["Requisito (Ficha Técnica / Doc. Descrição do Desafio)", "Como é endereçado"],
        ["Compatibilidade com TOTVS, e-Social e PGR", "Camada de serviços isolada (app/Servicos/) no Laravel — cada sistema legado ganha um serviço de integração próprio sem alterar o motor de domínio."],
        ["Interface Web + acesso via celular", "Painel React único, responsivo (mobile-first), validado em viewport de 375px sem rolagem horizontal — dispensa manter um app nativo separado nesta fase."],
        ["Offline-first", "Ainda não implementado no protótipo; roadmap prevê Progressive Web App (PWA) com service worker e cache local como evolução do painel responsivo atual (ver seção 6)."],
        ["APIs abertas p/ Defesa Civil, INMET, ANA", "Rotas REST em routes/api.php, documentáveis via Laravel + OpenAPI; serviços dedicados por integração em app/Servicos/."],
        ["Criptografia ponta a ponta / LGPD", "TLS em trânsito, criptografia em repouso no banco, minimização de dados pessoais, política de retenção e anonimização, DPO do parceiro tecnológico como ponto de contato."],
        ["Suporte a 1.200 usuários simultâneos", "Backend Laravel stateless horizontalmente escalável (containers) + banco com réplicas de leitura; testes de carga previstos no plano de trabalho."],
        ["Exportação .docx/.pdf prontos p/ assinatura", "Geração nativa via laravel-dompdf/phpword, com campo de assinatura do responsável técnico."],
        ["Indicador: % planejamento de risco concluído", "Calculado em tempo real a partir da tabela `missoes` (missões com plano concluído / total programado)."],
        ["Indicador: nº de não conformidades (RQA)", "Registro de RQA vinculado à missão (`relatorios_quase_acidente`); comparação automática antes/depois do CPSI."],
    ]
    t2 = Table(cellify(dados_req), colWidths=[6.8 * cm, 9.2 * cm])
    t2.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1565C0")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
        ("FONTSIZE", (0, 0), (-1, -1), 8.3),
        ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#CCCCCC")),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#F2F6FC")]),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("TOPPADDING", (0, 0), (-1, -1), 5),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
    ]))
    s.append(t2)

    s.append(PageBreak())
    s.append(Paragraph("6. Nível de maturidade (TRL) e o que já existe como protótipo", H1))
    s.append(Paragraph(
        "O edital pontua explicitamente o \"nível de maturidade\" da solução, considerando "
        "soluções entre TRL 5 e TRL 8 (Ficha Técnica, item 4). O repositório deste projeto "
        "(pasta <b>solucao/</b>) já contém um protótipo funcional (não apenas mockup "
        "estático) que implementa:", BODY))
    s.append(bullets([
        "Motor de análise de risco determinístico e auditável (matriz atividade × ambiente) "
        "em PHP, testado com 6 testes automatizados (PHPUnit, unitários e de API).",
        "API REST completa em Laravel (rotas em routes/api.php).",
        "Geração real de relatório em PDF (laravel-dompdf) e DOCX (phpword).",
        "Painel web funcional (React) consumindo a API, com formulário de nova missão, "
        "indicadores em tempo real e layout responsivo validado em viewport mobile.",
        "Persistência em banco de dados relacional (SQLite no protótipo, trocável por "
        "MySQL/PostgreSQL via configuração do Laravel, sem mudança de código de domínio).",
    ]))
    s.append(Paragraph(
        "Ainda como placeholder/mock, documentado explicitamente no código para não gerar "
        "falsa impressão de prontidão: pontos de apoio (dados fixos em vez de consulta a "
        "bases públicas reais), leitura do PGR real da CPRM (depende de acesso a dados que "
        "a CPRM ainda não forneceu), integrações reais com TOTVS/e-Social/INMET/ANA/Defesa "
        "Civil, autenticação/SSO, e o modo totalmente offline (hoje o painel é responsivo e "
        "acessível pelo celular via navegador, mas ainda depende de conexão).", NOTE))

    s.append(Paragraph("7. Roadmap de evolução (fase de teste do CPSI)", H1))
    dados_roadmap = [
        ["Mês", "Entrega"],
        ["1", "Alinhamento com a CPRM; acesso a amostra real (anonimizada) do PGR; ajuste fino da matriz de risco com o engenheiro de segurança."],
        ["2", "Integração real com pelo menos 1 fonte de dados de pontos de apoio (CNES/DATASUS); autenticação/SSO no painel."],
        ["3", "Evolução do painel responsivo para Progressive Web App (PWA) com cache local/offline; testes de campo piloto com 1 divisão (conforme item 13.2.2 do Doc. de Descrição do Desafio)."],
        ["4", "Integração com INMET/Defesa Civil para alertas meteorológicos; dashboard de indicadores consolidado."],
        ["5", "Testes de carga (1.200 usuários simultâneos); hardening de segurança e LGPD; testes com múltiplas divisões."],
        ["6", "Consolidação dos 200 testes previstos no edital (item 13.2.5 do Doc. de Descrição do Desafio); relatório final."],
    ]
    t3 = Table(cellify(dados_roadmap), colWidths=[1.8 * cm, 14.2 * cm])
    t3.setStyle(TableStyle([
        ("BACKGROUND", (0, 0), (-1, 0), colors.HexColor("#1565C0")),
        ("TEXTCOLOR", (0, 0), (-1, 0), colors.white),
        ("FONTSIZE", (0, 0), (-1, -1), 9),
        ("GRID", (0, 0), (-1, -1), 0.5, colors.HexColor("#CCCCCC")),
        ("ROWBACKGROUNDS", (0, 1), (-1, -1), [colors.white, colors.HexColor("#F2F6FC")]),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
        ("TOPPADDING", (0, 0), (-1, -1), 5),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 5),
    ]))
    s.append(t3)
    s.append(Paragraph(
        "O roadmap deve ser ajustado ao Plano de Trabalho final (Anexo IV-B) apresentado "
        "na proposta, respeitando o cronograma físico-financeiro negociado com a CPRM.", SOURCE))

    s.append(Paragraph("8. Propriedade intelectual e sigilo", H1))
    s.append(Paragraph(
        "Conforme o Documento de Descrição do Desafio (itens 9.4.6 e 17.2.4), a "
        "titularidade do código-fonte e da propriedade intelectual da solução permanece "
        "com a empresa CONTRATADA. A CPRM recebe apenas licença de uso perpétua, "
        "irrevogável, não exclusiva e gratuita para uso interno. É vedada a subcontratação "
        "(item 17.3) e é exigido sigilo sobre dados da CPRM (Anexo III-D).", BODY))

    return s


if __name__ == "__main__":
    build_doc(
        "01_guia_inscricao_passo_a_passo.pdf",
        "Guia Passo a Passo — Inscrição no Edital CPSI nº 0001/2026",
        "CPRM/SGB · Desafio: Segurança nas Saídas para Campo com Definição e Monitoramento de EPIs",
        story_guia,
    )
    build_doc(
        "02_escopo_tecnico_arquitetura.pdf",
        "Escopo Técnico e Arquitetura da Solução",
        "Edital CPSI nº 0001/2026 (CPRM/SGB) · Segurança nas Saídas para Campo com Definição e Monitoramento de EPIs",
        story_arquitetura,
    )
