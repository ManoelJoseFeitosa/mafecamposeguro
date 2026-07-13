# Servidor — API Laravel do protótipo CPSI 2026

API em Laravel 12 (PHP) que implementa o núcleo funcional exigido pela Ficha
Técnica do Desafio (Anexo II do edital): análise de risco de missão de campo,
recomendação automatizada de EPIs, localização de pontos de apoio, exportação
de relatório em `.pdf`/`.docx` e indicadores de dashboard.

## Convenção de nomenclatura deste projeto

Todo o código de domínio (nossas classes, tabelas, rotas, comentários) está em
**português**. As únicas exceções são nomes de pastas/arquivos que são
**contratos obrigatórios do próprio framework/ferramenta** — renomeá-los
quebraria o Laravel, o Composer ou o Artisan CLI:

- Pastas de esqueleto do Laravel: `app/`, `bootstrap/`, `config/`, `database/`,
  `public/`, `resources/`, `routes/`, `storage/`, `tests/`, `vendor/`.
- Arquivos de config do Laravel (`config/app.php`, `config/database.php` etc.)
  — o framework lê esses nomes de arquivo internamente, hardcoded.
- `artisan`, `composer.json`, `composer.lock`, `package.json`,
  `phpunit.xml`, `.env`, `.env.example`, `vite.config.js`.

Dentro dessas pastas, tudo que é nosso é português: `app/Modelos/` (não
`Models`), `app/Http/Controladores/` (não `Controllers`), `app/Servicos/`
(nossa camada de domínio), migrations com nomes e colunas em português,
`tests/Unitario/` e `tests/Funcionalidade/` (não `Unit`/`Feature`), rotas em
`routes/api.php` com paths e nomes em português.

Ver `CLAUDE.md` na raiz do projeto para a explicação completa dessa decisão.

## Rodar localmente

```bash
cd solucao/servidor
composer install
cp .env.example .env    # se ainda não existir
php artisan key:generate
touch database/database.sqlite   # se ainda não existir
php artisan migrate
php artisan serve   # http://127.0.0.1:8000
```

Se a porta 8000 já estiver em uso, rode `php artisan serve --port 8010` (ou
outra porta livre) e ajuste `server.proxy` em `solucao/painel-web/vite.config.ts`.

## Testes

```bash
php artisan test
```

## Autenticação (Sanctum)

O painel web (uso do gestor) exige login desde 2026-07-13. Login gera um
token via `laravel/sanctum` (`POST /api/login`, retorna `token` Bearer).

Usuário de demonstração criado por `php artisan db:seed`
(`database/seeders/DatabaseSeeder.php`): `gestor@cpsi2026.local` /
`cpsi2026-demo` — **credencial só para o protótipo local, trocar antes de
qualquer uso real.**

Decisão de escopo: `GET /api/indicadores`, `POST /api/logout` e
`GET /api/usuario-autenticado` exigem `auth:sanctum`. **`POST /api/missoes`,
`GET /api/missoes`, `POST /api/rqa` e `/api/catalogo/*` continuam públicas de
propósito** — são o caminho usado pelo app móvel offline-first
(`solucao/app-movel`) para criar/sincronizar missões em campo, onde não há
sessão de gestor (colaborador de campo não faz login). Proteger essas rotas
quebraria a sincronização offline-first já testada. Ver `routes/api.php`.

## Estrutura de domínio

- `app/Modelos/Missao.php`, `app/Modelos/RelatorioQuaseAcidente.php` — Eloquent.
- `app/Servicos/CatalogoServico.php` — dados de referência (divisões, EPIs, matriz de risco).
- `app/Servicos/MotorDeRiscoServico.php` — motor determinístico de análise de risco.
- `app/Servicos/PontosDeApoioServico.php` — localização de pontos de apoio (mock).
- `app/Servicos/RelatorioServico.php` — geração de PDF/DOCX.
- `app/Http/Controladores/` — controladores REST.
- `routes/api.php` — rotas da API.

## O que é real neste protótipo vs. o que é placeholder

- **Real e funcional:** motor de análise de risco (regras auditáveis, testes
  automatizados passando), API REST completa, persistência em banco (SQLite,
  trocável por MySQL/PostgreSQL via `.env`), geração de relatório `.pdf`/`.docx`
  real, cálculo dos indicadores contratuais + histórico de alertas/RQAs,
  autenticação do painel do gestor via Sanctum (login/logout/token).
- **Mock/placeholder, documentado como tal no código:** pontos de apoio (dados
  fixos em vez de consulta real ao CNES/SSP), leitura do PGR real (depende de
  acesso a dados que a CPRM ainda não forneceu), integrações
  TOTVS/e-Social/INMET/ANA/Defesa Civil. Autenticação cobre só o painel web —
  não há SSO corporativo nem cadastro de usuário via interface (usuário
  criado por seeder/tinker).
- Ver `documentacao/02_escopo_tecnico_arquitetura.pdf` para o roadmap completo.
