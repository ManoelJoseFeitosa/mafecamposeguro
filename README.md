# CPSI 2026 — Proposta para o Edital nº 0001-CPSI/2026 (CPRM/SGB)

Projeto de resposta ao edital "Segurança nas Saídas para Campo com Definição e
Monitoramento de EPIs" (Lei Complementar nº 182/2021 — CPSI).

Stack: **PHP/Laravel** (backend) + **React responsivo** (painel web, gestão,
funciona em desktop e no navegador do celular) + **React Native/Expo**
(app móvel offline-first, para uso em campo sem internet). Todo o código do
projeto é nomeado em português — ver `CLAUDE.md`, seção 3.1, para as poucas
exceções obrigatórias (nomes de arquivo que são contratos do
Composer/npm/Vite/Expo/Laravel).

## Comece por aqui

1. **`CLAUDE.md`** — memória do projeto: fatos do edital, prazos, requisitos técnicos,
   decisões de arquitetura e estado atual. Leia antes de qualquer coisa.
2. **`PROMPT_MESTRE.md`** — copie e cole no início de uma nova sessão de IA para
   retomar o trabalho com segurança (evita alucinação e retrabalho).

## Estrutura

```
CPSI2026/
├── CLAUDE.md                     <- memória / fonte de verdade
├── PROMPT_MESTRE.md              <- prompt de retomada
├── documentacao/
│   ├── fontes/                   <- PDFs originais do edital (não editar)
│   ├── gerar_pdfs.py             <- script que gera os 2 PDFs abaixo
│   ├── 01_guia_inscricao_passo_a_passo.pdf
│   └── 02_escopo_tecnico_arquitetura.pdf
└── solucao/
    ├── servidor/                  <- API Laravel/PHP (protótipo funcional, testado)
    ├── painel-web/                <- Painel React responsivo (protótipo funcional, testado)
    └── app-movel/                 <- App React Native/Expo offline-first (protótipo funcional, testado)
```

## Como rodar

```bash
# Backend
cd solucao/servidor
composer install
php artisan migrate
php artisan serve            # http://127.0.0.1:8000

# Painel web (outro terminal)
cd solucao/painel-web
npm install
npm run dev                   # http://localhost:5173

# App móvel offline-first (outro terminal)
cd solucao/app-movel
npm install
npx expo start                 # QR code (Expo Go) ou emulador Android/iOS
```

Ver `solucao/servidor/README.md`, `solucao/painel-web/README.md` e
`solucao/app-movel/README.md` para detalhes.

## O que já está pronto

- Protótipo funcional de ponta a ponta (não é só mockup): motor de análise de risco
  em PHP (6 testes PHPUnit passando), recomendação de EPIs, painel responsivo,
  exportação de relatório em PDF/DOCX real.
- Responsividade mobile validada (viewport de celular, sem rolagem horizontal).
- **App móvel offline-first** (React Native/Expo): missões de campo são
  gravadas em SQLite local e sincronizadas com o Laravel quando a conexão
  volta (4 testes Jest passando) — atende ao requisito de operação sem
  internet do edital (item 8 do Documento de Descrição do Desafio).
- Guia passo a passo de inscrição no edital
  (`documentacao/01_guia_inscricao_passo_a_passo.pdf`).
- Documento de escopo técnico e arquitetura
  (`documentacao/02_escopo_tecnico_arquitetura.pdf`) — **desatualizado quanto
  ao app móvel**: foi gerado antes da adição de `solucao/app-movel` e ainda
  descreve só Laravel + React; regenerar com `documentacao/gerar_pdfs.py`
  se for preciso um PDF atualizado (não fizemos isso automaticamente nesta
  sessão para não arriscar alterar texto do escopo sem revisão).

## O que falta (depende de você, não pode ser inventado pela IA)

- Dados reais da empresa/proponente para preencher os Anexos III e IV (CNPJ, equipe,
  valores, dados bancários).
- Gravação do vídeo Pitch (até 5 minutos).
- Engenheiro de segurança do trabalho com o perfil exigido (item 12.5.3 do Documento
  de Descrição do Desafio) para assinar como responsável técnico.

Ver seção 7 do `CLAUDE.md` para a lista completa de perguntas em aberto.
